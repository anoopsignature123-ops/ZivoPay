<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Shop\OrderResource;
use App\Http\Resources\Api\Shop\PaymentResource;
use App\Models\PaymentGatewaySetting;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Services\Shop\ShopOrderService;
use App\Services\Shop\ShopRazorpayService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use ApiResponse;

    public function checkout(Request $request, ShopOrderService $shopOrders, ShopRazorpayService $razorpay): JsonResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', 'exists:shop_addresses,id'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['payment_method'] = 'razorpay';
        if ($data['payment_method'] === 'razorpay' && ! PaymentGatewaySetting::razorpay()->isReady()) {
            return $this->errorResponse('Shop online payment is currently unavailable. Choose Cash on Delivery or try later.', 503);
        }
        $order = $shopOrders->placeOrder($request->user(), $data);
        $payment = $order->payments()->firstOrFail();
        $paymentData = null;
        if ($payment->provider === 'razorpay') {
            $settings = PaymentGatewaySetting::razorpay();
            try {
                $gatewayOrder = $razorpay->startCheckout($payment->load('order'));
                $paymentData = [
                    'provider' => 'razorpay',
                    'key_id' => $settings->keyIdFor((string) $payment->fresh()->mode),
                    'mode' => $payment->fresh()->mode,
                    'order_id' => $gatewayOrder['id'],
                    'amount' => (int) $gatewayOrder['amount'],
                    'currency' => $gatewayOrder['currency'],
                    'payment_id' => $payment->id,
                    'prefill' => [
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'contact' => $request->user()->mobile,
                    ],
                ];
            } catch (\Throwable $exception) {
                report($exception);

                return $this->errorResponse('Could not start Shop payment. Please try checkout again.', 502);
            }
        }

        return $this->successResponse([
            'order' => OrderResource::make($order->fresh()->load('items'))->resolve(),
            'payment' => $paymentData ?? PaymentResource::make($payment->fresh()->load('order'))->resolve(),
        ], 'Shop checkout started.', 201);
    }

    public function verifyPayment(Request $request, ShopRazorpayService $razorpay): JsonResponse
    {
        $data = $request->validate([
            'payment_id' => ['required', 'integer'],
            'razorpay_order_id' => ['required', 'string', 'max:255'],
            'razorpay_payment_id' => ['required', 'string', 'max:255'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);
        $payment = Payment::query()->whereKey($data['payment_id'])
            ->where('user_id', $request->user()->id)->where('provider', 'razorpay')->firstOrFail();
        if (! $razorpay->verifyCheckout(
            $payment, $data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature']
        )) {
            return $this->errorResponse('Shop payment verification failed. Check payment status before retrying.', 422);
        }

        return $this->successResponse(PaymentResource::make($payment->fresh()->load('order'))->resolve(), 'Shop payment verified.');
    }

    public function retryPayment(Request $request, Order $order, ShopRazorpayService $razorpay): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        if ($order->payment_method !== 'razorpay' || $order->status !== 'pending_payment'
            || $order->payment_status !== 'pending') {
            return $this->errorResponse('This Shop order is not waiting for a Razorpay payment.', 422);
        }

        $payment = $order->payments()->where('provider', 'razorpay')->where('status', 'pending')->latest()->firstOrFail();
        $settings = PaymentGatewaySetting::razorpay();
        if (! $settings->isReady()) {
            return $this->errorResponse('Shop online payment is currently unavailable.', 503);
        }

        try {
            $gatewayOrder = $payment->payment_order_id
                ? ['id' => $payment->payment_order_id, 'amount' => (int) round((float) $payment->amount * 100), 'currency' => $payment->currency]
                : $razorpay->startCheckout($payment->load('order'));
        } catch (\Throwable $exception) {
            report($exception);

            return $this->errorResponse('Could not restart Shop payment. Please try again.', 502);
        }

        $payment->refresh();

        return $this->successResponse([
            'payment_id' => $payment->id,
            'provider' => 'razorpay',
            'key_id' => $settings->keyIdFor((string) $payment->mode),
            'mode' => $payment->mode,
            'order_id' => $gatewayOrder['id'],
            'amount' => (int) $gatewayOrder['amount'],
            'currency' => $gatewayOrder['currency'],
            'prefill' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'contact' => $request->user()->mobile,
            ],
        ], 'Shop payment ready to retry.');
    }

    public function paymentHistory(Request $request): JsonResponse
    {
        $payments = Payment::query()->where('user_id', $request->user()->id)->with('order')->latest()->paginate(20);

        return $this->successResponse([
            'items' => PaymentResource::collection($payments->getCollection())->resolve(),
            'pagination' => ['current_page' => $payments->currentPage(), 'last_page' => $payments->lastPage(), 'total' => $payments->total()],
        ], 'Shop payment history retrieved.');
    }

    public function paymentStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_id' => ['required', 'integer', 'min:1'],
        ]);
        $payment = Payment::query()
            ->where('user_id', $request->user()->id)
            ->with('order')
            ->find($data['payment_id']);

        if (! $payment) {
            return $this->errorResponse('Shop payment not found. Use the payment_id returned by checkout.', 404);
        }

        return $this->successResponse(PaymentResource::make($payment->load('order'))->resolve(), 'Shop payment status retrieved.');
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()->where('user_id', $request->user()->id)->with('items')->latest()->paginate(20);

        return $this->successResponse([
            'items' => OrderResource::collection($orders->getCollection())->resolve(),
            'pagination' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage(), 'total' => $orders->total()],
        ], 'Shop order history retrieved.');
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return $this->successResponse(OrderResource::make($order->load('items'))->resolve(), 'Shop order retrieved.');
    }

    public function cancel(Request $request, ShopOrderService $shopOrders): JsonResponse
    {
        $order = Order::query()->whereKey($request->input('order_id'))->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        if (! in_array($order->status, ['pending_payment', 'placed'], true)) {
            throw ValidationException::withMessages(['order' => 'Only newly placed orders can be cancelled in the app. Contact support for help with this order.']);
        }

        $cancelled = $shopOrders->updateStatus($order, 'cancelled', $order->tracking_number, $order->carrier, 'Cancelled by customer.');

        return $this->successResponse(OrderResource::make($cancelled)->resolve(), 'Order cancelled.');
    }

    public function requestReturn(Request $request): JsonResponse
    {
        $order = Order::query()->whereKey($request->input('order_id'))->firstOrFail();
        abort_unless($order->user_id === $request->user()->id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        $order = DB::transaction(function () use ($request, $order, $data): Order {
            $lockedOrder = Order::query()->where('user_id', $request->user()->id)->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status !== 'delivered' || ! $lockedOrder->delivered_at
                || $lockedOrder->delivered_at->addDays((int) config('shop.return_window_days'))->isPast()) {
                throw ValidationException::withMessages(['order' => 'This order is outside the eligible return period.']);
            }
            $lockedOrder->update([
                'status' => 'return_requested',
                'return_reason' => $data['reason'],
                'return_requested_at' => now(),
            ]);

            return $lockedOrder;
        }, 3);

        return $this->successResponse(OrderResource::make($order->fresh()->load('items'))->resolve(), 'Return request submitted for review.');
    }
}
