<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    public function home()
    {
        return view('guest.home');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated(), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('patient.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        // Aman untuk database lama: field tambahan hanya disimpan jika kolomnya sudah ada.
        if (Schema::hasColumn('users', 'phone')) {
            $userData['phone'] = $data['phone'] ?? null;
        }

        if (Schema::hasColumn('users', 'role')) {
            $userData['role'] = 'patient';
        }

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->route('patient.dashboard')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('home'))->with('success', 'Anda berhasil logout.');
    }
}
