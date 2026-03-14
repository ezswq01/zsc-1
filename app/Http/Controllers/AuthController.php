<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginGet()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials, true)) {
            if (auth()->user()->hasRole('employee')) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Invalid credentials');
            }
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->route('login')->with('error', 'Invalid credentials');
    }

    public function logoutPost()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function changePassword()
    {
        return view('admin.change_password');
    }

    public function changePasswordStore(Request $request)
    {
        $request->validate([
            'old_password' => 'required|min:8',
            'password' => 'required|min:8|same:password',
        ]);

        if (!password_verify($request->old_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Old password is incorrect');
        }

        auth()->user()->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully');
    }
}
