# 📰 [NEWS] - Xem chi tiết bài viết

## URL
`GET /api/news/{id}`

## Method
`GET`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Accept`: `application/json`

## Path Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `id` | ✓ | Number | ID của bài viết |

### Chi tiết các trường

#### `id`
- **Bắt buộc**: Có
- **Kiểu**: Number
- **Mô tả**: ID của bài viết muốn xem chi tiết
- **Validation**: 
  - Phải tồn tại trong bảng `news`
  - Bài viết phải có status là `published`
  - Bài viết chưa bị xóa (soft delete)

## Response Success

```json
{
  "success": true,
  "code": "NEWS_FETCHED_SUCCESS",
  "message": "Lấy thông tin bài viết thành công",
  "data": {
    "id": 1,
    "title": "Khởi động dự án điện ảnh mới",
    "slug": "khoi-dong-du-an-dien-anh-moi",
    "summary": "Dự án điện ảnh mới được công bố với ngân sách khủng...",
    "content": "<p>Nội dung đầy đủ của bài viết...</p>",
    "status": "published",
    "thumbnail": {
      "id": 5,
      "folder_id": 2,
      "user_id": 1,
      "file_name": "news-thumbnail.jpg",
      "file_path": "media/news/2025/01/news-thumbnail.jpg",
      "url": "http://localhost:8000/storage/media/news/2025/01/news-thumbnail.jpg",
      "mime_type": "image/jpeg",
      "size": 150000,
      "type": "image",
      "created_at": "2025-01-15 10:30:00",
      "updated_at": "2025-01-15 10:30:00"
    },
    "author": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com",
      "phone": "0123456789",
      "address": "123 Đường ABC, Quận XYZ, TP.HCM",
      "date_of_birth": "1990-01-01",
      "gender": "male",
      "avatar": null,
      "role": {
        "id": 1,
        "name": "admin",
        "display_name": "Administrator"
      },
      "email_verified_at": "2025-01-01 00:00:00",
      "created_at": "2025-01-01 00:00:00",
      "updated_at": "2025-01-15 10:30:00"
    },
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`NEWS_FETCHED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Dữ liệu bài viết |
| `data.id` | ✓ | Number | ID của bài viết |
| `data.title` | ✓ | String | Tiêu đề bài viết (theo locale: `title_en` hoặc `title_vi`) |
| `data.slug` | ✓ | String | Slug của bài viết (dùng cho URL) |
| `data.summary` | ✓ | String | Tóm tắt bài viết (theo locale: `summary_en` hoặc `summary_vi`) |
| `data.content` | ✓ | String | Nội dung đầy đủ của bài viết (theo locale: `content_en` hoặc `content_vi`) |
| `data.status` | ✓ | String | Trạng thái: `draft`, `published` |
| `data.thumbnail` | ✗ | Object \| null | Ảnh thumbnail của bài viết (MediaFileResource) |
| `data.thumbnail.id` | ✓ | Number | ID của file media |
| `data.thumbnail.file_name` | ✓ | String | Tên file |
| `data.thumbnail.file_path` | ✓ | String | Đường dẫn file |
| `data.thumbnail.url` | ✓ | String | URL đầy đủ để truy cập file |
| `data.thumbnail.mime_type` | ✓ | String | Kiểu MIME của file |
| `data.thumbnail.size` | ✓ | Number | Dung lượng file (bytes) |
| `data.thumbnail.type` | ✓ | String | Loại file: `image`, `video` |
| `data.author` | ✓ | Object | Thông tin tác giả (UserResource) |
| `data.author.id` | ✓ | Number | ID của tác giả |
| `data.author.name` | ✓ | String | Tên tác giả |
| `data.author.email` | ✓ | String | Email tác giả |
| `data.author.phone` | ✗ | String \| null | Số điện thoại |
| `data.author.address` | ✗ | String \| null | Địa chỉ |
| `data.author.date_of_birth` | ✗ | String \| null | Ngày sinh (format: Y-m-d) |
| `data.author.gender` | ✗ | String \| null | Giới tính: `male`, `female`, `other` |
| `data.author.avatar` | ✗ | Object \| null | Avatar của tác giả (MediaFileResource) |
| `data.author.role` | ✗ | Object \| null | Vai trò của tác giả (RoleResource) |
| `data.created_at` | ✓ | String | Thời gian tạo (format: YYYY-MM-DD HH:mm:ss) |
| `data.updated_at` | ✓ | String | Thời gian cập nhật (format: YYYY-MM-DD HH:mm:ss) |

### Lưu ý về locale

- `title`, `summary`, và `content` sẽ tự động trả về theo ngôn ngữ được yêu cầu trong header `Language`:
  - `Language: vi` → Trả về `title_vi`, `summary_vi`, `content_vi`
  - `Language: en` → Trả về `title_en`, `summary_en`, `content_en`
  - Không có header hoặc giá trị khác → Mặc định tiếng Anh

## Response Error

### Not Found Error

```json
{
  "success": false,
  "code": "NEWS_NOT_FOUND",
  "message": "Không tìm thấy bài viết",
  "errors": {}
}
```

**Nguyên nhân:**
- ID bài viết không tồn tại
- Bài viết có status là `draft` (chỉ trả về bài viết đã `published`)
- Bài viết đã bị xóa (soft delete)

### Unauthorized Error

```json
{
  "success": false,
  "code": "INVALID_API_KEY",
  "message": "API key không hợp lệ",
  "errors": {}
}
```

**Nguyên nhân:**
- Thiếu header `X-Api-Key`
- API key không hợp lệ

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `NEWS_FETCHED_SUCCESS` | 200 | Lấy thông tin bài viết thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `NEWS_NOT_FOUND` | 404 | Không tìm thấy bài viết (ID không tồn tại, status draft, hoặc đã bị xóa) |
| `INVALID_API_KEY` | 401 | API key không hợp lệ hoặc thiếu |
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ (ví dụ: ID không phải số) |

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`

### Example Request

```
GET {{base_url}}/api/news/1
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

### Example Response (Vietnamese)

```json
{
  "success": true,
  "code": "NEWS_FETCHED_SUCCESS",
  "message": "Lấy thông tin bài viết thành công",
  "data": {
    "id": 1,
    "title": "Khởi động dự án điện ảnh mới",
    "slug": "khoi-dong-du-an-dien-anh-moi",
    "summary": "Dự án điện ảnh mới được công bố với ngân sách khủng...",
    "content": "<p>Nội dung đầy đủ của bài viết...</p>",
    "status": "published",
    "thumbnail": {
      "id": 5,
      "file_name": "news-thumbnail.jpg",
      "url": "http://localhost:8000/storage/media/news/2025/01/news-thumbnail.jpg",
      "mime_type": "image/jpeg",
      "size": 150000,
      "type": "image"
    },
    "author": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com"
    },
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

### Example Response (English)

```
GET {{base_url}}/api/news/1
Headers:
  X-Api-Key: {{api_key}}
  Language: en
  Accept: application/json
```

```json
{
  "success": true,
  "code": "NEWS_FETCHED_SUCCESS",
  "message": "News article fetched successfully",
  "data": {
    "id": 1,
    "title": "New Cinema Project Launched",
    "slug": "khoi-dong-du-an-dien-anh-moi",
    "summary": "A new cinema project announced with huge budget...",
    "content": "<p>Full content of the article...</p>",
    "status": "published",
    "thumbnail": {
      "id": 5,
      "file_name": "news-thumbnail.jpg",
      "url": "http://localhost:8000/storage/media/news/2025/01/news-thumbnail.jpg",
      "mime_type": "image/jpeg",
      "size": 150000,
      "type": "image"
    },
    "author": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com"
    },
    "created_at": "2025-01-15 10:30:00",
    "updated_at": "2025-01-15 10:30:00"
  }
}
```

## Notes

- API này chỉ trả về các bài viết có status là `published`
- Các bài viết có status là `draft` sẽ không được trả về (ngay cả khi ID tồn tại)
- Nội dung `title`, `summary`, và `content` tự động dịch theo header `Language`
- Nếu bài viết không có `thumbnail`, trường `thumbnail` sẽ là `null`
- Trường `author` luôn được trả về với đầy đủ thông tin (eager loaded)
- Slug của bài viết có thể dùng để tạo URL thân thiện với SEO
