<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request): View
    {
        $sponsor = $request->query('sponsor', null);

        // If user is logged in and no explicit sponsor is passed, default sponsor to logged-in user's referral code
        if (Auth::check() && empty($sponsor)) {
            $sponsor = Auth::user()->referral_code;
        }

        $isLockedSponsor = $request->has('sponsor') || Auth::check();

        return view('user.auth.register', compact('sponsor', 'isLockedSponsor'));
    }

    /**
     * Live AJAX lookup for sponsor code verification.
     */
    public function checkSponsor(Request $request): JsonResponse
    {
        $code = trim($request->query('code', ''));

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid sponsor code.',
            ]);
        }

        $sponsorUser = User::where('referral_code', $code)->first();

        if ($sponsorUser) {
            return response()->json([
                'success' => true,
                'name' => $sponsorUser->name,
                'email' => $sponsorUser->email,
                'referral_code' => $sponsorUser->referral_code,
            ]);
        }

        // System default admin fallback code
        if (in_array($code, ['ZIVO-0000001', 'DEX-0000001'])) {
            return response()->json([
                'success' => true,
                'name' => 'Zivo Pay System Admin',
                'email' => 'admin@zivopay.net',
                'referral_code' => $code,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid Sponsor Code! Member not found in system.',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'sponsor_id' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'sponsor_id.required' => 'Please enter a valid Sponsor Code.',
            'name.required' => 'Please enter member full name.',
            'email.required' => 'Please enter email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered in the system.',
            'mobile.required' => 'Please enter mobile phone number.',
            'mobile.unique' => 'This mobile number is already registered in the system.',
            'password.required' => 'Please enter account password.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $sponsorCode = trim($request->sponsor_id);
        $sponsorUser = User::where('referral_code', $sponsorCode)->first();
        $sponsorName = $sponsorUser ? $sponsorUser->name : 'ZIVO PAY Official';

        if (! $sponsorUser && ! in_array($sponsorCode, ['ZIVO-0000001', 'DEX-0000001'])) {
            return redirect()->back()->withInput()->withErrors(['sponsor_id' => 'Invalid Sponsor Code! Member not found in system.']);
        }

        $referralCode = User::generateReferralCode();
        $txPin = (string) rand(100000, 999999);

        // New member account created as inactive by default until package investment
        $user = User::create([
            'role_id' => 2,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'referral_code' => $referralCode,
            'sponsor_code' => $sponsorCode,
            'position' => 'direct',
            'status' => 'inactive',
            'password' => Hash::make($request->password),
        ]);

        // If guest (not logged in), automatically log in as the newly registered user
        if (! Auth::check()) {
            Auth::login($user);
        }

        $registeredUser = [
            'user_id' => $user->referral_code,
            'sponsor_id' => $user->sponsor_code,
            'sponsor_name' => $sponsorName,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'password' => $request->password,
            'tx_pin' => $txPin,
            'registered_at' => $user->created_at ? $user->created_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A'),
            'referral_link' => url('/user/register?sponsor='.$user->referral_code),
        ];

        return view('user.auth.register', [
            'sponsor' => $user->sponsor_code,
            'isLockedSponsor' => false,
            'registeredUser' => $registeredUser,
            'showModal' => true,
        ]);
    }
}
