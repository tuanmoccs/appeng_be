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

  public static function generate($userId, $deviceInfo = null)
  {
    // Xóa các refresh token cũ đã hết hạn
    self::where('user_id', $userId)
      ->where('expires_at', '<', now())
      ->delete();

    // Giới hạn số lượng refresh token (tối đa 5 thiết bị)
    $count = self::where('user_id', $userId)->count();
    if ($count >= 5) {
      self::where('user_id', $userId)
        ->orderBy('created_at', 'asc')
        ->first()
        ->delete();
    }

    $token = Str::random(64);

    return self::create([
      'user_id' => $userId,
      'token' => hash('sha256', $token),
      'device_info' => $deviceInfo,
      'expires_at' => Carbon::now()->addDays(30) // 30 ngày
    ]);
  }

  public static function verify($token)
  {
    $hashedToken = hash('sha256', $token);

    return self::where('token', $hashedToken)
      ->where('expires_at', '>', now())
      ->first();
  }

  public function isExpired()
  {
    return $this->expires_at < now();
  }
}
