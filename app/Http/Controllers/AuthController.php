<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function index()
    {
        return view('auth.login'); // Sesuaikan letak file blade Anda, misal di folder resources/views/auth/login.blade.php
    }

    // Memproses data login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek apakah input berupa email atau username
        $tipeLogin = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Susun array untuk attempt (percobaan login)
        $kredensial = [
            $tipeLogin => $request->username,
            'password' => $request->password,
        ];

        // Lakukan pengecekan ke database
        if (Auth::attempt($kredensial)) {
            $request->session()->regenerate();

            // Super admin -> Manajemen Akun; admin logistik -> dashboard utama; admin dapur ->
            // halaman laporan dapurnya sendiri.
            $tujuan = Auth::user()->rutePerdana();

            return redirect($tujuan)->with('status', 'Berhasil masuk ke sistem.');
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'username' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}