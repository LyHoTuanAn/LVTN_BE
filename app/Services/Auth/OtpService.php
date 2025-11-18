<?php

namespace App\Services\Auth;

use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate OTP code (6 digits: 0-9)
     */
    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate and send OTP
     */
    public function generateOtp(string $email, string $type, ?int $userId = null, ?string $locale = null): Otp
    {
        // Invalidate old OTPs for this email and type
        $this->invalidateOtp($email, $type);

        // Generate new OTP
        $otpCode = $this->generateOtpCode();
        $expiresAt = now()->addMinutes(5);

        $otp = Otp::create([
            'user_id' => $userId,
            'email' => $email,
            'otp_code' => $otpCode,
            'type' => $type,
            'expires_at' => $expiresAt,
        ]);

        // Send email with locale
        $this->sendOtpEmail($otp, $locale);

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $email, string $otpCode, string $type): ?Otp
    {
        $otp = Otp::forEmail($email)
            ->forType($type)
            ->valid()
            ->where('otp_code', $otpCode)
            ->first();

        if (!$otp) {
            return null;
        }

        // Remove OTP after successful verification to prevent reuse
        $otp->delete();

        return $otp;
    }

    /**
     * Resend OTP with rate limiting (60 seconds)
     */
    public function resendOtp(string $email, string $type, ?string $locale = null): Otp
    {
        // Check rate limit - must be at least 60 seconds since last OTP
        $lastOtp = Otp::forEmail($email)
            ->forType($type)
            ->latest('created_at')
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
            throw new \Exception(__('errors.RATE_LIMIT_EXCEEDED'));
        }

        // Generate new OTP
        $user = \App\Models\User::where('email', $email)->first();
        return $this->generateOtp($email, $type, $user?->id, $locale);
    }

    /**
     * Invalidate old OTPs for email and type
     */
    public function invalidateOtp(string $email, string $type): void
    {
        Otp::forEmail($email)
            ->forType($type)
            ->delete();
    }

    /**
     * Send OTP email
     */
    public function sendOtpEmail(Otp $otp, ?string $locale = null): void
    {
        Mail::to($otp->email)->send(new OtpMail($otp, $locale));
    }
}

