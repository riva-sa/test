<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ManagerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['هذا الحساب غير نشط. يرجى التواصل مع الإدارة.'],
                ]);
            }

            $request->session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();

            if ($user->hasRole('Admin')) {
                return redirect()->route('manager.dashboard');
            } elseif ($user->hasRole('developer')) {
                return redirect()->route('developer.dashboard');
            } elseif ($user->hasRole('sales_manager') || $user->hasRole('sales') || $user->hasRole('follow_up') || $user->hasRole('project_manager')) {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('frontend.home');
            }
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
