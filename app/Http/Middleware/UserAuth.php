<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If an impersonated user ID exists in session, set Auth::user() context to that user for user routes
        if (session()->has('impersonated_user_id')) {
            $impersonatedUser = User::find(session('impersonated_user_id'));
            if ($impersonatedUser) {
                Auth::setUser($impersonatedUser);

                return $next($request);
            }
        }

        if (! Auth::check()) {
            return redirect()->route('user.login')->with('error', 'Please log in to access your Member Portal.');
        }

        if (Auth::user()->isAdmin()) {
            Auth::logout();

            return redirect()->route('user.login')->withErrors(['email' => 'Invalid user credentials provided.']);
        }

        return $next($request);
    }
}
