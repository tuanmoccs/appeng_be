<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
  protected $fillable = [
    'email',
    'ip_address',
    'attempted_at',
    'successful',
  ];

  protected $casts = [
    'attempted_at' => 'datetime',
    'successful' => 'boolean',
  ];

  /**
   * Ghi lại một lần đăng nhập
   */
  public static function recordAttempt(string $email, string $ipAddress, bool $successful = false): self
  {
    return self::create([
      'email' => $email,
      'ip_address' => $ipAddress,
      'attempted_at' => Carbon::now(),
      'successful' => $successful,
    ]);
  }

  /**
   * Lấy số lần đăng nhập thất bại trong 15 phút gần nhất
   */
  public static function getFailedAttempts(string $email, string $ipAddress): int
  {
    return self::where('email', $email)
      ->where('ip_address', $ipAddress)
      ->where('successful', false)
      ->where('attempted_at', '>=', Carbon::now()->subMinutes(15))
      ->count();
  }

  /**
   * Kiểm tra xem tài khoản có bị khóa không (sai >= maxAttempts lần trong 15 phút)
   */
  public static function isLocked(string $email, string $ipAddress, int $maxAttempts = 5): bool
  {
    return self::getFailedAttempts($email, $ipAddress) >= $maxAttempts;
  }

  /**
   * Lấy thời gian khóa còn lại (tính từ lần thất bại cuối cùng + 15 phút)
   */
  public static function getLockUntil(string $email, string $ipAddress): ?Carbon
  {
    $lastFailedAttempt = self::where('email', $email)
      ->where('ip_address', $ipAddress)
      ->where('successful', false)
      ->where('attempted_at', '>=', Carbon::now()->subMinutes(15))
      ->orderBy('attempted_at', 'desc')
      ->first();

    if ($lastFailedAttempt && self::isLocked($email, $ipAddress)) {
      return $lastFailedAttempt->attempted_at->addMinutes(15);
    }

    return null;
  }

  /**
   * Xóa tất cả các lần đăng nhập thất bại khi đăng nhập thành công
   */
  public static function clearAttempts(string $email, string $ipAddress): void
  {
    self::where('email', $email)
      ->where('ip_address', $ipAddress)
      ->where('successful', false)
      ->delete();
  }

  /**
   * Dọn dẹp các bản ghi cũ hơn 24 giờ
   */
  public static function cleanup(): void
  {
    self::where('attempted_at', '<', Carbon::now()->subDay())->delete();
  }
}
