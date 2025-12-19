# 🔐 Change Password

API để đổi mật khẩu cho user đã đăng nhập.

## URL
`POST /api/auth/change-password`

## Method
`POST`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Authorization`: `Bearer {token}` (JWT token từ login)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

## Body

```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

## Request Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `current_password` | ✓ | String | Mật khẩu hiện tại của user |
| `password` | ✓ | String | Mật khẩu mới (tối thiểu 8 ký tự) |
| `password_confirmation` | ✓ | String | Xác nhận mật khẩu mới (phải khớp với `password`) |

### Chi tiết các trường

#### `current_password`
- **Bắt buộc**: Có
- **Kiểu**: String
- **Mô tả**: Mật khẩu hiện tại của user để xác thực
- **Validation**: 
  - Phải khớp với mật khẩu hiện tại trong database
  - Nếu không khớp → trả về lỗi `CURRENT_PASSWORD_INVALID`

#### `password`
- **Bắt buộc**: Có
- **Kiểu**: String
- **Mô tả**: Mật khẩu mới muốn đặt
- **Validation**: 
  - Tối thiểu 8 ký tự
  - Phải khớp với `password_confirmation`
  - Sẽ được hash trước khi lưu vào database

#### `password_confirmation`
- **Bắt buộc**: Có
- **Kiểu**: String
- **Mô tả**: Xác nhận mật khẩu mới
- **Validation**: 
  - Phải khớp với `password`
  - Nếu không khớp → trả về validation error

## Response Success

```json
{
  "success": true,
  "code": "PASSWORD_CHANGED_SUCCESS",
  "message": "Đổi mật khẩu thành công",
  "data": null
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`PASSWORD_CHANGED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Null | Không có dữ liệu trả về |

## Response Error

### Validation Error

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "current_password": "The current password field is required.",
    "password": "The password must be at least 8 characters.",
    "password_confirmation": "The password confirmation does not match."
  }
}
```

### Current Password Invalid

```json
{
  "success": false,
  "code": "CURRENT_PASSWORD_INVALID",
  "message": "Mật khẩu hiện tại không chính xác",
  "errors": {
    "current_password": "Mật khẩu hiện tại không chính xác"
  }
}
```

### Unauthorized

```json
{
  "success": false,
  "code": "UNAUTHORIZED",
  "message": "Chưa đăng nhập",
  "errors": null
}
```

### Server Error

```json
{
  "success": false,
  "code": "PASSWORD_CHANGE_FAILED",
  "message": "Đổi mật khẩu thất bại",
  "errors": {
    "error": "Error message details"
  }
}
```

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `PASSWORD_CHANGED_SUCCESS` | 200 | Đổi mật khẩu thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `CURRENT_PASSWORD_INVALID` | 400 | Mật khẩu hiện tại không chính xác |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập (thiếu hoặc token không hợp lệ) |
| `PASSWORD_CHANGE_FAILED` | 500 | Lỗi server khi đổi mật khẩu |

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`
- `token`: JWT token sau khi login

### Example Request
```
POST {{base_url}}/api/auth/change-password
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json

Body:
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

## Notes

- User phải đăng nhập trước khi có thể đổi mật khẩu
- Mật khẩu mới sẽ được hash bằng bcrypt trước khi lưu vào database
- Sau khi đổi mật khẩu thành công, user vẫn có thể tiếp tục sử dụng token hiện tại
- Nếu muốn bắt buộc đăng nhập lại sau khi đổi mật khẩu, có thể invalidate tất cả tokens của user (không implement trong API này)

