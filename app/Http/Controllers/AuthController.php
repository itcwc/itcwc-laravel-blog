<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 显示登录页面
    public function showLogin()
    {
        // 如果已经登录了，直接去首页，不用再看登录框
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    // 处理登录请求
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 尝试登录
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // 登录失败
        return back()->withErrors([
            'email' => '凭据与我们的记录不符。',
        ])->onlyInput('email');
    }

    // 退出登录
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
