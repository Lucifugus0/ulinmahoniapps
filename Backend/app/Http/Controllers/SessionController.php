<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Symfony\Component\CssSelector\Node\ElementNode;

class SessionController extends Controller
{
    function index()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Cari user yang aktif dan admin
        $user = User::where('email', $credentials['email'])
            ->where('status', 1)
            ->where('is_admin', 1)
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Login gagal! Pastikan email, password benar, dan akun aktif sebagai admin.',
        ])->onlyInput('email');
    }
}
