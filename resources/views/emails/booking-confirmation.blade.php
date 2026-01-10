<!DOCTYPE html>
<html lang="{{ $locale ?? 'vi' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.Booking Confirmation') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin-bottom: 20px;
        }
        .booking-code {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .booking-code strong {
            color: #667eea;
            font-size: 20px;
            letter-spacing: 1px;
        }
        .details-section {
            margin: 25px 0;
        }
        .details-section h2 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        .detail-value {
            color: #333;
            flex: 1;
        }
        .seats {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 5px;
        }
        .seat-badge {
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
        }
        .total-price {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
            border: 2px dashed #667eea;
        }
        .total-price .label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .total-price .amount {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
        .qr-note {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .qr-note strong {
            color: #856404;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .content {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                min-width: auto;
                margin-bottom: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎬 Celes Cinema</h1>
            <p>{{ __('emails.Booking Confirmation') }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Success Icon -->
            <div class="success-icon">✅</div>

            <!-- Booking Code -->
            <div class="booking-code">
                <p style="margin: 0 0 5px; color: #666; font-size: 14px;">{{ __('emails.Booking Code') }}:</p>
                <strong>{{ $booking->code }}</strong>
            </div>

            <p style="font-size: 16px; color: #333; text-align: center;">
                {{ __('emails.Thank you for booking with us! Your tickets have been confirmed.') }}
            </p>

            <!-- Movie Details -->
            <div class="details-section">
                <h2>🎬 {{ __('emails.Movie Information') }}</h2>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Movie') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->movie->title ?? 'N/A' }}</div>
                </div>
                @if($booking->showtime->movie->duration)
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Duration') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->movie->duration }} {{ __('emails.minutes') }}</div>
                </div>
                @endif
            </div>

            <!-- Showtime Details -->
            <div class="details-section">
                <h2>🎟️ {{ __('emails.Showtime Information') }}</h2>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Cinema') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->room->cinema->name ?? 'N/A' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Room') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->room->name ?? 'N/A' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Date') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->date?->format('d/m/Y') ?? 'N/A' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Time') }}:</div>
                    <div class="detail-value">{{ $booking->showtime->start_time ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Seats -->
            <div class="details-section">
                <h2>💺 {{ __('emails.Selected Seats') }}</h2>
                <div class="seats">
                    @foreach($booking->seats as $seat)
                        <span class="seat-badge">{{ $seat->row }}{{ $seat->number }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Total Price -->
            <div class="total-price">
                <div class="label">{{ __('emails.Total Amount') }}</div>
                <div class="amount">{{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</div>
            </div>

            <!-- Payment Info -->
            <div class="details-section">
                <h2>💳 {{ __('emails.Payment Information') }}</h2>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Payment Method') }}:</div>
                    <div class="detail-value">{{ strtoupper($booking->payment_method ?? 'N/A') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Payment Status') }}:</div>
                    <div class="detail-value" style="color: #28a745; font-weight: 600;">✅ {{ __('emails.Paid') }}</div>
                </div>
                @if($booking->paid_at)
                <div class="detail-row">
                    <div class="detail-label">{{ __('emails.Paid At') }}:</div>
                    <div class="detail-value">{{ $booking->paid_at->format('d/m/Y H:i:s') }}</div>
                </div>
                @endif
            </div>

            <!-- QR Code Note -->
            <div class="qr-note">
                <strong>📱 {{ __('emails.Important') }}:</strong><br>
                {{ __('emails.Please show this booking code or QR code from your app at the cinema counter to collect your tickets.') }}
            </div>

            <p style="text-align: center; margin-top: 30px; font-size: 14px; color: #666;">
                {{ __('emails.We hope you enjoy the movie! 🍿') }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Celes Cinema</strong></p>
            <p>{{ __('emails.Thank you for choosing us!') }}</p>
            <p style="margin-top: 10px;">
                {{ __('emails.If you have any questions, please contact our support team.') }}
            </p>
        </div>
    </div>
</body>
</html>
