@php
    // Set locale for this email view
    $originalLocale = app()->getLocale();
    if (isset($locale)) {
        app()->setLocale($locale);
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your OTP Code') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #333; margin-top: 0;">{{ __('Your OTP Code') }}</h2>
        
        <p>{{ $type === 'register' ? __('Thank you for registering!') : __('You have requested to reset your password.') }}</p>
        
        <div style="background-color: #fff; padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0;">
            <p style="font-size: 14px; color: #666; margin: 0 0 10px 0;">{{ __('Your OTP code is:') }}</p>
            <h1 style="font-size: 32px; letter-spacing: 5px; color: #007bff; margin: 10px 0;">{{ $otpCode }}</h1>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            {{ __('This OTP code will expire in :minutes minutes.', ['minutes' => $expiresIn]) }}
        </p>
        
        <p style="color: #666; font-size: 14px;">
            {{ __('If you did not request this code, please ignore this email.') }}
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        
        <p style="color: #999; font-size: 12px; margin: 0;">
            {{ __('This email is sent automatically, please do not reply.') }}
        </p>
    </div>
</body>
</html>

@php
    // Restore original locale
    app()->setLocale($originalLocale);
@endphp

