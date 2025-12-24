<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RefreshToken extends Model
{
  protected $fillable = [
    'user_id',
    'token',
    'device_info',
    'expires_at'
  ];

  protected $casts = [
    'expires_at' => 'datetime'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public static function generate(int $userId, ?string $deviceInfo = null): array
  {
    $tokenString = Str::random(64);

    $token = self::create([
      'user_id' => $userId,
      'token' => hash('sha256', $tokenString), // Hash để bảo mật
      'device_info' => $deviceInfo,
      'expires_at' => now()->addDays(30), // Refresh token sống 30 ngày
    ]);

    return [
      'token_string' => $tokenString, // Trả về plain text để gửi cho client
      'model' => $token
    ];
  }


  public static function verify(string $tokenString): ?self
  {
    $hashedToken = hash('sha256', $tokenString);

    return self::where('token', $hashedToken)
      ->where('expires_at', '>', now())
      ->first();
  }

  // ✅ Xóa token cũ của user (giới hạn số device)
  public static function revokeUserTokens(int $userId, int $keepLatest = 5): void
  {
    self::where('user_id', $userId)
      ->orderBy('created_at', 'desc')
      ->skip($keepLatest)
      ->take(PHP_INT_MAX)
      ->delete();
  }
}
