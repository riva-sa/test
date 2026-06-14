<?php

namespace App\Http\Controllers\Broker;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\BrokerActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BrokerAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('broker')->check()) {
            return redirect()->route('broker.dashboard');
        }

        return view('broker.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $broker = Broker::where('email', $credentials['email'])->first();

        if (! $broker || ! Hash::check($credentials['password'], $broker->password)) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->onlyInput('email');
        }

        // No login until the admin approves the application
        if ($broker->isPending()) {
            return back()->with('broker_pending', 'تم استلام طلب التسجيل وسيتم مراجعة البيانات من قبل الإدارة.')->onlyInput('email');
        }

        if ($broker->isRejected()) {
            return back()->with('broker_rejected', $broker->rejection_reason ?: 'نأسف، تم رفض طلب التسجيل الخاص بك.')->onlyInput('email');
        }

        Auth::guard('broker')->login($broker, $request->boolean('remember'));
        $request->session()->regenerate();

        BrokerActivityLog::record('login', $broker->id, 'تسجيل دخول الوسيط إلى البوابة');

        return redirect()->intended(route('broker.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('broker')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('broker.login');
    }
}
