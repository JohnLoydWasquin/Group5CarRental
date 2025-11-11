<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Redirect logged-in users
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($user->role === 'staff') {
                return redirect('/staff/dashboard');
            } else {
                return redirect('/');
            }
        }

        // Always define variables to avoid Intelephense warning
        $rememberedEmail = $request->cookie('remember_email') ?? '';
        $rememberedPassword = $request->cookie('remember_password') 
            ? Crypt::decryptString($request->cookie('remember_password')) 
            : '';

        return view('layouts.auth.login', compact('rememberedEmail', 'rememberedPassword'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Save email and password in cookies if "Remember Me" checked
            if ($remember) {
                Cookie::queue('remember_email', $request->email, 43200); // 30 days
                Cookie::queue('remember_password', Crypt::encryptString($request->password), 43200);
            } else {
                Cookie::queue(Cookie::forget('remember_email'));
                Cookie::queue(Cookie::forget('remember_password'));
            }

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'staff') {
                return redirect()->intended('/staff/dashboard');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user && in_array($user->role, ['admin', 'staff'])) {
            return redirect()->route('login.form');
        }

        return redirect()->route('home');
    }
}
