<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CaptchaService
{
  public function verify($token)
  {
    if (empty($token)) {
      return false;
    }

    try {
      $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret_key'),
        'response' => $token,
        'remoteip' => request()->ip()
      ]);

      $result = $response->json();

      return isset($result['success']) && $result['success'] === true;
    } catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('Captcha verification failed: ' . $e->getMessage());
      return false;
    }
  }
}
