<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = null; // User needs to verify email
        
        // Default role is customer
        if (!isset($data['role_id'])) {
            $customerRole = Role::where('slug', 'customer')->first();
            $data['role_id'] = $customerRole?->id;
        }

        $user = User::create($data);

        // Generate and send OTP with current locale
        $locale = app()->getLocale();
        $this->otpService->generateOtp($user->email, 'register', $user->id, $locale);

        return $user;
    }

    /**
     * Login user and return token
     */
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // Check if email is verified
        if (!$this->checkEmailVerified($user)) {
            return [
                'user' => $user,
                'requires_verification' => true,
                'message' => __('errors.EMAIL_NOT_VERIFIED'),
            ];
        }

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ];
    }

    /**
     * Verify email OTP and set email_verified_at
     */
    public function verifyEmailOtp(string $email, string $otpCode): bool
    {
        if (!$this->otpService->verifyOtp($email, $otpCode, 'register')) {
            return false;
        }

        // Set email_verified_at
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['email_verified_at' => now()]);
        }

        return true;
    }

    /**
     * Check if user email is verified
     */
    public function checkEmailVerified(User $user): bool
    {
        // All users must verify email (no exceptions)
        return $user->email_verified_at !== null;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh token
     */
    public function refreshToken(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ];
    }
}


