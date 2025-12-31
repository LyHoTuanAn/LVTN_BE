# API Reviews - Đánh giá phim

## Tổng quan

API Reviews cho phép người dùng đánh giá phim sau khi đã xem. Hệ thống áp dụng thiết kế **đánh giá theo PHIM với ràng buộc Booking** để đảm bảo độ tin cậy của đánh giá.

### Đặc điểm:

-   Mỗi user chỉ được đánh giá **1 lần/phim** (unique `[user_id, movie_id]`)
-   Nếu có `booking_id`, chỉ được review **sau khi suất chiếu kết thúc**
-   Booking phải ở trạng thái `completed`
-   Rating từ 1-5 sao

---

## Endpoints

### Protected Routes (yêu cầu đăng nhập)

| Method | Endpoint            | Mô tả                 |
| ------ | ------------------- | --------------------- |
| POST   | `/api/reviews`      | Tạo đánh giá mới      |
| GET    | `/api/reviews/{id}` | Xem chi tiết đánh giá |

---

## 1. Tạo đánh giá mới

### Request

```
POST /api/reviews
```

**Headers:**

```
X-Api-Key: your-api-key
Authorization: Bearer {access_token}
Content-Type: application/json
Language: vi
```

**Body:**

```json
{
    "movie_id": 5,
    "booking_id": 45,
    "rating": 5,
    "comment": "Phim rất hay, diễn xuất tuyệt vời!",
    "media_id": null
}
```

| Trường     | Kiểu    | Bắt buộc | Mô tả                                 |
| ---------- | ------- | -------- | ------------------------------------- |
| movie_id   | integer | ✅       | ID phim cần đánh giá                  |
| booking_id | integer | ❌       | ID đặt vé (tùy chọn, tăng độ tin cậy) |
| rating     | integer | ✅       | Số sao (1-5)                          |
| comment    | string  | ❌       | Nội dung đánh giá (tối đa 1000 ký tự) |
| media_id   | integer | ❌       | ID file media đính kèm                |

### Response thành công

```json
{
    "success": true,
    "code": "REVIEW_CREATED_SUCCESS",
    "message": "Đánh giá phim thành công",
    "data": {
        "id": 25,
        "rating": 5,
        "comment": "Phim rất hay, diễn xuất tuyệt vời!",
        "status": "approved",
        "user": {
            "id": 10,
            "name": "Nguyễn Văn A"
        },
        "movie": {
            "id": 5,
            "title": "Avengers: Endgame",
            "poster": {
                "url": "https://example.com/poster.jpg"
            }
        },
        "booking": {
            "id": 45,
            "code": "MWFMTIT9"
        },
        "media": null,
        "created_at": "2025-12-31 10:30:00",
        "updated_at": "2025-12-31 10:30:00"
    }
}
```

### Response lỗi - Đã đánh giá

```json
{
    "success": false,
    "code": "ALREADY_REVIEWED",
    "message": "Bạn đã đánh giá phim này rồi"
}
```

### Response lỗi - Phim chưa chiếu xong

```json
{
    "success": false,
    "code": "SHOWTIME_NOT_ENDED",
    "message": "Bạn chỉ có thể đánh giá sau khi phim kết thúc"
}
```

---

## 2. Xem chi tiết đánh giá

### Request

```
GET /api/reviews/{id}
```

**Headers:**

```
X-Api-Key: your-api-key
Authorization: Bearer {access_token}
Language: vi
```

### Response thành công

```json
{
    "success": true,
    "code": "REVIEW_FETCHED_SUCCESS",
    "message": "Lấy thông tin đánh giá thành công",
    "data": {
        "id": 25,
        "rating": 5,
        "comment": "Phim rất hay!",
        "status": "approved",
        "user": {
            "id": 10,
            "name": "Nguyễn Văn A"
        },
        "movie": {
            "id": 5,
            "title": "Avengers: Endgame",
            "poster": {
                "url": "https://example.com/poster.jpg"
            }
        },
        "booking": {
            "id": 45,
            "code": "MWFMTIT9"
        },
        "media": null,
        "created_at": "2025-12-31 10:30:00",
        "updated_at": "2025-12-31 10:30:00"
    }
}
```

### Response lỗi - Không tìm thấy

```json
{
    "success": false,
    "code": "REVIEW_NOT_FOUND",
    "message": "Không tìm thấy đánh giá"
}
```

---

## Error Codes

| Code                       | Message (EN)                                  | Message (VI)                                    |
| -------------------------- | --------------------------------------------- | ----------------------------------------------- |
| REVIEW_NOT_FOUND           | Review not found                              | Không tìm thấy đánh giá                         |
| ALREADY_REVIEWED           | You have already reviewed this movie          | Bạn đã đánh giá phim này rồi                    |
| BOOKING_NOT_BELONG_TO_USER | This booking does not belong to you           | Đặt vé này không thuộc về bạn                   |
| BOOKING_MOVIE_MISMATCH     | The booking is not for this movie             | Đặt vé này không phải cho phim này              |
| SHOWTIME_NOT_ENDED         | You can only review after the movie has ended | Bạn chỉ có thể đánh giá sau khi phim kết thúc   |
| BOOKING_NOT_COMPLETED      | Your booking must be completed to review      | Đặt vé của bạn phải được hoàn thành để đánh giá |
