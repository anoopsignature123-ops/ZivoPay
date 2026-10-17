<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForm(Request $request): View
    {
        $email = (string) $request->query('email', '');
        $token = (string) $request->query('token', '');
        $user = $email !== '' ? User::query()->where('email', $email)->first() : null;
        $validToken = $user !== null && $token !== '' && Password::broker()->tokenExists($user, $token);

        return view('auth.password-reset', compact('email', 'token', 'validToken'));
    }

    public function showSuccess(Request $request): View
    {
        abort_unless($request->session()->get('password_reset_success'), 404);

        return view('auth.password-reset-success');
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker()->reset($data, function (User $user) use ($data): void {
            $user->forceFill([
                'password' => $data['password'],
                'remember_token' => Str::random(60),
            ])->save();
            $user->tokens()->delete();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('password.reset.success')->with('password_reset_success', true);
        }

        return back()->withErrors([
            'reset' => 'This password reset link is invalid, expired, or has already been used. Request a new link and try again.',
        ]);
    }
}
