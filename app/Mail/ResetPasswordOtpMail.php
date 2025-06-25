<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;
    public $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $otp, Carbon $expiresAt)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Mã OTP đặt lại mật khẩu - ' . config('app.name'))
            ->view('mail.resetpassword')
            ->with([
                'user' => $this->user,
                'otp' => $this->otp,
                'expires_at' => $this->expiresAt
            ]);
    }
}
