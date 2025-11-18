# 💰 [BOOKING] - Tạo đặt vé

## URL
`POST /api/bookings`

## Method
`POST`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Authorization`: `Bearer {token}` (bắt buộc - user phải đăng nhập)
- `Content-Type`: `application/json`

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
| `showtime_id` | ✓ | Number | ID của suất chiếu muốn đặt vé |
| `seat_ids` | ✓ | Array | Danh sách ID ghế ngồi (tối thiểu 1 ghế) |
| `seat_ids.*` | ✓ | Number | ID của từng ghế ngồi trong mảng |
| `voucher_code` | ✗ | String | Mã voucher giảm giá (nếu có) |

### Chi tiết các trường

#### `showtime_id`
- **Bắt buộc**: Có
- **Kiểu**: Number
- **Mô tả**: ID của suất chiếu muốn đặt vé
- **Validation**: 
  - Phải tồn tại trong bảng `showtimes`
  - Suất chiếu phải có status là `scheduled` hoặc `ongoing`
  - Suất chiếu chưa bị xóa (soft delete)

#### `seat_ids`
- **Bắt buộc**: Có
- **Kiểu**: Array of Numbers
- **Mô tả**: Danh sách ID các ghế muốn đặt
- **Validation**: 
  - Phải là array
  - Tối thiểu 1 phần tử
  - Tất cả ID phải tồn tại trong bảng `seats`
  - Ghế phải thuộc phòng của suất chiếu
  - Ghế chưa được đặt trong suất chiếu đó
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

## Response Success

```json
{
  "success": true,
  "code": "BOOKING_CREATED_SUCCESS",
  "message": "Đặt vé thành công",
  "data": {
    "id": 123,
    "code": "ABC12345",
    "status": "pending",
    "is_paid": false,
    "price": 150000,
    "total_price": 135000,
    "voucher_amount": 15000,
    "payment_method": null,
    "user": {
      "id": 10,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com"
    },
    "showtime": {
      "id": 1,
      "date": "2024-01-15",
      "start_time": "14:00:00",
      "end_time": "16:30:00",
      "price": 50000,
      "movie": {
        "id": 5,
        "title": "Avengers: Endgame",
        "duration": 180
      },
      "room": {
        "id": 2,
        "name": "Phòng 1",
        "cinema": {
          "id": 1,
          "name": "CGV Vincom",
          "location": "Hà Nội"
        }
      }
    },
    "seats": [
      {
        "id": 1,
        "row": "A",
        "number": 5,
        "type": "normal"
      },
      {
        "id": 2,
        "row": "A",
        "number": 6,
        "type": "normal"
      }
    ],
    "voucher": {
      "id": 1,
      "code": "DISCOUNT10",
      "name": "Giảm 10%",
      "type": "percentage",
      "amount": 10
    },
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`BOOKING_CREATED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Dữ liệu booking đã tạo |
| `data.id` | ✓ | Number | ID của booking |
| `data.code` | ✓ | String | Mã đặt vé (8 ký tự, tự động generate) |
| `data.status` | ✓ | String | Trạng thái: `pending`, `confirmed`, `canceled`, `completed` |
| `data.is_paid` | ✓ | Boolean | Đã thanh toán hay chưa (mặc định: `false`) |
| `data.price` | ✓ | Number | Tổng tiền trước giảm (VND) |
| `data.total_price` | ✓ | Number | Tổng tiền sau giảm (VND) |
| `data.voucher_amount` | ✓ | Number | Số tiền được giảm từ voucher (VND, mặc định: 0) |
| `data.payment_method` | ✗ | String | Phương thức thanh toán (null nếu chưa thanh toán) |
| `data.user` | ✓ | Object | Thông tin user đặt vé |
| `data.user.id` | ✓ | Number | ID của user |
| `data.user.name` | ✓ | String | Tên user |
| `data.user.email` | ✓ | String | Email user |
| `data.showtime` | ✓ | Object | Thông tin suất chiếu |
| `data.showtime.id` | ✓ | Number | ID suất chiếu |
| `data.showtime.date` | ✓ | String | Ngày chiếu (format: YYYY-MM-DD) |
| `data.showtime.start_time` | ✓ | String | Giờ bắt đầu (format: HH:mm:ss) |
| `data.showtime.end_time` | ✓ | String | Giờ kết thúc (format: HH:mm:ss) |
| `data.showtime.price` | ✓ | Number | Giá vé (VND) |
| `data.showtime.movie` | ✓ | Object | Thông tin phim |
| `data.showtime.room` | ✓ | Object | Thông tin phòng chiếu |
| `data.seats` | ✓ | Array | Danh sách ghế đã đặt |
| `data.seats[].id` | ✓ | Number | ID ghế |
| `data.seats[].row` | ✓ | String | Hàng ghế (A, B, C...) |
| `data.seats[].number` | ✓ | Number | Số ghế |
| `data.seats[].type` | ✓ | String | Loại ghế: `normal`, `vip`, `couple` |
| `data.voucher` | ✗ | Object | Thông tin voucher (null nếu không dùng) |
| `data.created_at` | ✓ | String | Thời gian tạo (format: YYYY-MM-DD HH:mm:ss) |
| `data.updated_at` | ✓ | String | Thời gian cập nhật (format: YYYY-MM-DD HH:mm:ss) |

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
    "seat_ids": "Một số ghế đã được đặt trong suất chiếu này"
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
| `BOOKING_CREATED_SUCCESS` | 201 | Tạo đặt vé thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
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
POST {{base_url}}/api/bookings
```

**Headers:**
```
X-Api-Key: {{api_key}}
Language: vi
Authorization: Bearer {{token}}
Content-Type: application/json
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

- Booking code được tự động generate (8 ký tự, format: `[A-Z0-9]{8}`)
- Status mặc định là `pending` sau khi tạo
- Có thể hủy booking trong vòng 30 phút sau khi tạo (nếu chưa thanh toán)
- Voucher chỉ áp dụng 1 lần cho mỗi user (nếu `applies_to` = `specific_users`)
- Tổng tiền được tính: `total_price = price - voucher_amount`
- Nếu không dùng voucher: `voucher_amount = 0`, `total_price = price`

