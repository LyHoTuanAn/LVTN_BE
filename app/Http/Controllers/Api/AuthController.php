<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray()
            );
        }

        try {
            $user = $this->authService->register($validator->validated());
            $user->load(['role', 'avatar']);

            return $this->successResponse(
                'USER_CREATED_SUCCESS',
                new UserResource($user),
                null,
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray()
            );
        }

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
}
