# 🔐 Authentication Flow

Tài liệu này mô tả các API endpoints cho flow xác thực với OTP (One-Time Password) qua email.

## Tổng Quan Flow

### 1. Register Flow
1. User đăng ký → Nhận OTP qua email
2. User verify OTP → Email được xác minh
3. User có thể đăng nhập

### 2. Login Flow
1. User đăng nhập với email/password
2. Nếu email chưa verify → Yêu cầu verify OTP
3. Nếu email đã verify → Nhận JWT token

### 3. Forgot Password Flow
1. User yêu cầu đặt lại mật khẩu → Nhận OTP qua email
2. User verify OTP → Xác minh thành công
3. User đặt lại mật khẩu mới

---

## 1. Register (Đăng Ký)

### URL
`POST /api/auth/register`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "name": "Nguyễn Văn A",
  "email": "nguyenvana@example.com",
  "password": "password123",
  "phone": "0123456789",
  "address": "123 Đường ABC, Quận 1, TP.HCM"
}
```

### Request Parameters

| Trường     | Bắt buộc | Kiểu dữ liệu | Miêu tả                           |
|------------|----------|--------------|-----------------------------------|
| `name`     | ✓        | String       | Tên người dùng (tối đa 100 ký tự) |
| `email`    | ✓        | String       | Email đăng ký (phải unique)       |
| `password` | ✓        | String       | Mật khẩu (tối thiểu 8 ký tự)      |
| `phone`    | ✗        | String       | Số điện thoại (tối đa 20 ký tự)   |
| `address`  | ✗        | String       | Địa chỉ (tối đa 255 ký tự)        |

### Response Success

```json
{
  "success": true,
  "code": "OTP_SENT_SUCCESS",
  "message": "Mã OTP đã được gửi đến email của bạn",
  "data": {
    "id": 10,
    "name": "Nguyễn Văn A",
    "email": "nguyenvana@example.com",
    "phone": "0123456789",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "email_verified_at": null,
    "role": {
      "id": 3,
      "name": "Customer",
      "slug": "customer"
    },
    "created_at": "2024-01-15 10:30:00"
  }
}
```

### Response Error

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "email": "The email has already been taken",
    "password": "The password must be at least 8 characters."
  }
}
```

### Success Codes

| Code               | HTTP Status | Mô tả                               |
|--------------------|-------------|-------------------------------------|
| `OTP_SENT_SUCCESS` | 201         | Đăng ký thành công, OTP đã được gửi |

### Error Codes

| Code                   | HTTP Status | Mô tả                           |
|------------------------|-------------|---------------------------------|
| `VALIDATION_ERROR`     | 422         | Dữ liệu validation không hợp lệ |
| `EMAIL_EXISTS`         | 422         | Email đã được sử dụng           |
| `USER_CREATION_FAILED` | 500         | Lỗi khi tạo user                |

---

## 2. Verify OTP (Xác Minh OTP)

### URL
`POST /api/auth/verify-otp`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "email": "nguyenvana@example.com",
  "otp_code": "123456",
  "type": "register"
}
```

### Request Parameters

| Trường     | Bắt buộc | Kiểu dữ liệu | Miêu tả                                     |
|------------|----------|--------------|---------------------------------------------|
| `email`    | ✓        | String       | Email đã đăng ký                            |
| `otp_code` | ✓        | String       | Mã OTP 6 chữ số (0-9)                       |
| `type`     | ✓        | String       | Loại OTP: `register` hoặc `forgot_password` |

> **Ghi chú:**  
> - `type = register`: xác minh email cho tài khoản mới.  
> - `type = forgot_password`: xác minh OTP cho flow đặt lại mật khẩu.

### Response Success

```json
{
  "success": true,
  "code": "OTP_VERIFIED_SUCCESS",
  "message": "Xác minh mã OTP thành công",
  "data": null
}
```

#### Response Success (Forgot Password)

```json
{
  "success": true,
  "code": "OTP_VERIFIED_SUCCESS",
  "message": "Xác minh mã OTP thành công",
  "data": {
    "reset_token": "C6d1L9...xYZ12",
    "expires_in": 600
  }
}
```

> `reset_token` có hiệu lực 10 phút. Token này phải được gửi trong API `reset-password`.

### Response Error

```json
{
  "success": false,
  "code": "RESET_TOKEN_INVALID",
  "message": "Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn",
  "errors": {
    "reset_token": "Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn"
  }
}
```

### Success Codes

| Code                   | HTTP Status | Mô tả                   |
|------------------------|-------------|-------------------------|
| `OTP_VERIFIED_SUCCESS` | 200         | Xác minh OTP thành công |

### Error Codes

| Code                   | HTTP Status | Mô tả                               |
|------------------------|-------------|-------------------------------------|
| `OTP_INVALID`          | 400         | Mã OTP không hợp lệ hoặc không khớp |
| `OTP_EXPIRED`          | 400         | Mã OTP đã hết hạn (5 phút)          |
| `OTP_ALREADY_VERIFIED` | 400         | Mã OTP đã được xác minh             |
| `OTP_NOT_FOUND`        | 404         | Không tìm thấy mã OTP               |
| `VALIDATION_ERROR`     | 422         | Dữ liệu validation không hợp lệ     |

---

## 3. Resend OTP (Gửi Lại OTP)

### URL
`POST /api/auth/resend-otp`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "email": "nguyenvana@example.com",
  "type": "register"
}
```

