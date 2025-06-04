<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  /**
   * Show the admin login form.
   */
  public function showLoginForm()
  {
    // Nếu đã đăng nhập thì redirect về dashboard
    if (Auth::guard('admin')->check()) {
      return redirect()->route('admin.dashboard');
    }

    return view('admin.auth.login');
  }

  /**
   * Handle admin login request.
   */
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|string',
    ], [
      'email.required' => 'Vui lòng nhập email.',
      'email.email' => 'Email không hợp lệ.',
      'password.required' => 'Vui lòng nhập mật khẩu.',
    ]);

    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');

    // Thử đăng nhập với guard admin
    if (Auth::guard('admin')->attempt($credentials, $remember)) {
      $request->session()->regenerate();

      // Kiểm tra admin có active không
      if (!Auth::guard('admin')->user()->is_active) {
        Auth::guard('admin')->logout();
        return back()->withErrors([
          'email' => 'Tài khoản của bạn đã bị vô hiệu hóa.'
        ]);
      }

      return redirect()->intended(route('admin.dashboard'));
    }

    return back()->withErrors([
      'email' => 'Thông tin đăng nhập không chính xác.',
    ])->withInput($request->except('password'));
  }

  /**
   * Handle admin logout request.
   */
  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
  }
}
