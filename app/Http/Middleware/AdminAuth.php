<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (!Auth::guard('admin')->check()) {
      return redirect()->route('admin.login');
    }

    // Kiểm tra admin có active không
    if (!Auth::guard('admin')->user()->is_active) {
      Auth::guard('admin')->logout();
      return redirect()->route('admin.login')->withErrors([
        'email' => 'Tài khoản của bạn đã bị vô hiệu hóa.'
      ]);
    }

    return $next($request);
  }
}