### Request Parameters

| Trường  | Bắt buộc | Kiểu dữ liệu | Miêu tả                                     |
|---------|----------|--------------|---------------------------------------------|
| `email` | ✓        | String       | Email cần gửi lại OTP                       |
| `type`  | ✓        | String       | Loại OTP: `register` hoặc `forgot_password` |

### Response Success

```json
{
  "success": true,
  "code": "OTP_RESENT_SUCCESS",
  "message": "Mã OTP đã được gửi lại đến email của bạn",
  "data": null
}
```

### Response Error

```json
{
  "success": false,
  "code": "RATE_LIMIT_EXCEEDED",
  "message": "Vui lòng đợi 60 giây trước khi yêu cầu mã OTP mới",
  "errors": {
    "email": "Vui lòng đợi 60 giây trước khi yêu cầu mã OTP mới"
  }
}
```

### Success Codes

| Code                 | HTTP Status | Mô tả                  |
|----------------------|-------------|------------------------|
| `OTP_RESENT_SUCCESS` | 200         | Gửi lại OTP thành công |

### Error Codes

| Code                  | HTTP Status | Mô tả                                   |
|-----------------------|-------------|-----------------------------------------|
| `RATE_LIMIT_EXCEEDED` | 429         | Chưa đủ 60 giây kể từ lần gửi OTP trước |
| `VALIDATION_ERROR`    | 422         | Dữ liệu validation không hợp lệ         |

### Notes

- Rate limiting: Phải đợi ít nhất 60 giây giữa các lần gửi lại OTP
- OTP cũ sẽ tự động bị vô hiệu hóa khi tạo OTP mới

---

## 4. Login (Đăng Nhập)

