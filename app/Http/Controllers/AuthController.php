<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // validasi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            AuditLog::create([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id ?? null,
                'auditable_type' => 'Authentication',
                'auditable_id' => $user->id,
                'action' => 'login',
                'description' => 'User login berhasil',
                'old_values' => null,
                'new_values' => null,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // redirect berdasarkan role
            switch (strtolower($user->role)) {
                case 'owner':
                    return redirect('/owner');
                case 'manager':
                    return redirect('/manager');
                case 'finance':
                    return redirect('/finance');
                case 'warehouse':
                    return redirect('/warehouse');
                case 'cashier':
                    return redirect('/cashier');
                default:
                    return redirect('/'); // redirect ke home jika role tidak dikenali
            }
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id ?? null,
                'auditable_type' => 'Authentication',
                'auditable_id' => $user->id,
                'action' => 'logout',
                'description' => 'User logout',
                'old_values' => null,
                'new_values' => null,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}
