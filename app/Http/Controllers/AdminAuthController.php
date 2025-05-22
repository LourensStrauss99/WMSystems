<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function __construct()
    {
       // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        // Optionally, you can redirect if already admin:
        if (auth()->check() && (auth()->user()->is_superuser || auth()->user()->admin_level == 3)) {
            return redirect()->route('admin.panel');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if user is superuser or admin_level 3
            $user = Auth::user();
            if ($user->is_superuser || $user->admin_level == 3) {
                return redirect()->intended('/admin/panel');
            }

            // If not admin, logout and return with error
            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have admin access.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