### URL
`POST /api/auth/login`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "email": "nguyenvana@example.com",
  "password": "password123"
}
```

### Request Parameters

| Trường     | Bắt buộc | Kiểu dữ liệu | Miêu tả         |
|------------|----------|--------------|-----------------|
| `email`    | ✓        | String       | Email đăng nhập |
| `password` | ✓        | String       | Mật khẩu        |

### Response Success

```json
{
  "success": true,
  "code": "LOGIN_SUCCESS",
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "id": 10,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com",
      "email_verified_at": "2024-01-15 10:35:00",
      "role": {
        "id": 3,
        "name": "Customer",
        "slug": "customer"
      }
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "C6d1L9xYZ12abc123...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### Response Fields

| Trường               | Bắt buộc | Kiểu dữ liệu | Mô tả                                                |
|----------------------|----------|--------------|------------------------------------------------------|
| `success`            | ✓        | Boolean      | Trạng thái thành công (`true`)                       |
| `code`               | ✓        | String       | Mã response (`LOGIN_SUCCESS`)                        |
| `message`            | ✓        | String       | Thông báo theo ngôn ngữ client yêu cầu               |
| `data`               | ✓        | Object       | Dữ liệu đăng nhập                                    |
| `data.user`          | ✓        | Object       | Thông tin user                                       |
| `data.access_token`  | ✓        | String       | JWT access token (hết hạn sau 1 giờ)                 |
| `data.refresh_token` | ✓        | String       | Refresh token (hết hạn sau 30 ngày)                  |
| `data.token_type`    | ✓        | String       | Loại token (`bearer`)                                |
| `data.expires_in`    | ✓        | Number       | Thời gian hết hạn access token (giây) - 3600 (1 giờ) |

### Response Error - Email Chưa Verify

```json
{
  "success": false,
  "code": "EMAIL_NOT_VERIFIED",
  "message": "Email chưa được xác minh",
  "errors": {
    "email": "Email chưa được xác minh"
  }
}
```

### Response Error - Sai Thông Tin

```json
{
  "success": false,
  "code": "LOGIN_FAILED",
  "message": "Thông tin đăng nhập không chính xác",
  "errors": {
    "credentials": "Thông tin đăng nhập không chính xác"
  }
}
```

### Success Codes

| Code            | HTTP Status | Mô tả                |
|-----------------|-------------|----------------------|
| `LOGIN_SUCCESS` | 200         | Đăng nhập thành công |

### Error Codes

| Code                 | HTTP Status | Mô tả                                     |
|----------------------|-------------|-------------------------------------------|
| `LOGIN_FAILED`       | 401         | Email hoặc mật khẩu không chính xác       |
| `EMAIL_NOT_VERIFIED` | 403         | Email chưa được xác minh (cần verify OTP) |
| `VALIDATION_ERROR`   | 422         | Dữ liệu validation không hợp lệ           |

### Notes

- Nếu email chưa verify, user cần verify OTP trước khi đăng nhập
- Tất cả user (customer, admin, partner) đều phải verify email - không có ngoại lệ

---

## 5. Forgot Password (Quên Mật Khẩu)

### URL
`POST /api/auth/forgot-password`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "email": "nguyenvana@example.com"
}
```

### Request Parameters

| Trường  | Bắt buộc | Kiểu dữ liệu | Miêu tả                    |
|---------|----------|--------------|----------------------------|
| `email` | ✓        | String       | Email cần đặt lại mật khẩu |

### Response Success

```json
{
  "success": true,
  "code": "OTP_SENT_SUCCESS",
  "message": "Mã OTP đã được gửi đến email của bạn",
  "data": {
    "email": "nguyenvana@example.com",
    "otp_sent": true
  }
}
```

### Response Error

#### Email Not Found

```json
{
  "success": false,
  "code": "EMAIL_NOT_FOUND",
  "message": "Email không tồn tại",
  "errors": {
    "email": "Email không tồn tại"
  }
}
```

#### Validation Error

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "email": "The email field is required."
  }
}
```

### Response Fields

| Trường          | Bắt buộc | Kiểu dữ liệu | Mô tả                                  |
|-----------------|----------|--------------|----------------------------------------|
| `success`       | ✓        | Boolean      | Trạng thái thành công (`true`)         |
| `code`          | ✓        | String       | Mã response (`OTP_SENT_SUCCESS`)       |
| `message`       | ✓        | String       | Thông báo theo ngôn ngữ client yêu cầu |
| `data`          | ✓        | Object       | Dữ liệu trả về                         |
| `data.email`    | ✓        | String       | Email đã gửi OTP                       |
| `data.otp_sent` | ✓        | Boolean      | OTP đã được gửi (`true`)               |

### Success Codes

| Code               | HTTP Status | Mô tả                     |
|--------------------|-------------|---------------------------|
| `OTP_SENT_SUCCESS` | 200         | OTP đã được gửi đến email |

### Error Codes

| Code                    | HTTP Status | Mô tả                              |
|-------------------------|-------------|------------------------------------|
| `EMAIL_NOT_FOUND`       | 404         | Email không tồn tại trong hệ thống |
| `VALIDATION_ERROR`      | 422         | Dữ liệu validation không hợp lệ    |
| `PASSWORD_RESET_FAILED` | 500         | Lỗi khi gửi OTP                    |

### Notes

- API sẽ trả về lỗi `EMAIL_NOT_FOUND` nếu email không tồn tại trong hệ thống
- OTP sẽ được gửi qua email nếu email tồn tại

---

## 6. Reset Password (Đặt Lại Mật Khẩu)

### URL
`POST /api/auth/reset-password`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

### Body

