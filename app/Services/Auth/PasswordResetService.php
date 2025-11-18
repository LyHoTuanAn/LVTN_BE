<?php

namespace App\Services\Auth;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    protected OtpService $otpService;
    protected int $resetTokenTtlMinutes = 10;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request password reset - generate OTP for forgot_password type
     * 
     * @return array{exists: bool, otp_sent: bool}
     */
    public function requestPasswordReset(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'exists' => false,
                'otp_sent' => false,
            ];
        }

        PasswordResetToken::where('email', $email)->delete();
        
        // Generate and send OTP with current locale
        $locale = app()->getLocale();
        $this->otpService->generateOtp($email, 'forgot_password', $user->id, $locale);

        return [
            'exists' => true,
            'otp_sent' => true,
        ];
    }

    /**
     * Verify password reset OTP and return reset token
     */
    public function verifyPasswordResetOtp(string $email, string $otpCode): ?string
    {
        $otp = $this->otpService->verifyOtp($email, $otpCode, 'forgot_password');

        if (!$otp) {
            return null;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        return $this->createResetToken($user);
    }

    /**
     * Reset password after OTP verified
     */
    public function resetPassword(string $email, string $newPassword, string $resetToken): ?User
    {
        $hashedToken = hash('sha256', $resetToken);

        $tokenRecord = PasswordResetToken::where('email', $email)
            ->where('token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            return null;
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return null;
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Consume token after successful reset
        $tokenRecord->delete();

        return $user;
    }

    /**
     * Create password reset token for user
     */
    protected function createResetToken(User $user): string
    {
        // Remove existing tokens for this email
        PasswordResetToken::where('email', $user->email)->delete();

        $token = Str::random(64);

        PasswordResetToken::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => hash('sha256', $token),
            'expires_at' => now()->addMinutes($this->resetTokenTtlMinutes),
        ]);

        return $token;
    }

    public function getResetTokenTtlSeconds(): int
    {
        return $this->resetTokenTtlMinutes * 60;
    }
}

