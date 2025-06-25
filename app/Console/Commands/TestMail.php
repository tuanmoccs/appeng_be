<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ResetPasswordOTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TestMail extends Command
{
  protected $signature = 'test:mail {email}';
  protected $description = 'Test sending OTP email';

  public function handle()
  {
    $email = $this->argument('email');
    $user = User::where('email', $email)->first();

    if (!$user) {
      $this->error('User not found!');
      return;
    }

    try {
      Mail::to($email)->send(new ResetPasswordOTPMail($user, 'tymtym', Carbon::now()->addMinutes(10)));
      $this->info('Test email sent successfully!');
    } catch (\Exception $e) {
      $this->error('Failed to send email: ' . $e->getMessage());
    }
  }
}
