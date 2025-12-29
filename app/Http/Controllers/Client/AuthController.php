<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use App\Mail\ResetPasswordOtpMail;
use App\Models\LoginAttempt;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;
    // protected $captchaService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $email = $credentials['email'];
            $password = $credentials['password'];
            $ipAddress = $request->ip();

            Log::info('Login attempt', [
                'email' => $email,
                'has_otp_code' => isset($credentials['otp_code']),
                'otp_code' => $credentials['otp_code'] ?? 'NOT PROVIDED'
            ]);

            // ✅ 1. CHECK ACCOUNT LOCK
            if (LoginAttempt::isLocked($email, $ipAddress, 5)) {
                $lockUntil = LoginAttempt::getLockUntil($email, $ipAddress);
                $currentAttempts = LoginAttempt::getFailedAttempts($email, $ipAddress);
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản đã bị khóa do đăng nhập sai quá 5 lần. Vui lòng thử lại sau 15 phút.',
                    'locked' => true,
                    'lock_until' => $lockUntil ? $lockUntil->toISOString() : null,
                    'attempts' => $currentAttempts
                ], 423);
            }

            // ✅ 2. VERIFY EMAIL & PASSWORD (KHÔNG TẠO TOKEN Ở ĐÂY)
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                LoginAttempt::recordAttempt($email, $ipAddress, false);
                $attempts = LoginAttempt::getFailedAttempts($email, $ipAddress);
                $remainingAttempts = 5 - $attempts;

                throw new Exception("Email hoặc mật khẩu không đúng (Còn {$remainingAttempts} lần thử)");
            }

            // ✅ 3. CHECK 2FA REQUIREMENT
            if ($user->two_factor_enabled) {
                // ❌ Nếu chưa có OTP code → Yêu cầu nhập
                if (!isset($credentials['otp_code']) || empty($credentials['otp_code'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng nhập mã xác thực 2FA',
                        'require_2fa' => true,
                        'email' => $email
                    ], 400);
                }

                // ✅ Có OTP code → Verify
                $twoFactorService = app(\App\Services\TwoFactorService::class);
                $secret = decrypt($user->two_factor_secret);

                Log::info('Verifying OTP', [
                    'email' => $email,
                    'otp_code' => $credentials['otp_code'],
                    'otp_length' => strlen($credentials['otp_code'])
                ]);

                // Check if recovery code or regular OTP
                if (strlen($credentials['otp_code']) > 6) {
                    // ✅ RECOVERY CODE
                    $recoveryCodes = $twoFactorService->decryptRecoveryCodes(
                        $user->two_factor_recovery_codes
                    );

                    if (!$recoveryCodes->contains($credentials['otp_code'])) {
                        LoginAttempt::recordAttempt($email, $ipAddress, false);
                        $attempts = LoginAttempt::getFailedAttempts($email, $ipAddress);
                        $remainingAttempts = 5 - $attempts;

                        return response()->json([
                            'success' => false,
                            'message' => "Recovery code không hợp lệ (Còn {$remainingAttempts} lần thử)",
                            'require_2fa' => true,
                            'attempts' => $attempts
                        ], 400);
                    }

                    // Remove used recovery code
                    $recoveryCodes = $recoveryCodes->reject(function ($code) use ($credentials) {
                        return $code === $credentials['otp_code'];
                    });

                    $user->two_factor_recovery_codes = $twoFactorService->encryptRecoveryCodes($recoveryCodes);
                    $user->save();

                    Log::info('Recovery code verified successfully', ['email' => $email]);
                } else {
                    // ✅ REGULAR OTP
                    if (!$twoFactorService->verifyCode($secret, $credentials['otp_code'])) {
                        LoginAttempt::recordAttempt($email, $ipAddress, false);
                        $attempts = LoginAttempt::getFailedAttempts($email, $ipAddress);
                        $remainingAttempts = 5 - $attempts;

                        Log::warning('OTP verification failed', ['email' => $email]);

                        return response()->json([
                            'success' => false,
                            'message' => "Mã OTP không chính xác (Còn {$remainingAttempts} lần thử)",
                            'require_2fa' => true,
                            'attempts' => $attempts
                        ], 400);
                    }

                    Log::info('OTP verified successfully', ['email' => $email]);
                }
            }

            // ✅ 4. SAU KHI VERIFY THÀNH CÔNG (hoặc không cần 2FA), TẠO TOKEN
            Log::info('Creating JWT token for user', ['user_id' => $user->id, 'email' => $email]);

            $token = JWTAuth::fromUser($user);

            Log::info('JWT token created', [
                'user_id' => $user->id,
                'token_length' => strlen($token)
            ]);

            // ✅ 5. UPDATE LAST LOGIN
            $user->update(['last_login_at' => now()]);

            // ✅ 6. CLEANUP OLD REFRESH TOKENS & CREATE NEW ONE
            Log::info('Cleaning up old refresh tokens', ['user_id' => $user->id]);

            RefreshToken::revokeUserTokens($user->id, 5);

            Log::info('Generating new refresh token', ['user_id' => $user->id]);

            $refreshTokenData = RefreshToken::generate(
                $user->id,
                $request->header('User-Agent')
            );

            Log::info('Refresh token generated', [
                'user_id' => $user->id,
                'token_length' => strlen($refreshTokenData['token_string']),
                'token_refresh' => $refreshTokenData
            ]);

            // ✅ 7. CLEAR LOGIN ATTEMPTS
            LoginAttempt::recordAttempt($email, $ipAddress, true);
            LoginAttempt::clearAttempts($email, $ipAddress);

            Log::info('Login successful', [
                'user_id' => $user->id,
                'email' => $email,
                'has_2fa' => $user->two_factor_enabled
            ]);

            // ✅ 8. RETURN SUCCESS RESPONSE
            $response = [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $user,
                'token' => $token,
                'refresh_token' => $refreshTokenData['token_string'],
                'expires_in' => config('jwt.ttl', 60) * 60,
                'token_type' => 'Bearer'
            ];

            Log::info('Login response data', [
                'has_token' => !empty($response['token']),
                'has_refresh_token' => !empty($response['refresh_token']),
                'token_length' => strlen($response['token']),
                'refresh_token_length' => strlen($response['refresh_token'])
            ]);

            return response()->json($response, 200);
        } catch (Exception $e) {
            Log::error('Login exception', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $email = $request->input('email');
            $ipAddress = $request->ip();

            LoginAttempt::recordAttempt($email, $ipAddress, false);

            $failedAttempts = LoginAttempt::getFailedAttempts($email, $ipAddress);
            $locked = $failedAttempts >= 5;
            $lockUntil = null;

            if ($locked) {
                $message = 'Tài khoản đã bị khóa do đăng nhập sai quá 5 lần. Vui lòng thử lại sau 15 phút.';
                $lockUntil = LoginAttempt::getLockUntil($email, $ipAddress);
            } else {
                $remainingAttempts = 5 - $failedAttempts;
                $message = $e->getMessage();
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'locked' => $locked,
                'lock_until' => $lockUntil ? $lockUntil->toISOString() : null,
                'attempts' => $failedAttempts
            ], $locked ? 423 : 401);
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $refreshTokenString = $request->input('refresh_token') ?? $request->header('refresh_token');

            Log::info('=== REFRESH TOKEN REQUEST ===', [
                'has_body_token' => !empty($request->input('refresh_token')),
                'has_header_token' => !empty($request->header('refresh_token')),
                'token_length' => $refreshTokenString ? strlen($refreshTokenString) : 0,
                'token_first_10' => $refreshTokenString ? substr($refreshTokenString, 0, 10) : 'null'
            ]);

            if (!$refreshTokenString) {
                Log::error('Refresh token not provided', [
                    'body' => $request->all(),
                    'headers' => $request->headers->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token không được cung cấp',
                    'require_login' => true
                ], 401);
            }

            // Hash token để tìm trong DB
            $hashedToken = hash('sha256', $refreshTokenString);

            Log::info('Searching for token in database', [
                'hashed_token_first_20' => substr($hashedToken, 0, 20)
            ]);

            $tokenRecord = RefreshToken::verify($refreshTokenString);

            if (!$tokenRecord) {
                Log::error('Token not found in database', [
                    'hashed_token' => $hashedToken,
                    'latest_tokens_in_db' => RefreshToken::orderBy('created_at', 'desc')
                        ->take(5)
                        ->get()
                        ->map(function ($t) {
                            return [
                                'id' => $t->id,
                                'user_id' => $t->user_id,
                                'token_first_20' => substr($t->token, 0, 20),
                                'created_at' => $t->created_at,
                                'expires_at' => $t->expires_at
                            ];
                        })
                        ->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token không hợp lệ hoặc đã hết hạn',
                    'require_login' => true
                ], 401);
            }

            Log::info('Token found in database', [
                'token_id' => $tokenRecord->id,
                'user_id' => $tokenRecord->user_id,
                'expires_at' => $tokenRecord->expires_at
            ]);

            $user = $tokenRecord->user;

            if (!$user) {
                Log::error('User not found for token', ['token_id' => $tokenRecord->id]);
                $tokenRecord->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không tồn tại',
                    'require_login' => true
                ], 401);
            }

            // Tạo access token mới
            $newAccessToken = JWTAuth::fromUser($user);

            Log::info('New access token created', [
                'user_id' => $user->id,
                'token_length' => strlen($newAccessToken)
            ]);

            // Xóa token cũ và tạo refresh token mới
            $tokenRecord->delete();
            $newRefreshTokenData = RefreshToken::generate(
                $user->id,
                $request->header('User-Agent')
            );

            Log::info('New refresh token created', [
                'user_id' => $user->id,
                'token_length' => strlen($newRefreshTokenData['token_string'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token đã được làm mới',
                'token' => $newAccessToken,
                'refresh_token' => $newRefreshTokenData['token_string'],
                'expires_in' => config('jwt.ttl', 60) * 60,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Refresh token error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi làm mới token',
                'require_login' => true
            ], 500);
        }
    }

    /**
     * User registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            $result = $this->authService->register($userData);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'user' => $result['user'],
                'token' => $result['token']
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Social login
     */

    public function redirectToProvider(string $provider): JsonResponse
    {
        try {
            $this->validateProvider($provider);

            $url = Socialite::driver($provider)
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'success' => true,
                'url' => $url
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function handleProviderCallback(string $provider, Request $request)
    {
        try {
            $this->validateProvider($provider);

            $providerUser = Socialite::driver($provider)
                ->stateless()
                ->user();

            $result = $this->authService->handleOAuthLogin($provider, $providerUser);

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5137');
            $redirectUrl = $frontendUrl . '/oauth/callback?token=' . $result['token'] . '&user=' . urlencode(json_encode($result['user']));

            return redirect($redirectUrl);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            throw new Exception('Provider không được hỗ trợ');
        }
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng'
            ], 401);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            Log::info('Logout attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            // Gọi service để xử lý logout
            $this->authService->logout($user);

            Log::info('Logout successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ], 200);
        } catch (Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng xuất'
            ], 500);
        }
    }

    public function resetPasswordWithOTP(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->authService->resetPasswordWithOTP(
                $request->email,
                $request->otp,
                $request->password
            );

            LoginAttempt::clearAllAttemptsForEmail($request->email);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                RefreshToken::where('user_id', $user->id)->delete();
                Log::info('All refresh tokens revoked for user after password reset', [
                    'user_id' => $user->id,
                    'email' => $request->email
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function sendResetOTP(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->email;
            $this->authService->sendResetOTP($email);

            return response()->json([
                'success' => true,
                'message' => 'Mã OTP đã được gửi đến email của bạn'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $email = $request->validated()['email'];
            $this->authService->sendPasswordResetEmail($email);

            return response()->json([
                'success' => true,
                'message' => 'Email khôi phục mật khẩu đã được gửi'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->user());

            return response()->json([
                'success' => true,
                'token' => $result['token'],
                'user' => $result['user']
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể làm mới token'
            ], 401);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'avatar' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only(['name', 'avatar']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin'
            ], 500);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $this->authService->changePassword(
                $user,
                $request->current_password,
                $request->new_password
            );

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUserAchievements(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $achievements = $user->achievements()->orderBy('achieved_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'achievements' => $achievements
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin thành tích'
            ], 500);
        }
    }

    public function getUserStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $stats = $this->authService->getUserStats($user);

            return response()->json([
                'success' => true,
                'stats' => $stats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê người dùng'
            ], 500);
        }
    }
}
