# Stripe Payment API Documentation

## Tổng quan

Hệ thống thanh toán Stripe cho phép người dùng thanh toán đặt vé xem phim thông qua thẻ tín dụng/ghi nợ. API sử dụng **Stripe Checkout Session** để tạo **link thanh toán trực tiếp**.

## Quy trình thanh toán (Siêu đơn giản)

```
1. POST /api/bookings → Nhận checkout_url
2. Mở checkout_url trong WebView
3. User thanh toán trực tiếp trên trang Stripe
4. Webhook tự động cập nhật booking thành "confirmed"
5. Mobile poll /api/payments/status/{booking_id} để check
```

> **🚀 Mobile chỉ cần mở `checkout_url` trong WebView. Không cần xử lý gì thêm!**

---

## API Endpoints

### 1. Tạo Booking (Trả về Link Thanh Toán)

**Endpoint:** `POST /api/bookings`

**Authentication:** Bearer Token (JWT)

**Headers:**

```
X-Api-Key: {api_key}
Authorization: Bearer {access_token}
Content-Type: application/json
Language: vi | en
```

**Request Body:**

```json
{
    "showtime_id": 1,
    "seat_ids": [1, 2, 3],
    "voucher_code": "DISCOUNT10"
}
```

**Response thành công (201):**

```json
{
    "success": true,
    "code": "BOOKING_CREATED_SUCCESS",
    "message": "Đặt vé thành công",
    "data": {
        "booking": {
            "id": 1,
            "code": "ABC12345",
            "status": "pending",
            "is_paid": false,
            "price": 150000,
            "total_price": 135000,
            "voucher_amount": 15000,
            "payment_method": "stripe",
            "user": {...},
            "showtime": {...},
            "seats": [...],
            "created_at": "2025-12-30 08:00:00"
        },
        "payment": {
            "checkout_url": "https://checkout.stripe.com/c/pay/cs_test_xxx...",
            "expires_at": "2025-12-30 08:30:00"
        }
    }
}
```

> **🔑 Chỉ cần mở `checkout_url` trong WebView!** Link có hiệu lực 30 phút.

---

### 2. Kiểm tra trạng thái thanh toán

**Endpoint:** `GET /api/payments/status/{booking_id}`

**Response:**

```json
{
    "success": true,
    "code": "PAYMENT_STATUS_FETCHED_SUCCESS",
    "data": {
        "booking_id": 1,
        "booking_code": "ABC12345",
        "is_paid": true,
        "booking_status": "confirmed",
        "payment_method": "stripe"
    }
}
```

---

### 3. Hủy Booking/Payment

**Endpoint:** `POST /api/payments/cancel`

**Request Body:**

```json
{
    "booking_id": 1
}
```

---

## Tích hợp Mobile (Flutter)

```dart
Future<void> bookAndPay({
  required int showtimeId,
  required List<int> seatIds,
}) async {
  // 1. Tạo booking
  final response = await api.post('/bookings', {
    'showtime_id': showtimeId,
    'seat_ids': seatIds,
  });

  final bookingId = response.data['data']['booking']['id'];
  final checkoutUrl = response.data['data']['payment']['checkout_url'];

  // 2. Mở link thanh toán trong WebView
  Navigator.push(context, MaterialPageRoute(
    builder: (_) => PaymentWebView(
      url: checkoutUrl,
      bookingId: bookingId,
    ),
  ));
}

// WebView đơn giản
class PaymentWebView extends StatelessWidget {
  final String url;
  final int bookingId;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Thanh toán')),
      body: WebViewWidget(
        controller: WebViewController()
          ..loadRequest(Uri.parse(url))
          ..setNavigationDelegate(NavigationDelegate(
            onNavigationRequest: (request) {
              // Khi redirect về app
              if (request.url.startsWith('myapp://payment/')) {
                _checkPaymentStatus(context, bookingId);
                return NavigationDecision.prevent;
              }
              return NavigationDecision.navigate;
            },
          )),
      ),
    );
  }

  Future<void> _checkPaymentStatus(BuildContext context, int bookingId) async {
    final status = await api.get('/payments/status/$bookingId');
    if (status.data['data']['is_paid']) {
      Navigator.pop(context);
      showSuccess('Thanh toán thành công!');
    }
  }
}
```

---

## Webhook (Tự động cập nhật)

Stripe Webhook tự động cập nhật booking khi:

-   ✅ `checkout.session.completed` → Booking = confirmed, is_paid = true
-   ❌ `checkout.session.expired` → Booking = canceled

---

## Test Cards

| Card Number         | Description |
| ------------------- | ----------- |
| 4242 4242 4242 4242 | Thành công  |
| 4000 0000 0000 0002 | Thất bại    |

Expiry: bất kỳ tương lai, CVC: 3 số bất kỳ
