<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display member profile & security settings page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $user->load('sponsor');

        return view('user.profile', compact('user'));
    }

    /**
     * Update member personal information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,mobile,'.$user->id],
            'usdt_address' => 'nullable|string|max:255',
            'wallet_address' => 'nullable|string|max:255',
        ], [
            'mobile.unique' => 'This mobile number is already registered to another user account.',
            'mobile.regex' => 'Mobile number must be a valid 10-digit number without +91 or country code.',
        ]);

        $usdtAddress = $request->usdt_address ? trim($request->usdt_address) : ($request->wallet_address ? trim($request->wallet_address) : null);

        $user->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'usdt_address' => $usdtAddress,
            'wallet_address' => $usdtAddress ?: $user->wallet_address,
        ]);

        return redirect()->back()->with('success', 'Profile and Withdrawal Wallet Address updated successfully!');
    }

    /**
     * Change member account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Account password changed successfully!');
    }
}
