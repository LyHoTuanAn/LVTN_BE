<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Send password reset token
     */
    public function sendResetToken(string $email): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // TODO: Send email with reset token
        // Mail::to($user->email)->send(new PasswordResetMail($token));

        return true;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return false;
        }

        // Check if token is expired (24 hours)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            return false;
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }
}

