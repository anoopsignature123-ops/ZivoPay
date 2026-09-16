<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $admin = Auth::user();

        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$admin->id,
            'mobile' => 'required|string|max:20|unique:users,mobile,'.$admin->id,
        ], [
            'email.unique' => 'This email address is already in use by another user account.',
            'mobile.unique' => 'This mobile number is already in use by another user account.',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        return redirect()->back()->with('success', 'Admin profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = Auth::user();

        if (! Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Admin password changed successfully.');
    }
}
