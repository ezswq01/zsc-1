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

        if (auth()->attempt($credentials)) {
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
}
