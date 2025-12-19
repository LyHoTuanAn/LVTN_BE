<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Hệ Thống Đặt Vé Xem Phim</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://img.icons8.com/clouds/100/web.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #e0e0e0;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #1a1a1a;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }

        h1 {
            color: #4a9eff;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 3px solid #4a9eff;
            padding-bottom: 15px;
        }

        .subtitle {
            text-align: center;
            color: #9d9dff;
            font-size: 1.2em;
            margin-bottom: 40px;
        }

        .doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .doc-card {
            background: #222222;
            border: 1px solid #333333;
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .doc-card:hover {
            transform: translateY(-5px);
            border-color: #4a9eff;
            box-shadow: 0 8px 24px rgba(74, 158, 255, 0.3);
        }

        .doc-icon {
            font-size: 3em;
            margin-bottom: 15px;
            text-align: center;
        }

        .doc-title {
            color: #4a9eff;
            font-size: 1.5em;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .doc-description {
            color: #d0d0d0;
            font-size: 0.95em;
            margin-bottom: 15px;
            line-height: 1.5;
            flex: 1;
        }

        .doc-meta {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #333333;
        }

        .doc-link {
            color: #9d9dff;
            font-size: 0.9em;
            text-decoration: none;
        }

        .doc-link:hover {
            color: #4a9eff;
        }

        .section-header {
            color: #6bc46b;
            font-size: 1.8em;
            margin-top: 40px;
            margin-bottom: 20px;
            padding: 15px;
            background: #252525;
            border-left: 4px solid #6bc46b;
            border-radius: 6px;
        }

        .info-box {
            background: #1e3a5f;
            border-left: 4px solid #4a9eff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }

            .doc-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 API Documentation</h1>
        <p class="subtitle">Hệ Thống Đặt Vé Xem Phim - Tài Liệu API</p>

        <div class="info-box">
            <strong>Chào mừng đến với trang tài liệu API!</strong><br>
            Chọn một tài liệu bên dưới để xem chi tiết các endpoint và cách sử dụng.
        </div>

        <h2 class="section-header">Tài Liệu Có Sẵn</h2>

        <div class="doc-grid">
            <a href="/docs/instructions" class="doc-card">
                <div class="doc-icon">📖</div>
                <div class="doc-title">Hướng Dẫn Phát Triển</div>
                <div class="doc-description">
                    Tài liệu hướng dẫn chi tiết về quy tắc phát triển dự án, cấu trúc thư mục, 
                    quy tắc viết code, API pattern, và các best practices.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a>

            <!-- <a href="/api-docs/example-booking-create" class="doc-card">
                <div class="doc-icon">🎫</div>
                <div class="doc-title">Booking Example</div>
                <div class="doc-description">
                    Ví dụ về API tạo booking. Tài liệu mẫu cho việc tạo đặt vé xem phim.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a> -->

            <a href="/api-docs/auth-otp" class="doc-card">
                <div class="doc-icon">🔐</div>
                <div class="doc-title">Authentication Flow</div>
                <div class="doc-description">
                    Tài liệu chi tiết về flow xác thực với OTP (One-Time Password) qua email. 
                    Bao gồm: Register, Login, Verify OTP, Forgot Password, Reset Password, và Refresh Token.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a>

            <a href="/api-docs/auth-change-password" class="doc-card">
                <div class="doc-icon">🔑</div>
                <div class="doc-title">Change Password</div>
                <div class="doc-description">
                    API để đổi mật khẩu cho user đã đăng nhập. Yêu cầu mật khẩu hiện tại để xác thực 
                    và mật khẩu mới với xác nhận. API này yêu cầu authentication token.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a>

            <a href="/api-docs/auth-update-profile" class="doc-card">
                <div class="doc-icon">👤</div>
                <div class="doc-title">Update Profile</div>
                <div class="doc-description">
                    API để cập nhật thông tin cá nhân của user đã đăng nhập. Cho phép cập nhật: 
                    họ tên, ảnh đại diện, số điện thoại, ngày sinh, giới tính, và địa chỉ. API này yêu cầu authentication token.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a>

            <a href="/api-docs/media-upload-image" class="doc-card">
                <div class="doc-icon">📸</div>
                <div class="doc-title">Upload Image</div>
                <div class="doc-description">
                    API để upload ảnh lên server. Ảnh sẽ được tự động chuyển đổi sang định dạng WebP để tối ưu dung lượng. 
                    Hỗ trợ các định dạng: jpeg, jpg, png, gif, webp. Kích thước tối đa: 10MB. API này yêu cầu authentication token.
                </div>
                <div class="doc-meta">
                    <span class="doc-link">Xem tài liệu →</span>
                </div>
            </a>
        </div>

        <div class="info-box" style="margin-top: 40px;">
            <strong>💡 Lưu ý:</strong><br>
            - Tất cả các API đều yêu cầu header <code>X-Api-Key</code> và <code>Language</code><br>
            - Xem tài liệu <a href="/docs/instructions" style="color: #4a9eff; text-decoration: underline;">Hướng Dẫn Phát Triển</a> để biết thêm chi tiết về quy tắc phát triển
        </div>
    </div>
</body>
</html>

