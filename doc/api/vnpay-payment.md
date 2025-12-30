# VNPay Payment API Documentation

## Tổng quan

Hệ thống thanh toán VNPay cho phép người dùng thanh toán đặt vé xem phim thông qua ngân hàng nội địa Việt Nam, thẻ quốc tế và ví điện tử.

## Quy trình thanh toán

```
1. POST /api/bookings (payment_method: vnpay) → Nhận checkout_url VNPay
2. Mở checkout_url trong WebView
3. User thanh toán trên trang VNPay
4. VNPay gọi IPN → Backend cập nhật booking
5. VNPay redirect user về app
```

---

## API Endpoints

### 1. Tạo Booking với VNPay

**Endpoint:** `POST /api/bookings`

**Headers:**

```
X-Api-Key: {api_key}
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "showtime_id": 1,
    "seat_ids": [1, 2, 3],
    "voucher_code": "DISCOUNT10",
    "payment_method": "vnpay",
    "bank_code": "NCB"
}
```

| Field              | Type    | Required | Description               |
| ------------------ | ------- | -------- | ------------------------- |
| showtime_id        | integer | Yes      | ID của suất chiếu         |
| seat_ids           | array   | Yes      | Danh sách ID ghế đặt      |
| voucher_code       | string  | No       | Mã voucher giảm giá       |
| **payment_method** | string  | Yes      | **"vnpay"** để dùng VNPay |
| bank_code          | string  | No       | Mã ngân hàng (tùy chọn)   |

**Mã ngân hàng phổ biến:**
| Mã | Ngân hàng |
|----|-----------|
| NCB | Ngân hàng Quốc Dân |
| VNPAYQR | Quét mã QR |
| VNBANK | ATM - Ngân hàng nội địa |
| INTCARD | Thẻ quốc tế |

> **Lưu ý:** Nếu không truyền `bank_code`, user sẽ được chọn phương thức thanh toán trên trang VNPay.

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
            "payment_method": "vnpay",
            ...
        },
        "payment": {
            "method": "vnpay",
            "checkout_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=...",
            "expires_at": "2025-12-30 09:45:00"
        }
    }
}
```

> **🔑 Mở `checkout_url` trong WebView để thanh toán!**

---

### 2. VNPay IPN (Server-to-Server)

**Endpoint:** `GET /api/vnpay/ipn`

VNPay sẽ gọi URL này để thông báo kết quả thanh toán. Backend tự động xử lý:

-   Xác thực chữ ký (checksum)
-   Kiểm tra số tiền
-   Cập nhật trạng thái booking

**Response:**

```json
{
    "RspCode": "00",
    "Message": "Confirm Success"
}
```

---

### 3. VNPay Return URL (Redirect User)

**Endpoint:** `GET /api/vnpay/return`

Sau khi thanh toán, VNPay redirect user về URL này. Backend hiển thị kết quả và redirect về app.

---

## Tích hợp Mobile (Flutter)

```dart
Future<void> bookWithVNPay({
  required int showtimeId,
  required List<int> seatIds,
}) async {
  // 1. Tạo booking với VNPay
  final response = await api.post('/bookings', {
    'showtime_id': showtimeId,
    'seat_ids': seatIds,
    'payment_method': 'vnpay',  // Chọn VNPay
    // 'bank_code': 'NCB',      // Optional: chọn ngân hàng trước
  });

  final bookingId = response.data['data']['booking']['id'];
  final checkoutUrl = response.data['data']['payment']['checkout_url'];

  // 2. Mở WebView
  Navigator.push(context, MaterialPageRoute(
    builder: (_) => VNPayWebView(
      url: checkoutUrl,
      bookingId: bookingId,
    ),
  ));
}

// WebView VNPay
class VNPayWebView extends StatelessWidget {
  final String url;
  final int bookingId;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Thanh toán VNPay')),
      body: WebViewWidget(
        controller: WebViewController()
          ..loadRequest(Uri.parse(url))
          ..setNavigationDelegate(NavigationDelegate(
            onNavigationRequest: (request) {
              // Detect redirect về app
              if (request.url.contains('/vnpay/return')) {
                // Đợi 2s rồi close WebView và check status
                Future.delayed(Duration(seconds: 2), () {
                  _checkPaymentStatus(context);
                });
              }
              return NavigationDecision.navigate;
            },
          )),
      ),
    );
  }

  Future<void> _checkPaymentStatus(BuildContext context) async {
    final status = await api.get('/payments/status/$bookingId');
    Navigator.pop(context);

    if (status.data['data']['is_paid']) {
      showSuccess('Thanh toán thành công!');
    } else {
      showError('Thanh toán thất bại');
    }
  }
}
```

---

## So sánh Stripe vs VNPay

| Tiêu chí      | Stripe                     | VNPay                     |
| ------------- | -------------------------- | ------------------------- |
| Phương thức   | Thẻ quốc tế                | Ngân hàng VN, QR, Ví      |
| Đối tượng     | User quốc tế               | User Việt Nam             |
| Phí           | ~2.9% + 30¢                | Thỏa thuận                |
| Currency      | Multi                      | VND only                  |
| Request field | `payment_method: "stripe"` | `payment_method: "vnpay"` |

---

## Mã lỗi VNPay

| Mã  | Mô tả                            |
| --- | -------------------------------- |
| 00  | Giao dịch thành công             |
| 07  | Giao dịch bị nghi ngờ gian lận   |
| 09  | Chưa đăng ký InternetBanking     |
| 10  | Xác thực sai quá 3 lần           |
| 11  | Hết hạn thanh toán               |
| 12  | Thẻ/Tài khoản bị khóa            |
| 13  | Sai mật khẩu OTP                 |
| 24  | Khách hủy giao dịch              |
| 51  | Không đủ số dư                   |
| 65  | Vượt hạn mức giao dịch           |
| 75  | Ngân hàng đang bảo trì           |
| 79  | Sai mật khẩu quá số lần quy định |
| 99  | Lỗi khác                         |

---

## Test Account

VNPay Sandbox sử dụng các thông tin test:

-   **Ngân hàng:** NCB
-   **Số thẻ:** 9704198526191432198
-   **Tên chủ thẻ:** NGUYEN VAN A
-   **Ngày phát hành:** 07/15
-   **OTP:** 123456
