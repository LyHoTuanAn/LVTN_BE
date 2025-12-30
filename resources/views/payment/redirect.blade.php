<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 400px;
            margin: 20px;
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .success .icon { color: #22c55e; }
        .failed .icon { color: #ef4444; }
        .error .icon { color: #f59e0b; }
        h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #1f2937;
        }
        p {
            color: #6b7280;
            margin: 0 0 20px 0;
        }
        .booking-code {
            background: #f3f4f6;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 18px;
            color: #374151;
        }
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #9ca3af;
        }
        .redirect-text {
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container {{ $status }}">
        @if($status === 'success')
            <div class="icon">✅</div>
            <h1>Thanh toán thành công!</h1>
            <p>Cảm ơn bạn đã đặt vé</p>
        @elseif($status === 'failed')
            <div class="icon">❌</div>
            <h1>Thanh toán thất bại</h1>
            <p>Vui lòng thử lại</p>
        @else
            <div class="icon">⚠️</div>
            <h1>Có lỗi xảy ra</h1>
            <p>Không thể xác thực giao dịch</p>
        @endif

        @if($bookingCode)
            <div class="booking-code">{{ $bookingCode }}</div>
        @endif

        @if($message)
            <p class="message">{{ $message }}</p>
        @endif

        <p class="redirect-text">Đang chuyển hướng về ứng dụng...</p>
    </div>

    <script>
        // Auto redirect về app sau 2 giây
        setTimeout(function() {
            window.location.href = "{{ $redirectUrl }}";
        }, 2000);
    </script>
</body>
</html>
