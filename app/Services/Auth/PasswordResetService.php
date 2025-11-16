<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordResetService
{
    protected OtpService $otpService;

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

        // Generate and send OTP with current locale
        $locale = app()->getLocale();
        $this->otpService->generateOtp($email, 'forgot_password', $user->id, $locale);

        return [
            'exists' => true,
            'otp_sent' => true,
        ];
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordResetOtp(string $email, string $otpCode): bool
    {
        return $this->otpService->verifyOtp($email, $otpCode, 'forgot_password');
    }

    /**
     * Reset password after OTP verified
     */
    public function resetPassword(string $email, string $newPassword): User
    {
        $user = User::where('email', $email)->firstOrFail();

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $user;
    }
}

