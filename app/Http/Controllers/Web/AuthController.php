<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\WebLoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to appropriate dashboard
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(WebLoginRequest $request)
    {
        $user = $this->authService->loginWeb(
            $request->email,
            $request->password
        );

        if (!$user) {
            return back()->withErrors([
                'email' => __('Invalid email or password'),
            ])->withInput($request->only('email'));
        }

        // Check if email is verified
        if (!$this->authService->checkEmailVerified($user)) {
            return back()->withErrors([
                'email' => __('errors.EMAIL_NOT_VERIFIED'),
            ])->withInput($request->only('email'));
        }

        // Login user with session
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended($this->getDashboardUrl($user));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', __('Logout successful'));
    }

    /**
     * Get dashboard URL based on user role
     */
    protected function getDashboardUrl($user): string
    {
        // Ensure role is loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $roleSlug = $user->role?->slug;

        return match ($roleSlug) {
            'admin' => '/admin/dashboard',
            'partner' => '/partner/dashboard',
            'customer' => '/user/dashboard',
            default => '/login',
        };
    }

    /**
     * Redirect to dashboard based on user role
     */
    protected function redirectToDashboard($user)
    {
        return redirect($this->getDashboardUrl($user));
    }
}

