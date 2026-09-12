<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard');
        }

        return view('user.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'referral_code';

        if (Auth::attempt([$fieldType => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Invalid user credentials provided.']);
            }

            $request->session()->regenerate();

            return redirect()->route('user.dashboard')->with('success', 'Welcome back, '.$user->name);
        }

        return back()->withErrors(['email' => 'Invalid user credentials provided.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('info', 'Logged out successfully.');
    }
}
