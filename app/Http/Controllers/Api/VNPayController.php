<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Notification\NotificationDispatcher;
use App\Services\Payment\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    protected VNPayService $vnpayService;
    protected NotificationDispatcher $notificationDispatcher;

    public function __construct(VNPayService $vnpayService, NotificationDispatcher $notificationDispatcher)
    {
        $this->vnpayService = $vnpayService;
        $this->notificationDispatcher = $notificationDispatcher;
    }

    /**
     * IPN URL - Nhận kết quả thanh toán từ VNPay (server-to-server)
     * VNPay sẽ gọi URL này để thông báo kết quả thanh toán
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ipn(Request $request)
    {
        Log::info('VNPay IPN received', $request->all());

        $inputData = $request->all();

        // Verify checksum
        if (!$this->vnpayService->verifyIpnData($inputData)) {
            Log::warning('VNPay IPN: Invalid signature');
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

        // Lấy thông tin giao dịch
        $transactionInfo = $this->vnpayService->getTransactionInfo($inputData);
        $bookingCode = $transactionInfo['txn_ref'];

        // Tìm booking
        $booking = Booking::where('code', $bookingCode)->first();

        if (!$booking) {
            Log::warning("VNPay IPN: Booking not found - {$bookingCode}");
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        // Kiểm tra số tiền
        $vnpAmount = $transactionInfo['amount'];
        if ((int) $booking->total_price !== (int) $vnpAmount) {
            Log::warning("VNPay IPN: Amount mismatch - Booking: {$booking->total_price}, VNPay: {$vnpAmount}");
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        // Kiểm tra trạng thái đơn hàng (tránh xử lý trùng lặp)
        if ($booking->is_paid) {
            Log::info("VNPay IPN: Booking #{$bookingCode} already confirmed");
            return response()->json([
                'RspCode' => '02',
                'Message' => 'Order already confirmed',
            ]);
        }

        // Kiểm tra kết quả thanh toán
        if ($this->vnpayService->isPaymentSuccessful($inputData)) {
            // Thanh toán thành công
            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'paid_at' => now(),
                'payment_intent_id' => $transactionInfo['transaction_no'], // Lưu mã giao dịch VNPay
            ]);

            Log::info("VNPay IPN: Booking #{$bookingCode} marked as paid");

            // Gửi thông báo qua Telegram và FCM
            try {
                $this->notificationDispatcher->dispatchBookingPaid($booking);
            } catch (\Exception $e) {
                Log::error("Failed to send booking paid notification", [
                    'booking_code' => $bookingCode,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Thanh toán thất bại
            $responseCode = $transactionInfo['response_code'];
            $message = $this->vnpayService->getResponseMessage($responseCode);

            $booking->update([
                'status' => 'payment_failed',
            ]);

            Log::info("VNPay IPN: Booking #{$bookingCode} payment failed - {$message}");
        }

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }

    /**
     * Return URL - Redirect user sau khi thanh toán
     * Chỉ dùng để hiển thị kết quả cho user, không cập nhật trạng thái
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function returnUrl(Request $request)
    {
        Log::info('VNPay Return URL', $request->all());

        $inputData = $request->all();

        // Verify checksum
        $isValidSignature = $this->vnpayService->verifyIpnData($inputData);

        if (!$isValidSignature) {
            // Redirect với thông báo lỗi
            return $this->redirectToApp('error', null, 'Invalid signature');
        }

        $transactionInfo = $this->vnpayService->getTransactionInfo($inputData);
        $bookingCode = $transactionInfo['txn_ref'];
        $isSuccess = $this->vnpayService->isPaymentSuccessful($inputData);

        if ($isSuccess) {
            return $this->redirectToApp('success', $bookingCode);
        } else {
            $message = $this->vnpayService->getResponseMessage($transactionInfo['response_code']);
            return $this->redirectToApp('failed', $bookingCode, $message);
        }
    }

    /**
     * Redirect về app mobile
     *
     * @param string $status success|failed|error
     * @param string|null $bookingCode
     * @param string|null $message
     * @return \Illuminate\Http\Response
     */
    protected function redirectToApp(string $status, ?string $bookingCode = null, ?string $message = null)
    {
        // Deep link cho mobile app
        $baseUrl = match ($status) {
            'success' => config('vnpay.success_url', 'myapp://payment/success'),
            'failed' => config('vnpay.failed_url', 'myapp://payment/failed'),
            default => config('vnpay.error_url', 'myapp://payment/error'),
        };

        $params = [];
        if ($bookingCode) {
            $params['booking_code'] = $bookingCode;
        }
        if ($message) {
            $params['message'] = $message;
        }

        $url = $baseUrl . (count($params) > 0 ? '?' . http_build_query($params) : '');

        // Trả về HTML redirect (cho WebView)
        return response()->view('payment.redirect', [
            'redirectUrl' => $url,
            'status' => $status,
            'bookingCode' => $bookingCode,
            'message' => $message,
        ]);
    }
}
