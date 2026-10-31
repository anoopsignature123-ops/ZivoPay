<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\IpaymentWalletService;
use App\Services\UpigatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepositController extends Controller
{
    /**
     * Display User Add Fund / Deposit Portal & Recent History.
     */
    public function index(Request $request, UpigatewayService $upigateway, IpaymentWalletService $ipaymentWallet): View
    {
        $user = Auth::user();

        $query = Deposit::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('deposit_ref', 'like', "%{$search}%")
                    ->orWhere('trx_hash', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $deposits = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total_approved' => (float) Deposit::where('user_id', $user->id)->where('status', 'approved')->sum('final_amount'),
            'total_pending' => (float) Deposit::where('user_id', $user->id)->where('status', 'pending')->sum('final_amount'),
            'total_rejected' => (float) Deposit::where('user_id', $user->id)->where('status', 'rejected')->sum('final_amount'),
            'total_count' => Deposit::where('user_id', $user->id)->count(),
        ];

        $conversionRate = (float) config('gateway.usd_to_inr_rate', 100);
        $gatewayEnabled = $upigateway->isConfigured();
        $usdGatewayEnabled = (bool) config('gateway.ipaymentwallet.enabled') && $ipaymentWallet->isConfigured();
        $minDeposit = config('gateway.min_deposit_inr', 100.00);
        $maxDeposit = config('gateway.max_deposit_inr', 500000.00);
        $depositRef = (string) $request->query('deposit_ref', '');
        $depositReturnUrl = null;
        if ($depositRef !== '' && Deposit::query()->where('deposit_ref', $depositRef)->where('user_id', $user->id)->where('payment_provider', 'upigateway')->exists()) {
            $depositReturnUrl = route('user.deposit.return', [
                'deposit_ref' => $depositRef,
            ]);
        }

        return view('user.deposit.index', compact('user', 'deposits', 'stats', 'conversionRate', 'gatewayEnabled', 'usdGatewayEnabled', 'minDeposit', 'maxDeposit', 'depositReturnUrl'));
    }

    /**
     * Store new User Add Fund / Deposit request.
     */
    public function store(Request $request, UpigatewayService $upigateway, IpaymentWalletService $ipaymentWallet): RedirectResponse
    {
        $minDeposit = config('gateway.min_deposit_inr', 100.00);
        $maxDeposit = config('gateway.max_deposit_inr', 500000.00);

        $validated = $request->validate([
            'currency' => ['required', 'in:INR,USD'],
            'amount' => ['required', 'numeric', 'decimal:0,2'],
        ]);

        if ($validated['currency'] === 'USD') {
            if (! (bool) config('gateway.ipaymentwallet.enabled') || ! $ipaymentWallet->isConfigured()) {
                return back()->withErrors(['currency' => 'iPaymentWallet is not enabled or configured for the selected mode.'])->withInput();
            }

            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'decimal:0,2', 'min:1', 'max:50000'],
            ], [
                'amount.min' => 'Minimum USD deposit is $1.00.',
                'amount.max' => 'Maximum USD deposit is $50,000.00.',
            ]);

            $amountUsd = (float) $validated['amount'];
            $exchangeRate = (float) config('gateway.usd_to_inr_rate', 100);
            $walletCreditInr = round($amountUsd * $exchangeRate, 2);
            $deposit = Deposit::create([
                'user_id' => Auth::id(),
                'deposit_ref' => 'DEP-'.Str::upper(Str::random(12)),
                'amount' => $amountUsd,
                'charge' => 0,
                'final_amount' => $walletCreditInr,
                'amount_inr' => $walletCreditInr,
                'conversion_rate' => $exchangeRate,
                'payment_method' => 'iPaymentWallet USD',
                'payment_provider' => 'ipaymentwallet',
                'provider_mode' => 'single',
                'status' => 'pending',
                'admin_remark' => 'Waiting for iPaymentWallet payment confirmation.',
            ]);

            try {
                $payment = $ipaymentWallet->createPayment($deposit->load('user'));
                $deposit->refresh();

                return redirect()->route('user.deposit.payment', $deposit)
                    ->with('info', 'Payment request created. Send the exact USD amount shown on checkout.');
            } catch (\Throwable $exception) {
                report($exception);
                $deposit->forceFill([
                    'status' => 'rejected',
                    'admin_remark' => 'iPaymentWallet could not create the payment request.',
                ])->save();

                return back()->with('error', $exception instanceof \RuntimeException ? $exception->getMessage() : 'Could not start iPaymentWallet payment. Please try again.')->withInput();
            }
        }

        if (! $upigateway->isConfigured()) {
            return back()->with('error', 'UPIGateway is not configured yet. Please contact support.');
        }

        $user = Auth::user();
        $mobile = preg_replace('/\D+/', '', (string) $user->mobile);
        if (is_string($mobile) && strlen($mobile) === 12 && str_starts_with($mobile, '91')) {
            $mobile = substr($mobile, 2);
        }
        if (! is_string($mobile) || ! preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
            return back()->with('error', 'Add a valid 10-digit mobile number to your profile before UPI payment.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'decimal:0,2', "min:{$minDeposit}", "max:{$maxDeposit}"],
        ], [
            'amount.min' => 'Minimum INR deposit is ₹'.number_format($minDeposit, 2).'.',
            'amount.max' => 'Maximum INR deposit is ₹'.number_format($maxDeposit, 2).'.',
        ]);

        $amountInr = (float) $validated['amount'];
        $exchangeRate = (float) config('gateway.usd_to_inr_rate', 100);
        $deposit = Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-'.Str::upper(Str::random(12)),
            'amount' => $amountInr,
            'charge' => 0,
            'final_amount' => $amountInr,
            'amount_inr' => $amountInr,
            'conversion_rate' => $exchangeRate,
            'payment_method' => 'UPIGateway UPI (INR)',
            'payment_provider' => 'upigateway',
            'provider_mode' => 'single',
            'status' => 'pending',
            'admin_remark' => 'Waiting for UPIGateway INR payment confirmation.',
        ]);

        try {
            return redirect()->to($upigateway->createOrder($deposit->load('user'), $mobile));
        } catch (\Throwable $exception) {
            report($exception);
            $deposit->forceFill([
                'status' => 'rejected',
                'admin_remark' => 'UPIGateway could not create the checkout session.',
            ])->save();

            return back()->with('error', $exception instanceof \RuntimeException ? $exception->getMessage() : 'Could not start UPI payment. Please try again.');
        }
    }

    public function returnFromPayment(Request $request, UpigatewayService $upigateway): RedirectResponse|View
    {
        $depositRef = $request->input('deposit_ref', $request->input('client_txn_id'));
        validator(['deposit_ref' => $depositRef], ['deposit_ref' => ['required', 'string', 'max:255']])->validate();
        $deposit = Deposit::query()->where('deposit_ref', $depositRef)->first();
        if (! $deposit || $deposit->payment_provider !== 'upigateway') {
            Log::warning('UPIGateway returned to an unknown deposit reference.', [
                'deposit_ref' => $depositRef,
                'payment_provider' => $deposit?->payment_provider,
            ]);

            $message = 'We could not match this payment return to a UPI deposit. Check your deposit history or contact support before paying again.';

            return Auth::check()
                ? redirect()->route('user.dashboard')->with('error', $message)
                : redirect()->route('user.login')->with('error', $message);
        }

        $upigateway->verifyAndCredit($deposit->deposit_ref);
        $deposit->refresh();
        $isDepositOwner = Auth::check() && (int) Auth::id() === (int) $deposit->user_id;

        if ($deposit->status === 'rejected') {
            $message = 'UPI payment was not completed. No money was added to your Fund Wallet.';

            return $isDepositOwner
                ? redirect()->route('user.deposit.index')->with('error', $message)
                : redirect()->route('user.login')->with('error', $message);
        }

        return view('user.deposit.returning', [
            'deposit' => $deposit,
            'statusUrl' => URL::temporarySignedRoute(
                'app.deposit.upi.status',
                now()->addMinutes(15),
                ['deposit' => $deposit->id],
            ),
            'successUrl' => $isDepositOwner
                ? route('user.dashboard')
                : route('user.login'),
        ]);
    }

    public function payment(Deposit $deposit, IpaymentWalletService $ipaymentWallet): View|RedirectResponse
    {
        abort_unless($deposit->user_id === Auth::id(), 403);
        abort_unless($deposit->payment_provider === 'ipaymentwallet', 404);

        if ($deposit->status === 'approved') {
            return redirect()->route('user.deposit.index')->with('success', 'Payment already verified and credited.');
        }

        $ipaymentWallet->verifyAndCredit($deposit->deposit_ref);
        $deposit->refresh();
        if ($deposit->status === 'approved') {
            return redirect()->route('user.deposit.index')->with('success', 'USD payment verified and credited to your wallet.');
        }

        return view('user.deposit.ipayment', [
            'deposit' => $deposit,
            'qrPageUrl' => data_get($deposit->payment_metadata, 'qr_url'),
            'qrImage' => data_get($deposit->payment_metadata, 'qr_image')
                ?: ($deposit->gateway_reference ? 'https://ipaymentwallet.com/api/payment/qr/'.rawurlencode($deposit->gateway_reference) : null),
            'paymentUrl' => data_get($deposit->payment_metadata, 'payment_url'),
            'paymentAddress' => data_get($deposit->payment_metadata, 'payment_address'),
            'paymentNetwork' => data_get($deposit->payment_metadata, 'network', 'BEP20 (BSC)'),
        ]);
    }

    public function upiPayment(Deposit $deposit, UpigatewayService $upigateway): View|RedirectResponse
    {
        abort_unless($deposit->user_id === Auth::id(), 403);
        abort_unless($deposit->payment_provider === 'upigateway', 404);

        if ($deposit->status === 'approved') {
            return redirect()->route('user.deposit.index')->with('success', 'UPI payment verified and credited to your wallet.');
        }

        $upigateway->verifyAndCredit($deposit->deposit_ref);
        $deposit->refresh();

        return view('user.deposit.upi', [
            'deposit' => $deposit,
            'checkoutUrl' => data_get($deposit->payment_metadata, 'checkout_url'),
            'qrUrl' => data_get($deposit->payment_metadata, 'qr_url'),
        ]);
    }

    public function appUpiPayment(Deposit $deposit, UpigatewayService $upigateway): View|RedirectResponse
    {
        abort_unless($deposit->payment_provider === 'upigateway', 404);
        if ($deposit->status === 'pending') {
            $upigateway->verifyAndCredit($deposit->deposit_ref);
            $deposit->refresh();
        }

        return view('user.deposit.app-upi', [
            'deposit' => $deposit,
            'checkoutUrl' => data_get($deposit->payment_metadata, 'checkout_url'),
            'qrUrl' => data_get($deposit->payment_metadata, 'qr_url'),
            'statusUrl' => URL::temporarySignedRoute(
                'app.deposit.upi.status',
                now()->addHour(),
                ['deposit' => $deposit->id],
            ),
        ]);
    }

    public function appUpiReturn(Request $request, Deposit $deposit, UpigatewayService $upigateway): View
    {
        $hasValidSignature = URL::hasValidSignature(
            $request,
            true,
            static fn (string $parameter): bool => $parameter !== 'expires',
        );

        abort_unless($hasValidSignature, 403);
        abort_unless($deposit->payment_provider === 'upigateway', 404);
        if ($deposit->status === 'pending') {
            $upigateway->verifyAndCredit($deposit->deposit_ref);
            $deposit->refresh();
        }

        return view('user.deposit.returning', [
            'deposit' => $deposit,
            'statusUrl' => URL::temporarySignedRoute(
                'app.deposit.upi.status',
                now()->addMinutes(15),
                ['deposit' => $deposit->id],
            ),
            'successUrl' => null,
            'appReturn' => true,
        ]);
    }

    public function appUpiStatus(Deposit $deposit, UpigatewayService $upigateway): JsonResponse
    {
        abort_unless($deposit->payment_provider === 'upigateway', 404);
        if ($deposit->status === 'pending') {
            $upigateway->verifyAndCredit($deposit->deposit_ref);
            $deposit->refresh();
        }

        return response()->json([
            'deposit_id' => $deposit->id,
            'deposit_ref' => $deposit->deposit_ref,
            'status' => $deposit->status,
            'amount_inr' => (float) $deposit->amount_inr,
            'wallet_credit' => (float) $deposit->final_amount,
        ]);
    }

    public function ipaymentStatus(Deposit $deposit, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        abort_unless($deposit->user_id === Auth::id(), 403);
        abort_unless($deposit->payment_provider === 'ipaymentwallet', 404);

        if ($deposit->status === 'pending') {
            $ipaymentWallet->verifyAndCredit($deposit->deposit_ref);
            $deposit->refresh();
        }

        return response()->json(['status' => $deposit->status, 'deposit_ref' => $deposit->deposit_ref]);
    }

    public function status(Request $request, UpigatewayService $upigateway): JsonResponse
    {
        $validated = $request->validate(['deposit_ref' => ['required', 'string', 'max:255']]);
        $deposit = Deposit::query()->where('deposit_ref', $validated['deposit_ref'])
            ->where('user_id', Auth::id())->where('payment_provider', 'upigateway')->firstOrFail();
        if ($deposit->status === 'pending') {
            $upigateway->verifyAndCredit($deposit->deposit_ref);
            $deposit->refresh();
        }

        return response()->json(['status' => $deposit->status, 'deposit_ref' => $deposit->deposit_ref]);
    }
}
