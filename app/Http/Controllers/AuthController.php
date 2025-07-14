<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan form login ke pengguna
    public function showLoginForm()
    {
        return view('auth.login'); // pakai resources/views/login.blade.php
    }

    // Menangani proses login saat form dikirimkan
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'regex:/^\S*$/'],
            'password' => ['required', 'string'],
        ], [
            'username.regex' => 'Username tidak boleh mengandung spasi.',
        ]);

        // Ambil hanya username dan password dari request
        $credentials = $request->only('username', 'password');

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            return redirect('/dashboard');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    // Proses logout pengguna
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
