<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình kết nối VNPay - Cổng thanh toán VNPAY
    |
    */

    // Mã website của merchant trên hệ thống VNPAY
    'tmn_code' => env('VNPAY_TMN_CODE'),

    // Chuỗi bí mật dùng để tạo checksum
    'hash_secret' => env('VNPAY_HASH_SECRET'),

    // URL thanh toán (Sandbox hoặc Production)
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    // URL API query/refund
    'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),

    // URL nhận kết quả trả về cho user (redirect)
    'return_url' => env('VNPAY_RETURN_URL'),

    // URL nhận IPN từ VNPay (server-to-server)
    'ipn_url' => env('VNPAY_IPN_URL'),

    // Phiên bản API
    'version' => '2.1.0',

    // Đơn vị tiền tệ
    'currency' => 'VND',

    // Loại hàng hóa (billpayment = thanh toán hóa đơn, other = khác)
    'order_type' => 'billpayment',

    // Ngôn ngữ hiển thị (vn = Tiếng Việt, en = Tiếng Anh)
    'locale' => 'vn',

    // Thời gian hết hạn thanh toán (phút)
    'expire_minutes' => 15,
];
