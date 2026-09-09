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
        $isLockedSponsor = $request->has('sponsor');

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
        if ($code === 'DEX-0000001') {
            return response()->json([
                'success' => true,
                'name' => 'Dex Trade System Admin',
                'email' => 'admin@dextrade.com',
                'referral_code' => 'DEX-0000001',
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
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $sponsorCode = trim($request->sponsor_id);
        $sponsorUser = User::where('referral_code', $sponsorCode)->first();

        if (! $sponsorUser && $sponsorCode !== 'DEX-0000001') {
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
            'status' => 'inactive',
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        $registeredUser = [
            'user_id' => $user->referral_code,
            'sponsor_id' => $user->sponsor_code,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'tx_pin' => $txPin,
        ];

        return view('user.auth.register', [
            'sponsor' => $user->sponsor_code,
            'isLockedSponsor' => false,
            'registeredUser' => $registeredUser,
            'showModal' => true,
        ]);
    }
}
