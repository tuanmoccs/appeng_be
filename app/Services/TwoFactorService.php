<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorService
{
  protected $google2fa;

  public function __construct()
  {
    $this->google2fa = new Google2FA();
  }

  /**
   * Tạo secret key cho user
   */
  public function generateSecretKey(): string
  {
    return $this->google2fa->generateSecretKey();
  }

  /**
   * Tạo QR Code URL cho Google Authenticator
   */
  public function getQRCodeUrl(string $email, string $secret): string
  {
    $appName = config('app.name', 'Learning English');
    return $this->google2fa->getQRCodeUrl(
      $appName,
      $email,
      $secret
    );
  }

  /**
   * Tạo QR Code SVG
   */
  public function generateQRCode(string $email, string $secret): string
  {
    $url = $this->getQRCodeUrl($email, $secret);

    $renderer = new ImageRenderer(
      new RendererStyle(200),
      new SvgImageBackEnd()
    );

    $writer = new Writer($renderer);
    return $writer->writeString($url);
  }

  /**
   * Xác thực OTP code
   */
  public function verifyCode(string $secret, string $code): bool
  {
    return $this->google2fa->verifyKey($secret, $code);
  }

  /**
   * Tạo recovery codes
   */
  public function generateRecoveryCodes(): Collection
  {
    return collect(range(1, 8))->map(function () {
      return Str::random(10) . '-' . Str::random(10);
    });
  }

  /**
   * Mã hóa recovery codes
   */
  public function encryptRecoveryCodes(Collection $codes): string
  {
    return encrypt($codes->toJson());
  }

  /**
   * Giải mã recovery codes
   */
  public function decryptRecoveryCodes(string $encryptedCodes): Collection
  {
    return collect(json_decode(decrypt($encryptedCodes)));
  }
}
