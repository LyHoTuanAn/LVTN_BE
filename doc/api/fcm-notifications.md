# FCM Token & Push Notification API

## 📋 Mục lục

1. [Tổng quan](#tổng-quan)
2. [FCM Token API](#fcm-token-api)
3. [Admin Notification API](#admin-notification-api)
4. [Topic Messaging](#topic-messaging)
5. [Flutter Integration](#flutter-integration)

---

## 🎯 Tổng quan

Hệ thống Push Notification sử dụng Firebase Cloud Messaging (FCM) HTTP v1 API để gửi thông báo đến ứng dụng di động.

### Các tính năng chính:

-   **FCM Token Management**: Đăng ký, cập nhật và xóa FCM token
-   **Individual Notifications**: Gửi thông báo đến từng user
-   **Broadcast Notifications**: Gửi thông báo đến tất cả users qua Topic Messaging
-   **Admin Web Panel**: Giao diện quản lý gửi thông báo từ Admin

---

## 📱 FCM Token API

### 1. Đăng ký FCM Token

Đăng ký hoặc cập nhật FCM token cho user hiện tại.

**Endpoint:** `POST /api/auth/fcm-token`

**Headers:**

```
X-Api-Key: {api_key}
Authorization: Bearer {access_token}
Language: vi
```

**Request Body:**

```json
{
    "fcm_token": "dL8G2x...(FCM token từ Firebase)"
}
```

**Response thành công (200):**

```json
{
    "success": true,
    "code": "FCM_TOKEN_REGISTERED_SUCCESS",
    "message": "Đăng ký FCM token thành công",
    "data": {
        "fcm_token": "dL8G2x...",
        "is_active": true
    }
}
```

**Response lỗi (400):**

```json
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "fcm_token": ["FCM token là bắt buộc"]
    }
}
```

### 2. Logout với FCM Token

Khi logout, có thể gửi kèm FCM token để xóa khỏi hệ thống.

**Endpoint:** `POST /api/auth/logout`

**Request Body:**

```json
{
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "fcm_token": "dL8G2x..."
}
```

**Response thành công (200):**

```json
{
    "success": true,
    "code": "LOGOUT_SUCCESS",
    "message": "Đăng xuất thành công"
}
```

---

## 🔔 Admin Notification API

### 1. Gửi thông báo đến một user

**Endpoint:** `POST /api/admin/notifications/send-to-user`

**Headers:**

```
X-Api-Key: {api_key}
Authorization: Bearer {admin_access_token}
Language: vi
```

**Request Body:**

```json
{
    "user_id": 123,
    "title": "Khuyến mãi đặc biệt",
    "body": "Giảm 50% cho tất cả các phim trong tuần này!",
    "data": {
        "type": "promotion",
        "promotion_id": "PROMO123"
    },
    "image_url": "https://example.com/promo-image.jpg"
}
```

### 2. Gửi thông báo đến tất cả users (Broadcast)

Sử dụng Topic Messaging để gửi đến tất cả users đã đăng ký topic.

**Endpoint:** `POST /api/admin/notifications/send-to-all`

**Request Body:**

```json
{
    "title": "Tin tức quan trọng",
    "body": "Ứng dụng đã được cập nhật với nhiều tính năng mới!",
    "image_url": "https://example.com/announcement.jpg"
}
```

### 3. Lấy danh sách Topics

**Endpoint:** `GET /api/admin/notifications/topics`

---

## 📡 Topic Messaging

### Các Topics có sẵn:

| Topic Key           | Topic Name                | Mô tả                             |
| ------------------- | ------------------------- | --------------------------------- |
| `all_users`         | `celes_all_users`         | Tất cả users - dùng cho broadcast |
| `new_movies`        | `celes_new_movies`        | Thông báo phim mới                |
| `promotions`        | `celes_promotions`        | Khuyến mãi và giảm giá            |
| `booking_reminders` | `celes_booking_reminders` | Nhắc nhở lịch chiếu               |

---

## 📲 Flutter Integration

### 1. Get FCM Token và đăng ký

```dart
// Get FCM token
final fcmToken = await FirebaseMessaging.instance.getToken();

// Register token with backend (after login)
await dio.post('/api/auth/fcm-token', data: {
  'fcm_token': fcmToken,
});

// Subscribe to topics (after login)
await FirebaseMessaging.instance.subscribeToTopic('celes_all_users');
```

### 2. Logout với FCM Token

```dart
final fcmToken = await FirebaseMessaging.instance.getToken();

// Unsubscribe from topics
await FirebaseMessaging.instance.unsubscribeFromTopic('celes_all_users');

// Logout with FCM token
await dio.post('/api/auth/logout', data: {
  'refresh_token': refreshToken,
  'fcm_token': fcmToken,
});
```

---

## ⚠️ Error Codes

| Code                            | HTTP Status | Mô tả                        |
| ------------------------------- | ----------- | ---------------------------- |
| `FCM_TOKEN_REGISTRATION_FAILED` | 500         | Đăng ký FCM token thất bại   |
| `NOTIFICATION_SEND_FAILED`      | 400         | Gửi thông báo thất bại       |
| `VALIDATION_ERROR`              | 422         | Dữ liệu đầu vào không hợp lệ |
