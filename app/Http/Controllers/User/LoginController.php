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
            'mobile'   => 'required|digits_between:10,15',
            'password' => 'required',
        ], [
            'mobile.required'        => 'Mobile number is required.',
            'mobile.digits_between'  => 'Enter a valid mobile number.',
            'password.required'      => 'Password is required.',
        ]);

        if (Auth::attempt(['mobile' => $request->mobile, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                Auth::logout();

                return back()->withErrors(['mobile' => 'Invalid user credentials provided.']);
            }

            $request->session()->regenerate();

            return redirect()->route('user.dashboard')->with('success', 'Welcome back, '.$user->name);
        }

        return back()->withErrors(['mobile' => 'Invalid mobile number or password.'])->withInput(['mobile' => $request->mobile]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('info', 'Logged out successfully.');
    }
}
