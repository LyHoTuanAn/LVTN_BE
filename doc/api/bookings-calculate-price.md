# 💰 [BOOKING] - Tính Giá Vé

## URL
`POST /api/bookings/calculate-price`

## Method
`POST`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Authorization`: `Bearer {token}` (bắt buộc - user phải đăng nhập)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

## Body

```json
{
  "showtime_id": 1,
  "seat_ids": [1, 2, 3],
  "voucher_code": "DISCOUNT10"
}
```

## Request Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `showtime_id` | ✓ | Number | ID của suất chiếu muốn tính giá vé |
| `seat_ids` | ✓ | Array | Danh sách ID ghế ngồi (tối thiểu 1 ghế) |
| `seat_ids.*` | ✓ | Number | ID của từng ghế ngồi trong mảng |
| `voucher_code` | ✗ | String | Mã voucher giảm giá (nếu có) |

### Chi tiết các trường

#### `showtime_id`
- **Bắt buộc**: Có
- **Kiểu**: Number
- **Mô tả**: ID của suất chiếu muốn tính giá vé
- **Validation**: 
  - Phải tồn tại trong bảng `showtimes`
  - Suất chiếu phải có status là `scheduled` hoặc `ongoing`
  - Suất chiếu chưa bị xóa (soft delete)

#### `seat_ids`
- **Bắt buộc**: Có
- **Kiểu**: Array of Numbers
- **Mô tả**: Danh sách ID các ghế muốn tính giá
- **Validation**: 
  - Phải là array
  - Tối thiểu 1 phần tử
  - Tất cả ID phải tồn tại trong bảng `seats`
  - Ghế phải thuộc phòng của suất chiếu
  - Ghế chưa được đặt trong suất chiếu đó (chỉ tính ghế đã thanh toán)
  - Ghế phải có status là `active`

#### `voucher_code`
- **Bắt buộc**: Không
- **Kiểu**: String
- **Mô tả**: Mã voucher để giảm giá
- **Validation**: 
  - Nếu có, phải tồn tại trong bảng `vouchers`
  - Voucher phải có status là `active`
  - Voucher phải còn trong thời gian hiệu lực (`valid_from` <= now <= `valid_to`)
  - Voucher phải áp dụng được cho user (nếu `applies_to` = `specific_users`)
  - Voucher phải áp dụng được cho movie (nếu `applies_to` = `specific_movies`)
  - Voucher chưa vượt quá `usage_limit` (nếu có)
  - User chưa vượt quá `per_user_limit` (nếu có)
- **Lưu ý**: API này chỉ tính toán preview, không cập nhật `used_count` của voucher

## Response Success

```json
{
  "success": true,
  "code": "TICKET_PRICE_CALCULATED_SUCCESS",
  "message": "Tính giá vé thành công",
  "data": {
    "movie_title": "Avengers: Endgame",
    "genre": "Hành động, Khoa học viễn tưởng",
    "showtime": {
      "date": "2024-01-15",
      "start_time": "14:00:00"
    },
    "seats": [
      {
        "id": 1,
        "row": "A",
        "number": 5
      },
      {
        "id": 2,
        "row": "A",
        "number": 6
      },
      {
        "id": 3,
        "row": "A",
        "number": 7
      }
    ],
    "seat_count": 3,
    "price": 150000,
    "voucher_code": "DISCOUNT10",
    "voucher_discount": 15000,
    "total_price": 135000
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`TICKET_PRICE_CALCULATED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Dữ liệu tính giá vé |
| `data.movie_title` | ✓ | String | Tên phim |
| `data.genre` | ✓ | String | Thể loại phim |
| `data.showtime` | ✓ | Object | Thông tin suất chiếu |
| `data.showtime.date` | ✓ | String | Ngày chiếu (format: YYYY-MM-DD) |
| `data.showtime.start_time` | ✓ | String | Giờ bắt đầu (format: HH:mm:ss) |
| `data.seats` | ✓ | Array | Danh sách ghế đã chọn |
| `data.seats[].id` | ✓ | Number | ID ghế |
| `data.seats[].row` | ✓ | String | Hàng ghế (A, B, C...) |
| `data.seats[].number` | ✓ | Number | Số ghế |
| `data.seat_count` | ✓ | Number | Số lượng ghế đã chọn |
| `data.price` | ✓ | Number | Tổng giá tiền trước giảm (VND) |
| `data.voucher_code` | ✗ | String | Mã voucher (null nếu không dùng) |
| `data.voucher_discount` | ✓ | Number | Số tiền được giảm từ voucher (VND, mặc định: 0) |
| `data.total_price` | ✓ | Number | Tổng giá tiền sau giảm (VND) |

## Response Error

### Validation Error (422)

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "showtime_id": "Suất chiếu không tồn tại",
    "seat_ids": "Vui lòng chọn ít nhất 1 ghế",
    "seat_ids.0": "Ghế không tồn tại"
  }
}
```

### Business Logic Error (400)

```json
{
  "success": false,
  "code": "SEAT_ALREADY_BOOKED",
  "message": "Ghế đã được đặt",
  "errors": {
    "error": "Some seats are already booked"
  }
}
```

### Voucher Error (400)

```json
{
  "success": false,
  "code": "VOUCHER_NOT_APPLICABLE",
  "message": "Voucher không áp dụng được",
  "errors": {
    "error": "This voucher is not applicable for your account"
  }
}
```

### Unauthorized (401)

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
| `TICKET_PRICE_CALCULATED_SUCCESS` | 200 | Tính giá vé thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `TICKET_PRICE_CALCULATION_FAILED` | 400 | Tính giá vé thất bại |
| `SHOWTIME_NOT_FOUND` | 404 | Suất chiếu không tồn tại |
| `SHOWTIME_INVALID_STATUS` | 400 | Suất chiếu không ở trạng thái cho phép đặt vé |
| `SEAT_NOT_FOUND` | 404 | Ghế không tồn tại |
| `SEAT_ALREADY_BOOKED` | 400 | Ghế đã được đặt trong suất chiếu này |
| `SEAT_INVALID_ROOM` | 400 | Ghế không thuộc phòng của suất chiếu |
| `SEAT_INACTIVE` | 400 | Ghế đang bảo trì hoặc vô hiệu |
| `VOUCHER_NOT_FOUND` | 404 | Voucher không tồn tại |
| `VOUCHER_INVALID` | 400 | Voucher không hợp lệ hoặc hết hạn |
| `VOUCHER_USAGE_LIMIT_EXCEEDED` | 400 | Voucher đã vượt quá số lần sử dụng |
| `VOUCHER_NOT_APPLICABLE` | 400 | Voucher không áp dụng được cho user/movie này |
| `VOUCHER_PER_USER_LIMIT_EXCEEDED` | 400 | User đã đạt giới hạn sử dụng tối đa cho voucher này |
| `VOUCHER_USER_NOT_APPLICABLE` | 400 | Voucher này không áp dụng cho tài khoản của bạn |
| `VOUCHER_MOVIE_NOT_APPLICABLE` | 400 | Voucher này không áp dụng cho phim này |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập |
| `FORBIDDEN` | 403 | Không có quyền truy cập |

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`
- `token`: JWT token sau khi login

### Example Request

**Request:**
```
POST {{base_url}}/api/bookings/calculate-price
```

**Headers:**
```
X-Api-Key: {{api_key}}
Language: vi
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
  "showtime_id": 1,
  "seat_ids": [1, 2, 3],
  "voucher_code": "DISCOUNT10"
}
```

## Notes

- API này chỉ tính toán **preview** giá vé, không tạo booking thực sự
- Voucher được validate nhưng **KHÔNG** cập nhật `used_count` khi gọi API này
- Chỉ kiểm tra ghế đã được đặt và **đã thanh toán** (`is_paid = true`) - ghế chưa thanh toán vẫn có thể chọn
- Giá vé được tính: `price = showtime->price * số lượng ghế`
- Nếu dùng voucher: `total_price = price - voucher_discount`
- Nếu không dùng voucher: `voucher_code = null`, `voucher_discount = 0`, `total_price = price`
- Seats được sắp xếp theo `row` và `number` (tăng dần)

