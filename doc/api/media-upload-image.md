# 📸 Upload Image

API để upload ảnh lên server. Ảnh sẽ được tự động chuyển đổi sang định dạng WebP để tối ưu dung lượng.

## URL
`POST /api/media/upload-image`

## Method
`POST`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Authorization`: `Bearer {token}` (bắt buộc - cần đăng nhập)
- `Content-Type`: `multipart/form-data`
- `Accept`: `application/json`

## Body (Form Data)

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `image` | ✓ | File | File ảnh cần upload |
| `folder_id` | ✗ | Number | ID của thư mục media (nếu có) |

### Chi tiết các trường

#### `image`
- **Bắt buộc**: Có
- **Kiểu**: File (multipart/form-data)
- **Mô tả**: File ảnh cần upload
- **Định dạng hỗ trợ**: `jpeg`, `jpg`, `png`, `gif`, `webp`
- **Kích thước tối đa**: 10MB
- **Validation**: 
  - Phải là file ảnh hợp lệ
  - Định dạng: jpeg, jpg, png, gif, webp
  - Kích thước tối đa: 10MB

#### `folder_id`
- **Bắt buộc**: Không
- **Kiểu**: Number
- **Mô tả**: ID của thư mục media để lưu ảnh vào (nếu có)
- **Validation**: 
  - Nếu có, phải tồn tại trong bảng `media_folders`

## Request Example

### cURL
```bash
curl -X POST "http://localhost:8000/api/media/upload-image" \
  -H "X-Api-Key: your-api-key-here" \
  -H "Language: vi" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "image=@/path/to/image.jpg" \
  -F "folder_id=1"
```

### JavaScript (Fetch)
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('folder_id', 1);

fetch('http://localhost:8000/api/media/upload-image', {
  method: 'POST',
  headers: {
    'X-Api-Key': 'your-api-key-here',
    'Language': 'vi',
    'Authorization': 'Bearer {token}',
    'Accept': 'application/json'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## Response Success

```json
{
  "success": true,
  "code": "IMAGE_UPLOADED_SUCCESS",
  "message": "Tải ảnh lên thành công",
  "data": {
    "id": 123,
    "folder_id": 1,
    "user_id": 10,
    "file_name": "image_1705123456_abc123.webp",
    "file_path": "media/avatars/2024/01/image_1705123456_abc123.webp",
    "url": "http://localhost:8000/storage/media/avatars/2024/01/image_1705123456_abc123.webp",
    "mime_type": "image/webp",
    "size": 245678,
    "type": "image",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`IMAGE_UPLOADED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Dữ liệu file media đã upload |
| `data.id` | ✓ | Number | ID của media file |
| `data.folder_id` | ✗ | Number | ID của thư mục media (nếu có) |
| `data.user_id` | ✓ | Number | ID của user đã upload |
| `data.file_name` | ✓ | String | Tên file đã lưu |
| `data.file_path` | ✓ | String | Đường dẫn file trên server |
| `data.url` | ✓ | String | URL công khai để truy cập ảnh |
| `data.mime_type` | ✓ | String | MIME type của file (luôn là `image/webp`) |
| `data.size` | ✓ | Number | Kích thước file (bytes) |
| `data.type` | ✓ | String | Loại file (luôn là `image`) |
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
    "image": "The image field is required."
  }
}
```

### File Too Large

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "image": "The image may not be greater than 10240 kilobytes."
  }
}
```

### Invalid File Format

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "image": "The image must be a file of type: jpeg, jpg, png, gif, webp."
  }
```

### Upload Failed

```json
{
  "success": false,
  "code": "IMAGE_UPLOAD_FAILED",
  "message": "Tải ảnh lên thất bại",
  "errors": {
    "image": "Failed to convert image to WebP: ..."
  }
}
```

### Unauthorized

```json
{
  "success": false,
  "code": "UNAUTHORIZED",
  "message": "Chưa đăng nhập",
  "errors": {}
}
```

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `IMAGE_UPLOADED_SUCCESS` | 201 | Upload ảnh thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `IMAGE_UPLOAD_FAILED` | 400 | Upload ảnh thất bại (lỗi server) |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập |
| `FORBIDDEN` | 403 | Không có quyền truy cập |

## Notes

- **Ảnh tự động chuyển đổi sang WebP**: Tất cả ảnh upload sẽ được tự động chuyển đổi sang định dạng WebP để tối ưu dung lượng và tốc độ tải
- **Lưu trữ theo thư mục**: Ảnh được lưu theo cấu trúc `media/avatars/YYYY/MM/` để dễ quản lý
- **Tên file unique**: Tên file được tự động generate với format `{original_name}_{timestamp}_{uniqid}.webp` để tránh trùng lặp
- **URL công khai**: Sử dụng field `url` trong response để truy cập ảnh từ client
- **Kích thước tối đa**: 10MB
- **Định dạng hỗ trợ**: jpeg, jpg, png, gif, webp (tất cả sẽ được convert sang webp)
- **Authentication**: API này yêu cầu đăng nhập (JWT token)

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`
- `token`: JWT token sau khi login

### Example Request
```
POST {{base_url}}/api/media/upload-image
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Authorization: Bearer {{token}}
  Accept: application/json

Body (form-data):
  image: [Select File]
  folder_id: 1 (optional)
```

