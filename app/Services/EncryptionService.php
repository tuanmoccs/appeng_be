<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
  /**
   * Encrypt a message for secure storage
   */
  public static function encrypt(string $message): string
  {
    try {
      return Crypt::encryptString($message);
    } catch (\Exception $e) {
      Log::error('Encryption failed: ' . $e->getMessage());
      throw new \Exception('Failed to encrypt message');
    }
  }

  /**
   * Decrypt a message for display
   */
  public static function decrypt(string $encryptedMessage): string
  {
    try {
      return Crypt::decryptString($encryptedMessage);
    } catch (\Exception $e) {
      Log::error('Decryption failed: ' . $e->getMessage());
      return '[Message could not be decrypted]';
    }
  }

  /**
   * Hash a message for verification (one-way)
   */
  public static function hash(string $message): string
  {
    return hash('sha256', $message);
  }
}
