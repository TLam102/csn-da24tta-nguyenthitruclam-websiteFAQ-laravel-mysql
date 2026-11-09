<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $r)
    {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($data, $r->boolean('remember'))) {
            $r->session()->regenerate();
            // "Phân quyền theo vai trò"
            return Auth::user()->isQuanTri()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('faq.index');
        }
        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::create($data + ['vai_tro' => User::VAI_TRO_NGUOI_DUNG]);
        Auth::login($user);
        return redirect()->route('faq.index')->with('ok', 'Đăng ký tài khoản thành công!');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('faq.index');
    }
}
