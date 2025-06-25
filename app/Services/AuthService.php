<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStat;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;  
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthService
{
    /**
     * Handle user login
     */
    public function login(array $credentials): array
    {
        $email = $credentials['email'];
        $password = $credentials['password'];
        $remember = $credentials['remember'] ?? false;

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new Exception('Email hoặc mật khẩu không đúng');
        }

        //        if (!$user->is_active) {
        //            throw new Exception('Tài khoản đã bị khóa');
        //        }

        // Create JWT token
        $token = JWTAuth::fromUser($user);

        // Update last login
        $user->update([
            'last_login_at' => now()
        ]);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Handle user registration
     */
    public function register(array $userData): array
    {
        // Check if email already exists
        if (User::where('email', $userData['email'])->exists()) {
            throw new Exception('Email này đã được sử dụng');
        }

        // Create user
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            //            'is_active' => true,
            'email_verified_at' => now(), // Auto verify for now
        ]);

        // Create JWT token
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Handle user logout
     */
    public function logout(User $user): void
    {
        // Invalidate JWT token
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new Exception('Không tìm thấy người dùng với email này');
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new Exception('Không thể gửi email khôi phục mật khẩu');
        }
    }

    /**
     * Refresh user token
     */
    public function refreshToken(User $user): array
    {
        // Refresh JWT token
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Change password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new Exception('Mật khẩu hiện tại không đúng');
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
    public function getUserStats(User $user): array
    {
        // Tìm thống kê của người dùng dựa theo user_id
        $userStat = UserStat::where('user_id', $user->id)->first();

        if (!$userStat) {
            // Nếu chưa có thống kê, trả về mặc định
            return [
                'words_learned' => 0,
                'lessons_completed' => 0,
                'quizzes_completed' => 0,
                'streak_days' => 0,
                'last_activity_at' => null,
            ];
        }

        // Trả về thông tin thống kê
        return [
            'words_learned' => $userStat->words_learned,
            'lessons_completed' => $userStat->lessons_completed,
            'quizzes_completed' => $userStat->quizzes_completed,
            'streak_days' => $userStat->streak_days,
            'last_activity_at' => $userStat->last_activity_at,
        ];
    }
    public function sendResetOTP(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new Exception('Không tìm thấy người dùng với email này');
        }

        // Clean up old OTPs for this email
        PasswordResetOtp::where('email', $email)->delete();

        // Generate new OTP
        $otp = PasswordResetOtp::generateOTP();
        $expiresAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        // Save OTP to database
        PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP via email
        try {
            Mail::send('mail.resetpassword', [
                'otp' => $otp,
                'user' => $user,
                'expires_at' => $expiresAt
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Mã OTP đặt lại mật khẩu - ' . config('app.name'));
            });
        } catch (Exception $e) {
            // If email fails, still throw an exception but don't expose email details
            throw new Exception('Không thể gửi email OTP. Vui lòng thử lại sau.');
        }
    }

    /**
     * Reset password using OTP
     */
    public function resetPasswordWithOTP(string $email, string $otp, string $newPassword): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new Exception('Không tìm thấy người dùng với email này');
        }

        // Find valid OTP
        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            throw new Exception('Mã OTP không hợp lệ hoặc đã hết hạn');
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Clean up old OTPs
        PasswordResetOtp::cleanup();
    }
}
