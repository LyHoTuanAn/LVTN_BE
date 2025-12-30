<?php

namespace App\Services\Payment;

use App\Models\Booking;
use Exception;

class VNPayService
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $url;
    protected string $returnUrl;
    protected string $version;

    public function __construct()
    {
        $this->tmnCode = config('vnpay.tmn_code');
        $this->hashSecret = config('vnpay.hash_secret');
        $this->url = config('vnpay.url');
        $this->returnUrl = config('vnpay.return_url');
        $this->version = config('vnpay.version', '2.1.0');
    }

    /**
     * Tạo URL thanh toán VNPay cho booking
     *
     * @param Booking $booking
     * @param string $ipAddress IP của khách hàng
     * @param string|null $bankCode Mã ngân hàng (tùy chọn)
     * @return string URL thanh toán
     * @throws Exception
     */
    public function createPaymentUrl(Booking $booking, string $ipAddress, ?string $bankCode = null): string
    {
        if (empty($this->tmnCode) || empty($this->hashSecret)) {
            throw new Exception(__('errors.VNPAY_NOT_CONFIGURED'));
        }

        // Số tiền thanh toán (nhân 100 để bỏ phần thập phân)
        $amount = (int) $booking->total_price * 100;

        // VNPay yêu cầu thời gian GMT+7 (Asia/Ho_Chi_Minh)
        $timezone = new \DateTimeZone('Asia/Ho_Chi_Minh');
        $now = new \DateTime('now', $timezone);
        $createDate = $now->format('YmdHis');
        
        $expireMinutes = config('vnpay.expire_minutes', 15);
        $expireDateTime = (clone $now)->modify("+{$expireMinutes} minutes");
        $expireDate = $expireDateTime->format('YmdHis');

        // Thông tin đơn hàng (không dấu, không ký tự đặc biệt)
        $orderInfo = $this->removeAccents("Thanh toan ve xem phim - Ma {$booking->code}");

        // Dữ liệu gửi sang VNPay
        $inputData = [
            'vnp_Version' => $this->version,
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => $amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode' => config('vnpay.currency', 'VND'),
            'vnp_IpAddr' => $ipAddress,
            'vnp_Locale' => config('vnpay.locale', 'vn'),
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => config('vnpay.order_type', 'billpayment'),
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_TxnRef' => $booking->code, // Mã đơn hàng duy nhất
            'vnp_ExpireDate' => $expireDate,
        ];

        // Thêm mã ngân hàng nếu có
        if (!empty($bankCode)) {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        // Sắp xếp theo thứ tự alphabet
        ksort($inputData);

        // Tạo chuỗi hash
        $hashData = '';
        $query = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        // Tạo secure hash
        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        // Build URL hoàn chỉnh
        return $this->url . '?' . $query . 'vnp_SecureHash=' . $vnpSecureHash;
    }

    /**
     * Xác thực dữ liệu IPN từ VNPay
     *
     * @param array $data Dữ liệu từ VNPay
     * @return bool
     */
    public function verifyIpnData(array $data): bool
    {
        if (!isset($data['vnp_SecureHash'])) {
            return false;
        }

        $vnpSecureHash = $data['vnp_SecureHash'];
        unset($data['vnp_SecureHash']);
        unset($data['vnp_SecureHashType']);

        // Chỉ lấy các tham số bắt đầu bằng vnp_
        $inputData = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) === 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return $secureHash === $vnpSecureHash;
    }

    /**
     * Kiểm tra giao dịch có thành công không
     *
     * @param array $data Dữ liệu từ VNPay
     * @return bool
     */
    public function isPaymentSuccessful(array $data): bool
    {
        $responseCode = $data['vnp_ResponseCode'] ?? '';
        $transactionStatus = $data['vnp_TransactionStatus'] ?? '';

        return $responseCode === '00' && $transactionStatus === '00';
    }

    /**
     * Lấy thông tin giao dịch từ response VNPay
     *
     * @param array $data
     * @return array
     */
    public function getTransactionInfo(array $data): array
    {
        return [
            'txn_ref' => $data['vnp_TxnRef'] ?? null,              // Mã đơn hàng (booking code)
            'amount' => isset($data['vnp_Amount']) ? (int) $data['vnp_Amount'] / 100 : 0,
            'bank_code' => $data['vnp_BankCode'] ?? null,
            'bank_tran_no' => $data['vnp_BankTranNo'] ?? null,
            'card_type' => $data['vnp_CardType'] ?? null,
            'pay_date' => $data['vnp_PayDate'] ?? null,
            'transaction_no' => $data['vnp_TransactionNo'] ?? null,
            'response_code' => $data['vnp_ResponseCode'] ?? null,
            'transaction_status' => $data['vnp_TransactionStatus'] ?? null,
            'order_info' => $data['vnp_OrderInfo'] ?? null,
        ];
    }

    /**
     * Lấy mô tả mã lỗi VNPay
     *
     * @param string $responseCode
     * @return string
     */
    public function getResponseMessage(string $responseCode): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP)',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Lỗi không xác định',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }

    /**
     * Xóa dấu tiếng Việt
     *
     * @param string $str
     * @return string
     */
    protected function removeAccents(string $str): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];

        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/($accent)/i", $nonAccent, $str);
        }

        return $str;
    }
}
