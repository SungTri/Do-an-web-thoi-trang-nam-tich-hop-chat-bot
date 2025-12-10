<?php
session_start();
require_once __DIR__ . '/../../Config/db.php';
require_once __DIR__ . '/../Models/Payment.php';

$config = require __DIR__ . '/../../Config/vnpay_config.php';
$vnp_HashSecret = $config['vnp_HashSecret'];

// Lấy secure hash từ VNPay trả về
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? null;
if (!$vnp_SecureHash) {
    die("Thiếu tham số vnp_SecureHash từ VNPay!");
}

// Gom dữ liệu trả về
$inputData = [];
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

// Loại bỏ vnp_SecureHash để tính lại
unset($inputData['vnp_SecureHash']);
ksort($inputData);

// Build chuỗi hash (không urldecode!)
$hashData = http_build_query($inputData, '', '&');
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

// Kiểm tra checksum
if ($secureHash === $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] === '00') {
        // Lấy order_id từ VNPay trả về (thường là vnp_TxnRef)
        $order_id = $_GET['vnp_TxnRef'];
        $user_id = $_SESSION['user_id'] ?? null;

        // Cập nhật trạng thái thanh toán
        $paymentModel = new Payment($conn);
        $paymentModel->updatePaymentStatus($order_id, 'Đã thanh toán');

        // Chuyển về trang danh sách đơn hàng
        echo "<script>alert('Thanh toán thành công!'); window.location.href='../Views/Pages/Order_Success.php';</script>";
        exit;
    } else {
        echo "<h2>❌ Thanh toán thất bại!</h2>";
        echo "<p>Lý do: " . htmlspecialchars($_GET['vnp_ResponseCode']) . "</p>";
    }
} else {
    echo "Sai checksum!";
}