<?php
session_start();
require_once __DIR__ . '/../../Config/db.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../../Config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('Bạn cần đăng nhập để xem đơn hàng!'); window.location.href='../Pages/Login.php';</script>");
}

$orderModel = new Order($conn);
$user_id    = $_SESSION['user_id'];

// ==========================
// Trường hợp: Xem chi tiết một đơn hàng
// ==========================
if (isset($_GET['order_id']) && empty($_GET['action'])) {
    $order_id = intval($_GET['order_id']);

    // Lấy thông tin đơn hàng
    $order = $orderModel->getOrderById($order_id, $user_id);
    if ($order) {
        // Lấy trạng thái vận chuyển
        $shipping = $orderModel->getShippingStatus($order_id);
        $shipping_status = $shipping ? $shipping['status'] : "Chưa có thông tin";
        $updated_at      = $shipping ? $shipping['updated_at'] : "-";

        // Lấy danh sách sản phẩm
        $items = $orderModel->getOrderItems($order_id);

        // Lưu vào session để view hiển thị
        $_SESSION['order_success'] = [
            'order'           => $order,
            'items'           => $items,
            'shipping_status' => $shipping_status,
            'updated_at'      => $updated_at
        ];

        // Redirect sang view để đổi URL
        header("Location: ../Views/Pages/Order_Success.php");
        exit;
    }
}
// ==========================
// Trường hợp: Hiển thị sau khi thanh toán thành công
// ==========================
if (isset($_GET['action']) && $_GET['action'] === 'success') {
    if (!empty($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);

        // Lấy thông tin đơn hàng
        $order = $orderModel->getOrderById($order_id, $user_id);
        if ($order) {
            // Lấy trạng thái vận chuyển
            $shipping = $orderModel->getShippingStatus($order_id);
            $shipping_status = $shipping ? $shipping['status'] : "Chưa có thông tin";
            $updated_at      = $shipping ? $shipping['updated_at'] : "-";

            // Lấy danh sách sản phẩm
            $items = $orderModel->getOrderItems($order_id);

            // Lưu vào session để hiển thị ngay ở view
            $_SESSION['order_success'] = [
                'order'           => $order,
                'items'           => $items,
                'shipping_status' => $shipping_status,
                'updated_at'      => $updated_at
            ];

            header("Location: ../Views/Pages/Order_Success.php");
            exit;
        }
    }
    // fallback: hiển thị toàn bộ đơn
    $orders = $orderModel->getOrdersByUser($user_id);
    // ✅ gắn thêm sản phẩm cho từng đơn
    foreach ($orders as &$ord) {
        $ord['items'] = $orderModel->getOrderItems($ord['id']);
    }
    unset($ord);

    $_SESSION['orders'] = $orders;

    // Điều hướng sang file View để đổi URL
    header("Location: ../Views/Pages/Order_Success.php");
    exit;
}
// ==========================
// Trường hợp: Hủy đơn hàng
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cancel') {
    $order_id = intval($_POST['order_id']);
    $success = $orderModel->cancelOrder($order_id, $user_id);

    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Hủy đơn hàng</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: "<?= $success ? 'Thành công!' : 'Thất bại!' ?>",
            text: "<?= $success ? 'Đã hủy đơn hàng thành công!' : 'Hủy đơn hàng thất bại!' ?>",
            icon: "<?= $success ? 'success' : 'error' ?>",
            confirmButtonText: "OK"
        }).then(() => {
            window.location.href='../Views/Pages/Order_Success.php';
        });
    </script>
    </body>
    </html>
    <?php

    if ($success) {
        // Refresh lại danh sách đơn hàng trong session
        $orders = $orderModel->getOrdersByUser($user_id);
        foreach ($orders as &$ord) {
            $ord['items'] = $orderModel->getOrderItems($ord['id']);
        }
        unset($ord);
        $_SESSION['orders'] = $orders;
    }

    exit;
}
// ==========================
// Nếu không có tham số phù hợp → quay lại Shop
// ==========================
header("Location: ../Pages/Shop.php");
exit;