```json
{
  "email": "nguyenvana@example.com",
  "reset_token": "C6d1L9...xYZ12",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Request Parameters

| Trường                  | Bắt buộc | Kiểu dữ liệu | Miêu tả                                        |
|-------------------------|----------|--------------|------------------------------------------------|
| `email`                 | ✓        | String       | Email cần đặt lại mật khẩu                     |
| `reset_token`           | ✓        | String       | Token nhận được sau khi verify OTP (10 phút)   |
| `password`              | ✓        | String       | Mật khẩu mới (tối thiểu 8 ký tự)               |
| `password_confirmation` | ✓        | String       | Xác nhận mật khẩu mới (phải khớp với password) |

### Response Success

```json
{
  "success": true,
  "code": "PASSWORD_RESET_SUCCESS",
  "message": "Đặt lại mật khẩu thành công",
  "data": {
    "id": 10,
    "name": "Nguyễn Văn A",
    "email": "nguyenvana@example.com",
    "role": {
      "id": 3,
      "name": "Customer",
      "slug": "customer"
    }
  }
}
```

### Response Error

```json
{
  "success": false,
  "code": "RESET_TOKEN_INVALID",
  "message": "Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn",
  "errors": {
    "reset_token": "Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn"
  }
}
```

### Success Codes

| Code                     | HTTP Status | Mô tả                       |
|--------------------------|-------------|-----------------------------|
| `PASSWORD_RESET_SUCCESS` | 200         | Đặt lại mật khẩu thành công |

### Error Codes

| Code                    | HTTP Status | Mô tả                                               |
|-------------------------|-------------|-----------------------------------------------------|
| `RESET_TOKEN_INVALID`   | 400         | Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn |
| `VALIDATION_ERROR`      | 422         | Dữ liệu validation không hợp lệ                     |
| `PASSWORD_RESET_FAILED` | 500         | Lỗi khi đặt lại mật khẩu                            |

### Notes

- OTP phải được verify trước khi đặt lại mật khẩu
- Sau khi đặt lại mật khẩu thành công, user có thể đăng nhập với mật khẩu mới

---

## 7. Refresh Token (Làm Mới Access Token)

### URL
`POST /api/auth/refresh`

### Method
`POST`

### Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

> **Lưu ý:** Endpoint này là **public route**, không cần `Authorization` header. Sử dụng `refresh_token` từ body.

### Body

```json
{
  "refresh_token": "C6d1L9xYZ12abc123..."
}
```

### Request Parameters

| Trường          | Bắt buộc | Kiểu dữ liệu | Mô tả                                 |
|-----------------|----------|--------------|---------------------------------------|
| `refresh_token` | ✓        | String       | JWT refresh token nhận được khi login |

### Response Success

```json
{
  "success": true,
  "code": "TOKEN_REFRESHED_SUCCESS",
  "message": "Làm mới token thành công",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### Response Fields

| Trường              | Bắt buộc | Kiểu dữ liệu | Mô tả                                    |
|---------------------|----------|--------------|------------------------------------------|
| `success`           | ✓        | Boolean      | Trạng thái thành công (`true`)           |
| `code`              | ✓        | String       | Mã response (`TOKEN_REFRESHED_SUCCESS`)  |
| `message`           | ✓        | String       | Thông báo theo ngôn ngữ client yêu cầu   |
| `data`              | ✓        | Object       | Dữ liệu access token mới                 |
| `data.access_token` | ✓        | String       | JWT access token mới (hết hạn sau 1 giờ) |
| `data.token_type`   | ✓        | String       | Loại token (`bearer`)                    |
| `data.expires_in`   | ✓        | Number       | Thời gian hết hạn (giây) - 3600 (1 giờ)  |

### Response Error

#### Token Invalid or Expired

```json
{
  "success": false,
  "code": "TOKEN_REFRESH_FAILED",
  "message": "Làm mới token thất bại",
  "errors": {
    "error": "Token has expired and can no longer be refreshed"
  }
}
```

#### Unauthorized

```json
{
  "success": false,
  "code": "UNAUTHORIZED",
  "message": "Chưa đăng nhập",
  "errors": {
    "error": "Token not provided"
  }
}
```

### Success Codes

| Code                      | HTTP Status | Mô tả                    |
|---------------------------|-------------|--------------------------|
| `TOKEN_REFRESHED_SUCCESS` | 200         | Làm mới token thành công |

### Error Codes

| Code                   | HTTP Status | Mô tả                                                          |
|------------------------|-------------|----------------------------------------------------------------|
| `TOKEN_REFRESH_FAILED` | 401         | Token không thể làm mới (đã hết hạn quá lâu hoặc không hợp lệ) |
| `UNAUTHORIZED`         | 401         | Chưa đăng nhập hoặc token không hợp lệ                         |

### Notes

- **Access Token Expiration**: Access token có thời gian hết hạn là **1 giờ (3600 giây)**
- **Refresh Token Expiration**: Refresh token có thời gian hết hạn là **30 ngày**
- **Refresh Token Usage**: Khi access token hết hạn, client sử dụng `refresh_token` để lấy `access_token` mới mà **không cần đăng nhập lại**
- **Refresh Token Security**: Refresh token là JWT token, được lưu JTI (JWT ID) trong database để có thể revoke khi cần
- **Sau khi refresh**: Access token mới được tạo, refresh token vẫn giữ nguyên (có thể sử dụng lại)
- **Best Practice**: 
  - Client nên tự động refresh access token trước khi hết hạn (ví dụ: refresh khi còn 5 phút)
  - Lưu trữ refresh token an toàn (ví dụ: secure storage trên mobile, httpOnly cookie trên web)
  - Khi logout, nên gửi `refresh_token` để xóa khỏi database

### Flow Xử Lý Token Hết Hạn

```
1. Client gọi API với access_token đã hết hạn
   → Server trả về 401 Unauthorized

2. Client tự động gọi POST /api/auth/refresh với refresh_token
   → Nếu refresh_token còn hợp lệ: Nhận access_token mới
   → Nếu refresh_token đã hết hạn: Phải đăng nhập lại

3. Client sử dụng access_token mới để retry request ban đầu
```

---

## Flow Diagram

```
Register Flow:
User → POST /api/auth/register → OTP sent to email
User → POST /api/auth/verify-otp (type: register) → Email verified
User → POST /api/auth/login → Receive access_token + refresh_token

Login Flow (Email not verified):
User → POST /api/auth/login → Error: EMAIL_NOT_VERIFIED
User → POST /api/auth/resend-otp (type: register) → OTP resent
User → POST /api/auth/verify-otp (type: register) → Email verified
User → POST /api/auth/login → Receive access_token + refresh_token

Token Refresh Flow:
User → API call with expired access_token → 401 Unauthorized
User → POST /api/auth/refresh (with refresh_token) → New access_token received
User → Retry original API call with new access_token → Success

Logout Flow:
User → POST /api/auth/logout (with refresh_token) → Access token invalidated + Refresh token deleted

Forgot Password Flow:
User → POST /api/auth/forgot-password → OTP sent to email
User → POST /api/auth/verify-otp (type: forgot_password) → Receive reset_token
User → POST /api/auth/reset-password (with reset_token) → Password reset successfully
```

---

## Lưu Ý Quan Trọng

1. **OTP Expiration**: OTP hết hạn sau 5 phút
2. **Rate Limiting**: Phải đợi 60 giây giữa các lần gửi lại OTP
3. **OTP Format**: 6 chữ số (0-9)
4. **Email Verification**: Bắt buộc cho tất cả user (customer, admin, partner) - không có ngoại lệ
5. **Forgot Password**: API forgot-password sẽ trả về lỗi `EMAIL_NOT_FOUND` nếu email không tồn tại trong hệ thống
6. **Reset Token**: Sau khi verify OTP (type: forgot_password), client phải sử dụng `reset_token` (hết hạn sau 10 phút) để gọi API `reset-password`
7. **Access Token & Refresh Token Pattern**: 
   - **Access Token**: JWT token ngắn hạn, hết hạn sau **1 giờ (3600 giây)**. Dùng để truy cập các API protected
   - **Refresh Token**: Token dài hạn, hết hạn sau **30 ngày**. Dùng để refresh access token mà không cần đăng nhập lại
   - Khi login thành công, client nhận cả `access_token` và `refresh_token`
8. **Refresh Token Flow**: Khi access token hết hạn, client gọi API `/api/auth/refresh` với `refresh_token` để nhận `access_token` mới
9. **Token Security**: Refresh token là JWT token, JTI (JWT ID) được lưu trong database để có thể revoke. Khi logout, nên gửi `refresh_token` để xóa JTI khỏi database
10. **Token Refresh Best Practice**: Client nên tự động refresh access token trước khi hết hạn (ví dụ: refresh khi còn 5 phút) để tránh gián đoạn trải nghiệm người dùng

---