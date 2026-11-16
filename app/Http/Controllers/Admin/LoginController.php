<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LoginAuditRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Secret Key Query Protection for Security (ngt-2026)
        if ($request->query('key') === 'ngt-2026') {
            session(['admin_secret_key' => true]);
        }

        if (! session('admin_secret_key')) {
            abort(404);
        }

        return view('admin.auth.login');
    }

    public function login(Request $request, LoginAuditRecorder $loginAuditRecorder): RedirectResponse
    {
        if ($request->query('key') === 'ngt-2026') {
            session(['admin_secret_key' => true]);
        }

        if (! session('admin_secret_key')) {
            abort(404);
        }

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'referral_code';

        if (Auth::attempt([$fieldType => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            if (! $user->isAdmin()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Access denied. Administrator privileges required.']);
            }

            if ($user->status !== 'active') {
                Auth::logout();

                return back()->withErrors(['email' => 'Your admin account is currently inactive.']);
            }

            $request->session()->regenerate();
            session(['admin_user_id' => $user->id, 'admin_secret_key' => true]);
            $loginAuditRecorder->recordSuccessfulLogin($user, $request, 'admin_web', 'password');

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, '.$user->name);
        }

        return back()->withErrors(['email' => 'Invalid admin credentials provided.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(['admin_user_id', 'impersonated_user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login', ['key' => 'ngt-2026'])->with('info', 'Logged out successfully.');
    }
}
