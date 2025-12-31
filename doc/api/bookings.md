# API Booking (Đặt vé)

API quản lý đặt vé xem phim cho người dùng.

## Base URL

```
/api/bookings
```

## Headers bắt buộc

| Header        | Giá trị        | Mô tả                            |
| ------------- | -------------- | -------------------------------- |
| X-Api-Key     | your-api-key   | API key do server cấp            |
| Language      | vi hoặc en     | Ngôn ngữ response (mặc định: en) |
| Authorization | Bearer {token} | JWT token (bắt buộc)             |

---

## Endpoints

### 1. Lấy danh sách đặt vé

**GET** `/api/bookings`

Lấy danh sách tất cả đặt vé của user đang đăng nhập.

#### Query Parameters

| Tham số  | Kiểu    | Bắt buộc | Mô tả                                                             |
| -------- | ------- | -------- | ----------------------------------------------------------------- |
| status   | string  | Không    | Filter theo trạng thái (pending, confirmed, completed, cancelled) |
| page     | integer | Không    | Trang hiện tại                                                    |
| per_page | integer | Không    | Số booking mỗi trang. Mặc định: 15                                |

#### Response thành công

```json
{
    "success": true,
    "code": "BOOKINGS_FETCHED_SUCCESS",
    "message": "Bookings fetched successfully",
    "data": [
        {
            "id": 1,
            "code": "MWFMTIT9",
            "status": "confirmed",
            "is_paid": true,
            "price": 150000,
            "total_price": 135000,
            "voucher_amount": 15000,
            "payment_method": "stripe",
            "payment_intent_id": "pi_xxx",
            "paid_at": "2024-12-31 10:00:00",
            "showtime": {
                "id": 1,
                "date": "2024-12-31",
                "start_time": "19:00",
                "end_time": "21:30",
                "movie": {
                    "id": 1,
                    "title": "Avengers: Endgame",
                    "poster": {...}
                },
                "room": {
                    "id": 1,
                    "name": "Room A",
                    "cinema": {
                        "id": 1,
                        "name": "CGV Vincom",
                        "address": "123 Nguyen Hue",
                        "latitude": 10.7769,
                        "longitude": 106.7009
                    }
                }
            },
            "seats": [
                {
                    "id": 1,
                    "row": "A",
                    "number": 5,
                    "type": "standard"
                }
            ],
            "voucher": {
                "id": 1,
                "code": "NEWYEAR2024",
                "discount_amount": 15000
            },
            "created_at": "2024-12-31 09:00:00",
            "updated_at": "2024-12-31 10:00:00"
        }
    ]
}
```

---

### 2. Tạo đặt vé mới

**POST** `/api/bookings`

Tạo đặt vé mới kèm thanh toán (Stripe hoặc VNPay).

#### Request Body

| Field          | Kiểu    | Bắt buộc | Mô tả                                                             |
| -------------- | ------- | -------- | ----------------------------------------------------------------- |
| showtime_id    | integer | Có       | ID của suất chiếu                                                 |
| seat_ids       | array   | Có       | Mảng ID các ghế muốn đặt                                          |
| voucher_code   | string  | Không    | Mã voucher giảm giá                                               |
| payment_method | string  | Không    | Phương thức thanh toán: `stripe` hoặc `vnpay`. Mặc định: `stripe` |
| bank_code      | string  | Không    | Mã ngân hàng cho VNPay (optional)                                 |

#### Request Example

```json
{
    "showtime_id": 1,
    "seat_ids": [1, 2, 3],
    "voucher_code": "NEWYEAR2024",
    "payment_method": "stripe"
}
```

#### Response thành công (201)

**Stripe Payment:**

```json
{
    "success": true,
    "code": "BOOKING_CREATED_SUCCESS",
    "message": "Booking created successfully",
    "data": {
        "booking": {
            "id": 1,
            "code": "MWFMTIT9",
            "status": "pending",
            "is_paid": false,
            "price": 150000,
            "total_price": 135000,
            "voucher_amount": 15000,
            "payment_method": "stripe",
            "payment_intent_id": "pi_xxx",
            "paid_at": null,
            "showtime": {...},
            "seats": [...],
            "voucher": {...},
            "created_at": "2024-12-31 09:00:00",
            "updated_at": "2024-12-31 09:00:00"
        },
        "payment": {
            "client_secret": "pi_xxx_secret_xxx",
            "payment_intent_id": "pi_xxx",
            "amount": 135000,
            "currency": "vnd"
        }
    }
}
```

**VNPay Payment:**

```json
{
    "success": true,
    "code": "BOOKING_CREATED_SUCCESS",
    "message": "Booking created successfully",
    "data": {
        "booking": {
            "id": 1,
            "code": "MWFMTIT9",
            "status": "pending",
            "is_paid": false,
            "price": 150000,
            "total_price": 135000,
            "voucher_amount": 15000,
            "payment_method": "vnpay",
            "payment_intent_id": null,
            "paid_at": null,
            "showtime": {...},
            "seats": [...],
            "voucher": {...},
            "created_at": "2024-12-31 09:00:00",
            "updated_at": "2024-12-31 09:00:00"
        },
        "payment": {
            "payment_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?...",
            "txn_ref": "MWFMTIT9",
            "amount": 135000
        }
    }
}
```

#### Response lỗi - Ghế đã được đặt (400)

```json
{
    "success": false,
    "code": "SEAT_ALREADY_BOOKED",
    "message": "Seat already booked",
    "errors": {}
}
```

