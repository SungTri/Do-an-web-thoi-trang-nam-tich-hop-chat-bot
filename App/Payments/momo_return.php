<?php
session_start();
require_once __DIR__ . '/../../Config/db.php';
require_once '../Models/Payment.php';
require_once '../Models/Order.php';

$paymentModel = new Payment($conn);
$orderModel = new Order($conn);

if (!isset($_GET['resultCode'])) {
    die("Không có dữ liệu trả về!");
}

// Lấy thông tin từ MoMo
$resultCode = $_GET['resultCode'];
$orderId = $_GET['orderId'] ?? '';
$amount = $_GET['amount'] ?? '';
$message = $_GET['message'] ?? '';

if ($resultCode == '0') {
    // ✅ Thanh toán thành công
    // TODO: Cập nhật trạng thái thanh toán trong DB
    // $paymentModel->updatePaymentStatus($order_id, 'Đã thanh toán');
    
    echo "<!DOCTYPE html>
    <html lang='vi'>
    <head>
        <meta charset='UTF-8'>
        <title>Thanh toán thành công</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thanh toán thành công!',
                text: 'Đơn hàng của bạn đã được thanh toán.',
                confirmButtonText: 'Xem đơn hàng'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/WebThoiTrangNam/App/Views/Pages/index.php';
                }
            });
        </script>
    </body>
    </html>";
} else {
    // ❌ Thanh toán thất bại
    echo "<!DOCTYPE html>
    <html lang='vi'>
    <head>
        <meta charset='UTF-8'>
        <title>Thanh toán thất bại</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Thanh toán thất bại!',
                text: '{$message}',
                confirmButtonText: 'Thử lại'
            }).then((result) => {
                window.location.href = '/WebThoiTrangNam/App/Views/Pages/Confirm_payment.php';
            });
        </script>
    </body>
    </html>";
}
?>
