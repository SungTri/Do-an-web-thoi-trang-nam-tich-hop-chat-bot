<?php
session_start();

// Kết nối DB
require_once __DIR__ . '/../../Config/db.php';

// Load Models
require_once '../Models/Order.php';
require_once '../Models/Payment.php';
require_once '../Models/Shipping.php';
require_once __DIR__ . '/../Models/Cart.php';

// Khởi tạo models với $conn
$orderModel    = new Order($conn);
$paymentModel  = new Payment($conn);
$shippingModel = new Shipping($conn);
$cartModel     = new Cart($conn);

if (isset($_POST['confirm_payment'])) {
    $user_id        = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $data           = $_SESSION['confirm_data']; 
    $cart_items     = $data['cart_items'];

    // 1. Tạo đơn hàng
    $order_id = $orderModel->createOrder(
        $user_id,
        $data['final_price'],
        $data['shipping_address']
    );

    if (!$order_id) die("Lỗi khi tạo đơn hàng");

    // 2. Lưu chi tiết đơn hàng + cập nhật tồn kho + xóa giỏ
    foreach ($cart_items as $item) {
        $orderModel->addOrderDetail(
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['size']
        );

        $orderModel->updateStock($item['product_id'], $item['quantity']);

        // Xóa giỏ hàng
        $cartModel->removeFromCartItem($user_id, $item['product_id'], $item['size']);
        if (isset($_SESSION['cart_items'][$item['product_id']][$item['size']])) {
            unset($_SESSION['cart_items'][$item['product_id']][$item['size']]);
            if (empty($_SESSION['cart_items'][$item['product_id']])) {
                unset($_SESSION['cart_items'][$item['product_id']]);
            }
        }
    }

    // 3. Trạng thái vận chuyển
    $shippingModel->createShippingStatus($order_id);

    // 4. Xử lý thanh toán
    if ($payment_method === 'COD') {

        // COD: chưa thanh toán
        $paymentModel->createPayment(
            $order_id,
            $user_id,
            $data['final_price'],
            'COD',
            'Chưa thanh toán'
        );

        echo "<!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <title>Đặt hàng thành công</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script src='/WebThoiTrangNam/Public/js/alerts.js'></script>
        </head>
        <body>
            <script>
                showSuccess('Đặt hàng thành công! Đơn hàng của bạn đã được ghi nhận.',
                            '../Controller/OrderController.php?order_id={$order_id}');
            </script>
        </body>
        </html>";
        exit;

    } elseif ($payment_method === 'VNPAY') {

        $paymentModel->createPayment(
            $order_id,
            $user_id,
            $data['final_price'],
            'VNPAY',
            'Đã thanh toán'
        );

        header("Location: ../Payments/vnpay_payment.php?order_id=$order_id&amount=" . $data['final_price']);
        exit;

    } elseif ($payment_method === 'MOMO') {

        // 🔹 Tạo bản ghi thanh toán (trạng thái: Chờ thanh toán)
        $paymentModel->createPayment(
            $order_id,
            $user_id,
            $data['final_price'],
            'MOMO',
            'Chờ thanh toán'
        );

        // 🔹 Điều hướng sang trang tạo URL thanh toán MoMo
        $amount = $data['final_price'];
        header("Location: ../Payments/momo_payment.php?order_id={$order_id}&amount={$amount}");
        exit;
        
    } else {
        // ⬅️ THÊM: Xử lý trường hợp payment_method không hợp lệ
        die("Phương thức thanh toán không hợp lệ!");
    }
}
?>