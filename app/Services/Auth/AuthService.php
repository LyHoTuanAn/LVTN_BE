<?php

namespace App\Services\Auth;

use App\Models\RefreshToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected OtpService $otpService;
    protected int $accessTokenTtlMinutes = 60; // 1 hour
    protected int $refreshTokenTtlDays = 30; // 30 days

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

        // Generate access token (short-lived)
        $accessToken = JWTAuth::customClaims([
            'exp' => now()->addMinutes($this->accessTokenTtlMinutes)->timestamp,
        ])->fromUser($user);

        // Generate refresh token (long-lived)
        $refreshToken = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => $this->accessTokenTtlMinutes * 60,
        ];
    }

    /**
     * Login user for web (session authentication, no JWT)
     * Returns User object if successful, null if failed
     */
    public function loginWeb(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // Load role relationship
        $user->load('role');

        return $user;
    }

    /**
     * Verify email OTP and set email_verified_at
     */
    public function verifyEmailOtp(string $email, string $otpCode): bool
    {
        $otp = $this->otpService->verifyOtp($email, $otpCode, 'register');

        if (!$otp) {
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
     * Logout user - invalidate access token and delete refresh tokens
     */
    public function logout(?string $refreshToken = null): void
    {
        // Invalidate current access token if available
        try {
            if (JWTAuth::getToken()) {
                JWTAuth::invalidate(JWTAuth::getToken());
            }
        } catch (\Exception $e) {
            // Token might already be invalid, ignore
        }

        // Delete refresh token if provided
        if ($refreshToken) {
            try {
                $payload = JWTAuth::setToken($refreshToken)->getPayload();
                $jti = $payload->get('jti');
                if ($jti) {
                    RefreshToken::where('jti', $jti)->delete();
                }
            } catch (JWTException $e) {
                // Invalid refresh token, ignore
            }
        }
    }

    /**
     * Refresh access token using refresh token (JWT)
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        try {
            // Parse and validate refresh token JWT
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            $jti = $payload->get('jti');
            $userId = $payload->get('sub');

            if (!$jti || !$userId) {
                return null;
            }

            // Check if refresh token exists in database and is valid
            $tokenRecord = RefreshToken::where('jti', $jti)
                ->where('user_id', $userId)
                ->valid()
                ->first();

            if (!$tokenRecord) {
                return null;
            }

            $user = $tokenRecord->user;

            if (!$user) {
                return null;
            }

            // Generate new access token
            $accessToken = JWTAuth::customClaims([
                'exp' => now()->addMinutes($this->accessTokenTtlMinutes)->timestamp,
            ])->fromUser($user);

            return [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => $this->accessTokenTtlMinutes * 60,
            ];
        } catch (JWTException $e) {
            // Invalid or expired refresh token
            return null;
        }
    }

    /**
     * Create a new refresh token (JWT) for user
     */
    protected function createRefreshToken(User $user): string
    {
        // Delete old refresh tokens for this user (optional: keep multiple devices)
        // RefreshToken::where('user_id', $user->id)->delete();

        // Generate JTI (JWT ID) for refresh token
        $jti = bin2hex(random_bytes(32)); // 64 character hex string

        // Generate refresh token as JWT with custom claims
        $refreshToken = JWTAuth::customClaims([
            'jti' => $jti,
            'type' => 'refresh',
            'exp' => now()->addDays($this->refreshTokenTtlDays)->timestamp,
        ])->fromUser($user);

        // Store JTI in database for revocation
        RefreshToken::create([
            'user_id' => $user->id,
            'jti' => $jti,
            'expires_at' => now()->addDays($this->refreshTokenTtlDays),
        ]);

        return $refreshToken;
    }
}


