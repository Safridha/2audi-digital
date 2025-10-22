<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * 🔹 Tampilkan form login.
     * Halaman login bisa dibuka kapan saja, meskipun user sudah login.
     */
    public function create()
    {
        // Logout user lama supaya bisa login ulang
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('auth.login');
    }

    /**
     * 🔹 Proses login.
     */
    public function store(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Coba login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        // Regenerasi session agar aman
        $request->session()->regenerate();

        // Redirect sesuai role
        return $this->redirectToRole();
    }

    /**
     * 🔹 Logout user.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        // Hapus session dan token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama
        return redirect('/');
    }

    /**
     * 🔹 Fungsi bantu redirect sesuai role.
     */
    private function redirectToRole()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default: user biasa → home
        return redirect()->route('home');
    }
}
