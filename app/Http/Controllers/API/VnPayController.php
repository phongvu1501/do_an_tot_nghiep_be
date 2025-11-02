<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class VnPayController extends Controller
{
    const PAYMENT_EXPIRATION_MINUTES = 15; // 15 phút

    public function createPayment($data, Request $request) {
        // Lấy thông tin config:
        $vnp_TmnCode = config('vnpay.vnp_TmnCode'); // Mã website của bạn tại VNPAY
        $vnp_HashSecret = config('vnpay.vnp_HashSecret'); // Chuỗi bí mật
        $vnp_Url = config('vnpay.vnp_URL'); // URL thanh toán của VNPAY
        $vnp_ReturnUrl = config('vnpay.vnp_ReturnURL'); // URL nhận kết quả trả về

        $data = (object)$data;

        // Thông tin đơn hàng, thanh toán
        $vnp_TxnRef = $data->code;
        $vnp_OrderInfo = $data->info;
        $vnp_OrderType =  $data->type;
        $vnp_Amount = $data->total * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = $data->bankCode;  // Mã ngân hàng
        $vnp_IpAddr = $request->ip(); // Địa chỉ IP

        // Tạo input data để gửi sang VNPay server
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => date('YmdHis', strtotime('+' . self::PAYMENT_EXPIRATION_MINUTES . ' minutes'))
            );
            // Kiểm tra nếu mã ngân hàng đã được thiết lập và không rỗng
            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            // Kiểm tra nếu thông tin tỉnh/thành phố hóa đơn đã được thiết lập và không rỗng
            if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                $inputData['vnp_Bill_State'] = $vnp_Bill_State; // Gán thông tin tỉnh/thành phố hóa đơn vào mảng dữ liệu input
            }

            // Sắp xếp mảng dữ liệu input theo thứ tự bảng chữ cái của key
            ksort($inputData);

            $query = ""; // Biến lưu trữ chuỗi truy vấn (query string)
            $i = 0; // Biến đếm để kiểm tra lần đầu tiên
            $hashdata = ""; // Biến lưu trữ dữ liệu để tạo mã băm (hash data)

            // Duyệt qua từng phần tử trong mảng dữ liệu input
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    // Nếu không phải lần đầu tiên, thêm ký tự '&' trước mỗi cặp key=value
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    // Nếu là lần đầu tiên, không thêm ký tự '&'
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1; // Đánh dấu đã qua lần đầu tiên
                }
                // Xây dựng chuỗi truy vấn
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            // Gán chuỗi truy vấn vào URL của VNPay
            $vnp_Url = $vnp_Url . "?" . $query;

            // Kiểm tra nếu chuỗi bí mật hash secret đã được thiết lập
            if (isset($vnp_HashSecret)) {
                // Tạo mã băm bảo mật (Secure Hash) bằng cách sử dụng thuật toán SHA-512 với hash secret
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                // Thêm mã băm bảo mật vào URL để đảm bảo tính toàn vẹn của dữ liệu
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            return $vnp_Url;
    }

    public function vnpayReturn(Request $request)
    {
        $reservation = Reservation::where('reservation_code', $request->vnp_TxnRef)->first();

        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->all();

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, config('vnpay.vnp_HashSecret'));

        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Thanh toán thành công cọc
                $reservation->update([
                    'status' => 'deposit_paid',
                ]);

                // Thanh toán thành công số tiền còn lại
                if ($reservation->status !== 'deposit_paid') {
                    $reservation->update([
                        'status' => 'completed',
                    ]);
                }


                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công!',
                    'data' => $inputData
                ]);
            } else {
                // Thanh toán không thành công

                return response()->json([
                    'success' => false,
                    'message' => 'Thanh toán không thành công!',
                ]);
            }
        } else {
            // Dữ liệu không hợp lệ
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ!',
            ]);
        }
    }

}
