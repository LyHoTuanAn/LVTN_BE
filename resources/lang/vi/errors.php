<?php

return [
    'INVALID_API_KEY' => 'API key không hợp lệ',
    'EMAIL_EXISTS' => 'Email đã được sử dụng',
    'LOGIN_FAILED' => 'Thông tin đăng nhập không chính xác',
    'UNAUTHORIZED' => 'Chưa đăng nhập',
    'FORBIDDEN' => 'Không có quyền truy cập',
    'VALIDATION_ERROR' => 'Dữ liệu không hợp lệ',
    'NOT_FOUND' => 'Không tìm thấy tài nguyên',
    'SEAT_ALREADY_BOOKED' => 'Ghế đã được đặt',
    'SHOWTIME_NOT_FOUND' => 'Suất chiếu không tồn tại',
    'VOUCHER_INVALID' => 'Voucher không hợp lệ hoặc đã hết hạn',
    'VOUCHER_NOT_FOUND' => 'Voucher không tồn tại',
    'VOUCHER_USAGE_LIMIT_EXCEEDED' => 'Voucher đã vượt quá số lần sử dụng',
    'VOUCHER_NOT_APPLICABLE' => 'Voucher không áp dụng được',
    'VOUCHER_PER_USER_LIMIT_EXCEEDED' => 'Bạn đã đạt giới hạn sử dụng tối đa cho voucher này',
    'VOUCHER_USER_NOT_APPLICABLE' => 'Voucher này không áp dụng cho tài khoản của bạn',
    'VOUCHER_MOVIE_NOT_APPLICABLE' => 'Voucher này không áp dụng cho phim này',
    'SEAT_NOT_FOUND' => 'Ghế không tồn tại',
    'SEAT_INVALID_ROOM' => 'Ghế không thuộc phòng của suất chiếu',
    'SEAT_INACTIVE' => 'Ghế đang bảo trì hoặc vô hiệu',
    'SHOWTIME_INVALID_STATUS' => 'Suất chiếu không ở trạng thái cho phép đặt vé',
    'USER_CREATION_FAILED' => 'Tạo người dùng thất bại',
    'LOGOUT_FAILED' => 'Đăng xuất thất bại',
    'TOKEN_REFRESH_FAILED' => 'Làm mới token thất bại',
    'BOOKING_CREATION_FAILED' => 'Tạo đặt vé thất bại',
    'BOOKING_CANCEL_FAILED' => 'Hủy đặt vé thất bại',
    'TICKET_PRICE_CALCULATION_FAILED' => 'Tính giá vé thất bại',
    'OTP_INVALID' => 'Mã OTP không hợp lệ',
    'OTP_EXPIRED' => 'Mã OTP đã hết hạn',
    'OTP_ALREADY_VERIFIED' => 'Mã OTP đã được xác minh',
    'OTP_NOT_FOUND' => 'Không tìm thấy mã OTP',
    'EMAIL_NOT_VERIFIED' => 'Email chưa được xác minh',
    'RATE_LIMIT_EXCEEDED' => 'Vui lòng đợi 60 giây trước khi yêu cầu mã OTP mới',
    'PASSWORD_RESET_FAILED' => 'Đặt lại mật khẩu thất bại',
    'EMAIL_NOT_FOUND' => 'Email không tồn tại',
    'RESET_TOKEN_INVALID' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn',
    'CURRENT_PASSWORD_INVALID' => 'Mật khẩu hiện tại không chính xác',
    'PASSWORD_CHANGE_FAILED' => 'Đổi mật khẩu thất bại',
    'PROFILE_UPDATE_FAILED' => 'Cập nhật thông tin cá nhân thất bại',
    'IMAGE_UPLOAD_FAILED' => 'Tải ảnh lên thất bại',

    // Cinema errors
    'CINEMA_CREATE_FAILED' => 'Tạo rạp chiếu thất bại',
    'CINEMA_VALIDATION_FAILED' => 'Dữ liệu rạp chiếu không hợp lệ',
    'CINEMA_DELETE_FAILED' => 'Xóa rạp chiếu thất bại',
    'CINEMA_UPDATE_FAILED' => 'Cập nhật rạp chiếu thất bại',

    // Stripe Payment Errors
    'STRIPE_AMOUNT_TOO_SMALL' => 'Số tiền thanh toán quá nhỏ. Tối thiểu là 10.000 VND',
    'STRIPE_PAYMENT_INTENT_FAILED' => 'Không thể tạo yêu cầu thanh toán',
    'STRIPE_PAYMENT_INTENT_NOT_FOUND' => 'Không tìm thấy yêu cầu thanh toán',
    'STRIPE_PAYMENT_CONFIRM_FAILED' => 'Xác nhận thanh toán thất bại',
    'STRIPE_PAYMENT_CANCEL_FAILED' => 'Hủy thanh toán thất bại',
    'STRIPE_REFUND_FAILED' => 'Hoàn tiền thất bại',
    'STRIPE_WEBHOOK_SIGNATURE_INVALID' => 'Chữ ký webhook không hợp lệ',
    'STRIPE_CHECKOUT_SESSION_FAILED' => 'Không thể tạo phiên thanh toán',
    'STRIPE_CHECKOUT_SESSION_NOT_FOUND' => 'Không tìm thấy phiên thanh toán',
    'BOOKING_ALREADY_PAID' => 'Đặt vé này đã được thanh toán',
    'BOOKING_INVALID_STATUS' => 'Đặt vé không ở trạng thái hợp lệ cho thao tác này',
    'PAYMENT_INTENT_MISMATCH' => 'Phiên thanh toán không khớp với đặt vé',
    'PAYMENT_NOT_COMPLETED' => 'Thanh toán chưa hoàn thành',
    'PAYMENT_INTENT_CREATION_FAILED' => 'Không thể tạo yêu cầu thanh toán',
    'PAYMENT_CONFIRMATION_FAILED' => 'Xác nhận thanh toán thất bại',
    'PAYMENT_CANCEL_FAILED' => 'Hủy thanh toán thất bại',

    // VNPay errors
    'VNPAY_NOT_CONFIGURED' => 'VNPay chưa được cấu hình',
    'VNPAY_INVALID_SIGNATURE' => 'Chữ ký VNPay không hợp lệ',
    'VNPAY_PAYMENT_FAILED' => 'Thanh toán VNPay thất bại',
    'VNPAY_INVALID_AMOUNT' => 'Số tiền thanh toán không khớp',

    // QR Ticket Scanner errors
    'BOOKING_NOT_FOUND' => 'Không tìm thấy đặt vé',
    'BOOKING_ALREADY_COMPLETED' => 'Vé này đã được sử dụng',
    'BOOKING_CANCELED' => 'Đặt vé này đã bị hủy',
    'BOOKING_NOT_PAID' => 'Đặt vé này chưa được thanh toán',

    // Favorite Movie errors
    'MOVIE_NOT_FOUND' => 'Không tìm thấy phim',
    'MOVIE_ALREADY_FAVORITED' => 'Phim đã có trong danh sách yêu thích',
    'FAVORITE_NOT_FOUND' => 'Phim không có trong danh sách yêu thích',

    // Review errors
    'REVIEW_NOT_FOUND' => 'Không tìm thấy đánh giá',
    'ALREADY_REVIEWED' => 'Bạn đã đánh giá phim này rồi',
    'BOOKING_NOT_BELONG_TO_USER' => 'Đặt vé này không thuộc về bạn',
    'BOOKING_MOVIE_MISMATCH' => 'Đặt vé này không phải cho phim này',
    'SHOWTIME_NOT_ENDED' => 'Bạn chỉ có thể đánh giá sau khi phim kết thúc',
    'BOOKING_NOT_COMPLETED' => 'Đặt vé của bạn phải được hoàn thành để đánh giá',
    'REVIEW_NOT_BELONG_TO_USER' => 'Đánh giá này không thuộc về bạn',

    // FCM Token errors
    'FCM_TOKEN_REGISTRATION_FAILED' => 'Đăng ký FCM token thất bại',
    'FCM_TOKEN_NOT_FOUND' => 'Không tìm thấy FCM token',

    // Notification errors
    'NOTIFICATION_SEND_FAILED' => 'Gửi thông báo thất bại',
    'NOTIFICATION_USER_NOT_FOUND' => 'Không tìm thấy người dùng để gửi thông báo',
    'NOTIFICATION_NO_TOKENS' => 'Không tìm thấy FCM token nào cho người dùng',
];
