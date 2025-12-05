<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
  protected $twoFactorService;

  public function __construct(TwoFactorService $twoFactorService)
  {
    $this->twoFactorService = $twoFactorService;
  }

  /**
   * Bật 2FA - Tạo secret và QR code
   */
  public function enable(Request $request): JsonResponse
  {
    $user = Auth::user();

    if ($user->two_factor_enabled) {
      return response()->json([
        'success' => false,
        'message' => '2FA đã được bật trước đó'
      ], 400);
    }

    // Tạo secret key
    $secret = $this->twoFactorService->generateSecretKey();

    // Tạo QR code
    $qrCodeSvg = $this->twoFactorService->generateQRCode($user->email, $secret);

    // Lưu tạm secret (chưa enable)
    $user->two_factor_secret = encrypt($secret);
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Vui lòng quét QR code bằng Google Authenticator',
      'secret' => $secret,
      'qr_code' => $qrCodeSvg
    ]);
  }

  /**
   * Xác nhận bật 2FA bằng OTP
   */
  public function confirm(Request $request): JsonResponse
  {
    $request->validate([
      'code' => 'required|string|size:6'
    ]);

    $user = Auth::user();

    if ($user->two_factor_enabled) {
      return response()->json([
        'success' => false,
        'message' => '2FA đã được bật'
      ], 400);
    }

    $secret = decrypt($user->two_factor_secret);

    // Xác thực OTP
    if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
      return response()->json([
        'success' => false,
        'message' => 'Mã OTP không chính xác'
      ], 400);
    }

    // Tạo recovery codes
    $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

    // Bật 2FA
    $user->two_factor_enabled = true;
    $user->two_factor_confirmed_at = now();
    $user->two_factor_recovery_codes = $this->twoFactorService->encryptRecoveryCodes($recoveryCodes);
    $user->save();

    return response()->json([
      'success' => true,
      'message' => '2FA đã được bật thành công',
      'recovery_codes' => $recoveryCodes
    ]);
  }

  /**
   * Xác thực OTP khi đăng nhập
   */
  public function verify(Request $request): JsonResponse
  {
    $request->validate([
      'code' => 'required|string',
      'email' => 'required|email'
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !$user->two_factor_enabled) {
      return response()->json([
        'success' => false,
        'message' => 'Người dùng không tồn tại hoặc chưa bật 2FA'
      ], 400);
    }

    $secret = decrypt($user->two_factor_secret);

    // Kiểm tra nếu là recovery code
    if (strlen($request->code) > 6) {
      return $this->verifyRecoveryCode($user, $request->code);
    }

    // Xác thực OTP
    if (!$this->twoFactorService->verifyCode($secret, $request->code)) {
      return response()->json([
        'success' => false,
        'message' => 'Mã OTP không chính xác'
      ], 400);
    }

    return response()->json([
      'success' => true,
      'message' => 'Xác thực thành công'
    ]);
  }

  /**
   * Tắt 2FA
   */
  public function disable(Request $request): JsonResponse
  {
    $request->validate([
      'password' => 'required|string'
    ]);

    $user = Auth::user();

    // Xác thực mật khẩu
    if (!\Hash::check($request->password, $user->password)) {
      return response()->json([
        'success' => false,
        'message' => 'Mật khẩu không chính xác'
      ], 400);
    }

    $user->two_factor_enabled = false;
    $user->two_factor_secret = null;
    $user->two_factor_recovery_codes = null;
    $user->two_factor_confirmed_at = null;
    $user->save();

    return response()->json([
      'success' => true,
      'message' => '2FA đã được tắt'
    ]);
  }

  /**
   * Lấy recovery codes mới
   */
  public function regenerateRecoveryCodes(Request $request): JsonResponse
  {
    $user = Auth::user();

    if (!$user->two_factor_enabled) {
      return response()->json([
        'success' => false,
        'message' => '2FA chưa được bật'
      ], 400);
    }

    $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
    $user->two_factor_recovery_codes = $this->twoFactorService->encryptRecoveryCodes($recoveryCodes);
    $user->save();

    return response()->json([
      'success' => true,
      'recovery_codes' => $recoveryCodes
    ]);
  }

  /**
   * Xác thực recovery code
   */
  protected function verifyRecoveryCode($user, string $code): JsonResponse
  {
    $recoveryCodes = $this->twoFactorService->decryptRecoveryCodes(
      $user->two_factor_recovery_codes
    );

    if (!$recoveryCodes->contains($code)) {
      return response()->json([
        'success' => false,
        'message' => 'Recovery code không hợp lệ'
      ], 400);
    }

    // Xóa code đã dùng
    $recoveryCodes = $recoveryCodes->reject(function ($recoveryCode) use ($code) {
      return $recoveryCode === $code;
    });

    $user->two_factor_recovery_codes = $this->twoFactorService->encryptRecoveryCodes($recoveryCodes);
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Xác thực thành công bằng recovery code'
    ]);
  }
}