#### Response lỗi - Suất chiếu không khả dụng (400)

```json
{
    "success": false,
    "code": "SHOWTIME_INVALID_STATUS",
    "message": "Showtime is not available for booking",
    "errors": {}
}
```

#### Response lỗi - Voucher không hợp lệ (400)

```json
{
    "success": false,
    "code": "VOUCHER_INVALID",
    "message": "Invalid or expired voucher",
    "errors": {}
}
```

---

### 3. Xem chi tiết đặt vé

**GET** `/api/bookings/{id}`

Lấy thông tin chi tiết của một đặt vé.

#### Path Parameters

| Tham số | Kiểu    | Mô tả          |
| ------- | ------- | -------------- |
| id      | integer | ID của booking |

#### Response thành công

```json
{
    "success": true,
    "code": "BOOKING_FETCHED_SUCCESS",
    "message": "Booking fetched successfully",
    "data": {
        "id": 1,
        "code": "MWFMTIT9",
        "status": "confirmed",
        "is_paid": true,
        "price": 150000,
        "total_price": 135000,
        "voucher_amount": 15000,
        "payment_method": "stripe",
        "payment_intent_id": "pi_xxx",
        "paid_at": "2024-12-31 10:00:00",
        "showtime": {
            "id": 1,
            "date": "2024-12-31",
            "start_time": "19:00",
            "end_time": "21:30",
            "movie": {
                "id": 1,
                "title": "Avengers: Endgame",
                "duration": 181,
                "poster": {
                    "id": 1,
                    "url": "https://example.com/poster.jpg"
                }
            },
            "room": {
                "id": 1,
                "name": "Room A",
                "room_type": {
                    "id": 1,
                    "name": "2D"
                },
                "cinema": {
                    "id": 1,
                    "name": "CGV Vincom",
                    "address": "123 Nguyen Hue, District 1, Ho Chi Minh City",
                    "latitude": 10.7769,
                    "longitude": 106.7009
                }
            }
        },
        "seats": [
            {
                "id": 1,
                "row": "A",
                "number": 5,
                "type": "standard"
            },
            {
                "id": 2,
                "row": "A",
                "number": 6,
                "type": "standard"
            }
        ],
        "voucher": {
            "id": 1,
            "code": "NEWYEAR2024",
            "discount_type": "fixed",
            "discount_value": 15000
        },
        "created_at": "2024-12-31 09:00:00",
        "updated_at": "2024-12-31 10:00:00"
    }
}
```

#### Response lỗi - Không tìm thấy (404)

```json
{
    "success": false,
    "code": "NOT_FOUND",
    "message": "Resource not found",
    "errors": {}
}
```

---

### 4. Hủy đặt vé

**POST** `/api/bookings/{id}/cancel`

Hủy một đặt vé (chỉ hủy được nếu chưa thanh toán hoặc trong thời gian cho phép).

#### Path Parameters

| Tham số | Kiểu    | Mô tả          |
| ------- | ------- | -------------- |
| id      | integer | ID của booking |

#### Response thành công

```json
{
    "success": true,
    "code": "BOOKING_CANCELLED_SUCCESS",
    "message": "Booking cancelled successfully",
    "data": null
}
```

#### Response lỗi - Không thể hủy (400)

```json
{
    "success": false,
    "code": "BOOKING_CANCEL_FAILED",
    "message": "Failed to cancel booking",
    "errors": {}
}
```

---

## Booking Status

| Status    | Mô tả                         |
| --------- | ----------------------------- |
| pending   | Đang chờ thanh toán           |
| confirmed | Đã xác nhận (đã thanh toán)   |
| completed | Đã hoàn thành (đã sử dụng vé) |
| cancelled | Đã hủy                        |

---

## Mã lỗi

| Mã                              | HTTP Status | Mô tả                                         |
| ------------------------------- | ----------- | --------------------------------------------- |
| SEAT_ALREADY_BOOKED             | 400         | Ghế đã được đặt                               |
| SHOWTIME_NOT_FOUND              | 404         | Không tìm thấy suất chiếu                     |
| SHOWTIME_INVALID_STATUS         | 400         | Suất chiếu không ở trạng thái cho phép đặt vé |
| SEAT_NOT_FOUND                  | 404         | Ghế không tồn tại                             |
| SEAT_INVALID_ROOM               | 400         | Ghế không thuộc phòng của suất chiếu          |
| SEAT_INACTIVE                   | 400         | Ghế đang bảo trì hoặc vô hiệu                 |
| VOUCHER_INVALID                 | 400         | Voucher không hợp lệ hoặc đã hết hạn          |
| VOUCHER_NOT_FOUND               | 404         | Voucher không tồn tại                         |
| VOUCHER_USAGE_LIMIT_EXCEEDED    | 400         | Voucher đã vượt quá số lần sử dụng            |
| VOUCHER_PER_USER_LIMIT_EXCEEDED | 400         | Đã đạt giới hạn sử dụng voucher               |
| VOUCHER_MOVIE_NOT_APPLICABLE    | 400         | Voucher không áp dụng cho phim này            |
| BOOKING_NOT_FOUND               | 404         | Không tìm thấy đặt vé                         |
| BOOKING_CANCEL_FAILED           | 400         | Hủy đặt vé thất bại                           |
| BOOKING_ALREADY_PAID            | 400         | Đặt vé đã được thanh toán                     |
| UNAUTHORIZED                    | 401         | Chưa đăng nhập hoặc token hết hạn             |
| INVALID_API_KEY                 | 401         | API key không hợp lệ                          |
