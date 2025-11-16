<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Auth\AuthService;
use App\Services\Auth\OtpService;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;
    protected OtpService $otpService;
    protected PasswordResetService $passwordResetService;

    public function __construct(
        AuthService $authService,
        OtpService $otpService,
        PasswordResetService $passwordResetService
    ) {
        $this->authService = $authService;
        $this->otpService = $otpService;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $user->load(['role', 'avatar']);

            return $this->successResponse(
                'OTP_SENT_SUCCESS',
                new UserResource($user),
                __('success.OTP_SENT_SUCCESS'),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'USER_CREATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$result) {
            return $this->errorResponse(
                'LOGIN_FAILED',
                ['credentials' => __('errors.LOGIN_FAILED')]
            );
        }

        // Check if email verification is required
        if (isset($result['requires_verification']) && $result['requires_verification']) {
            $user = $result['user'];
            $user->load(['role', 'avatar']);

            return $this->errorResponse(
                'EMAIL_NOT_VERIFIED',
                ['email' => $result['message']],
                __('errors.EMAIL_NOT_VERIFIED'),
                403
            );
        }

        $user = $result['user'];
        $user->load(['role', 'avatar']);

        return $this->successResponse(
            'LOGIN_SUCCESS',
            [
                'user' => new UserResource($user),
                'token' => $result['token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
            ]
        );
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->successResponse('LOGOUT_SUCCESS');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'LOGOUT_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $result = $this->authService->refreshToken();
            return $this->successResponse(
                'TOKEN_REFRESHED_SUCCESS',
                $result
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'TOKEN_REFRESH_FAILED',
                ['error' => $e->getMessage()],
                null,
                401
            );
        }
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        $user = auth()->user();
        $user->load(['role', 'avatar']);

        return $this->successResponse(
            'USER_FETCHED_SUCCESS',
            new UserResource($user)
        );
    }

    /**
     * Verify OTP for register/login
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            if ($request->type === 'register') {
                $verified = $this->authService->verifyEmailOtp(
                    $request->email,
                    $request->otp_code
                );
            } else {
                $verified = $this->passwordResetService->verifyPasswordResetOtp(
                    $request->email,
                    $request->otp_code
                );
            }

            if (!$verified) {
                return $this->errorResponse(
                    'OTP_INVALID',
                    ['otp_code' => __('errors.OTP_INVALID')],
                    __('errors.OTP_INVALID'),
                    400
                );
            }

            return $this->successResponse(
                'OTP_VERIFIED_SUCCESS',
                null,
                __('success.OTP_VERIFIED_SUCCESS')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'OTP_VERIFICATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            // Get locale from current app locale (set by LanguageMiddleware)
            $locale = app()->getLocale();
            $this->otpService->resendOtp($request->email, $request->type, $locale);

            return $this->successResponse(
                'OTP_RESENT_SUCCESS',
                null,
                __('success.OTP_RESENT_SUCCESS')
            );
        } catch (\Exception $e) {
            if ($e->getMessage() === __('errors.RATE_LIMIT_EXCEEDED')) {
                return $this->errorResponse(
                    'RATE_LIMIT_EXCEEDED',
                    ['email' => $e->getMessage()],
                    __('errors.RATE_LIMIT_EXCEEDED'),
                    429
                );
            }

            return $this->errorResponse(
                'OTP_RESEND_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }

    /**
     * Request password reset OTP
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $result = $this->passwordResetService->requestPasswordReset($request->email);

            if (!$result['exists']) {
                return $this->errorResponse(
                    'EMAIL_NOT_FOUND',
                    ['email' => __('errors.EMAIL_NOT_FOUND')],
                    __('errors.EMAIL_NOT_FOUND'),
                    404
                );
            }

            return $this->successResponse(
                'OTP_SENT_SUCCESS',
                ['email' => $request->email, 'otp_sent' => true],
                __('success.OTP_SENT_SUCCESS')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'PASSWORD_RESET_FAILED',
                ['error' => $e->getMessage()],
                __('errors.PASSWORD_RESET_FAILED'),
                500
            );
        }
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordResetOtp(VerifyOtpRequest $request)
    {
        try {
            $verified = $this->passwordResetService->verifyPasswordResetOtp(
                $request->email,
                $request->otp_code
            );

            if (!$verified) {
                return $this->errorResponse(
                    'OTP_INVALID',
                    ['otp_code' => __('errors.OTP_INVALID')],
                    __('errors.OTP_INVALID'),
                    400
                );
            }

            return $this->successResponse(
                'OTP_VERIFIED_SUCCESS',
                null,
                __('success.OTP_VERIFIED_SUCCESS')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'OTP_VERIFICATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                500
            );
        }
    }

    /**
     * Reset password after OTP verified
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            // Verify OTP first
            $verified = $this->passwordResetService->verifyPasswordResetOtp(
                $request->email,
                $request->otp_code
            );

            if (!$verified) {
                return $this->errorResponse(
                    'OTP_INVALID',
                    ['otp_code' => __('errors.OTP_INVALID')],
                    __('errors.OTP_INVALID'),
                    400
                );
            }

            // Reset password
            $user = $this->passwordResetService->resetPassword(
                $request->email,
                $request->password
            );
            $user->load(['role', 'avatar']);

            return $this->successResponse(
                'PASSWORD_RESET_SUCCESS',
                new UserResource($user),
                __('success.PASSWORD_RESET_SUCCESS')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'PASSWORD_RESET_FAILED',
                ['error' => $e->getMessage()],
                __('errors.PASSWORD_RESET_FAILED'),
                500
            );
        }
    }
}
