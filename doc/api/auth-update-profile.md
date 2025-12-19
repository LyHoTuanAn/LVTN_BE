# 👤 Update Profile

API để cập nhật thông tin cá nhân của user đã đăng nhập.

## URL
`PUT /api/auth/me`

## Method
`PUT`

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
  "name": "Nguyễn Văn A",
  "avatar_id": 1,
  "phone": "0123456789",
  "date_of_birth": "1990-01-15",
  "gender": "male",
  "address": "123 Đường ABC, Quận 1, TP.HCM"
}
```

## Request Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `name` | ✗ | String | Họ và tên (tối đa 100 ký tự) |
| `avatar_id` | ✗ | Number | ID của ảnh đại diện trong bảng `media_files` |
| `phone` | ✗ | String | Số điện thoại (tối đa 20 ký tự) |
| `date_of_birth` | ✗ | String (Date) | Ngày sinh (format: YYYY-MM-DD, không được trong tương lai) |
| `gender` | ✗ | String | Giới tính: `male`, `female`, hoặc `other` |
| `address` | ✗ | String | Địa chỉ (tối đa 255 ký tự) |

### Chi tiết các trường

#### `name`
- **Bắt buộc**: Không
- **Kiểu**: String
- **Mô tả**: Họ và tên của user
- **Validation**: 
  - Tối đa 100 ký tự
  - Nếu không cung cấp, giữ nguyên giá trị hiện tại

#### `avatar_id`
- **Bắt buộc**: Không
- **Kiểu**: Number
- **Mô tả**: ID của ảnh đại diện trong bảng `media_files`
- **Validation**: 
  - Phải tồn tại trong bảng `media_files`
  - Có thể set `null` để xóa ảnh đại diện (gửi `null` trong request)

#### `phone`
- **Bắt buộc**: Không
- **Kiểu**: String
- **Mô tả**: Số điện thoại của user
- **Validation**: 
  - Tối đa 20 ký tự
  - Nếu không cung cấp, giữ nguyên giá trị hiện tại

#### `date_of_birth`
- **Bắt buộc**: Không
- **Kiểu**: String (Date)
- **Mô tả**: Ngày sinh của user
- **Validation**: 
  - Format: `YYYY-MM-DD` (ví dụ: `1990-01-15`)
  - Không được trong tương lai (phải trước ngày hiện tại)
  - Nếu không cung cấp, giữ nguyên giá trị hiện tại

#### `gender`
- **Bắt buộc**: Không
- **Kiểu**: String (Enum)
- **Mô tả**: Giới tính của user
- **Validation**: 
  - Chỉ chấp nhận: `male`, `female`, hoặc `other`
  - Nếu không cung cấp, giữ nguyên giá trị hiện tại

#### `address`
- **Bắt buộc**: Không
- **Kiểu**: String
- **Mô tả**: Địa chỉ của user
- **Validation**: 
  - Tối đa 255 ký tự
  - Nếu không cung cấp, giữ nguyên giá trị hiện tại

## Response Success

```json
{
  "success": true,
  "code": "PROFILE_UPDATED_SUCCESS",
  "message": "Cập nhật thông tin cá nhân thành công",
  "data": {
    "id": 10,
    "name": "Nguyễn Văn A",
    "email": "user@example.com",
    "phone": "0123456789",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "date_of_birth": "1990-01-15",
    "gender": "male",
    "role": {
      "id": 3,
      "name": "Customer",
      "slug": "customer"
    },
    "avatar": {
      "id": 1,
      "url": "https://example.com/avatars/user.jpg",
      "type": "image"
    },
    "email_verified_at": "2024-01-15 10:30:00",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 15:45:00"
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`PROFILE_UPDATED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Thông tin user đã được cập nhật |
| `data.id` | ✓ | Number | ID của user |
| `data.name` | ✓ | String | Họ và tên |
| `data.email` | ✓ | String | Email (không thể thay đổi) |
| `data.phone` | ✗ | String | Số điện thoại |
| `data.address` | ✗ | String | Địa chỉ |
| `data.date_of_birth` | ✗ | String | Ngày sinh (format: YYYY-MM-DD) |
| `data.gender` | ✗ | String | Giới tính: `male`, `female`, hoặc `other` |
| `data.role` | ✓ | Object | Thông tin role của user |
| `data.avatar` | ✗ | Object | Thông tin ảnh đại diện |
| `data.email_verified_at` | ✗ | String | Thời gian xác minh email (format: YYYY-MM-DD HH:mm:ss) |
| `data.created_at` | ✓ | String | Thời gian tạo (format: YYYY-MM-DD HH:mm:ss) |
| `data.updated_at` | ✓ | String | Thời gian cập nhật (format: YYYY-MM-DD HH:mm:ss) |

## Response Error

### Validation Error

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "name": "The name may not be greater than 100 characters.",
    "avatar_id": "The selected avatar id is invalid.",
    "date_of_birth": "The date of birth must be a date before today.",
    "gender": "The selected gender is invalid."
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
  "code": "PROFILE_UPDATE_FAILED",
  "message": "Cập nhật thông tin cá nhân thất bại",
  "errors": {
    "error": "Error message details"
  }
}
```

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `PROFILE_UPDATED_SUCCESS` | 200 | Cập nhật thông tin cá nhân thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập (thiếu hoặc token không hợp lệ) |
| `PROFILE_UPDATE_FAILED` | 500 | Lỗi server khi cập nhật profile |

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`
- `token`: JWT token sau khi login

### Example Request
```
PUT {{base_url}}/api/auth/me
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json

Body:
{
  "name": "Nguyễn Văn A",
  "avatar_id": 1,
  "phone": "0123456789",
  "date_of_birth": "1990-01-15",
  "gender": "male",
  "address": "123 Đường ABC, Quận 1, TP.HCM"
}
```

## Notes

- User phải đăng nhập trước khi có thể cập nhật profile
- Tất cả các trường đều là optional - chỉ cập nhật các trường được gửi trong request
- Email không thể thay đổi qua API này (phải dùng API riêng nếu cần)
- `avatar_id` phải tồn tại trong bảng `media_files` trước khi sử dụng
- `date_of_birth` không được trong tương lai
- `gender` chỉ chấp nhận: `male`, `female`, hoặc `other`
- Response trả về thông tin user đã được cập nhật với đầy đủ relationships (role, avatar)

