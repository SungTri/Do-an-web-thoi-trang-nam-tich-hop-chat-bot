<?php
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Shipping.php';
require_once __DIR__ . '/../Models/Payment.php';

$orderModel = new Order();
$shippingModel = new Shipping();
$paymentModel = new Payment();

$search = $_GET['search'] ?? '';

if ($search) {
    $orders = $orderModel->searchOrders($search);
} else {
    $orders = $orderModel->getAll();
}

// Quản lý đơn hàng
if(isset($_POST['update_order_status'])){
    $id = intval($_POST['id']);
    $status = $_POST['order_status'];
    $success = $orderModel->updateStatus($id,$status);
    header("Location: ../Views/Admin/Admin_Order.php?message=".($success?'edit_success':'edit_error'));
    exit;
}

if(isset($_GET['action']) && $_GET['action']=='delete' && !empty($_GET['id'])){
    $id = intval($_GET['id']);
    $success = $orderModel->delete($id);
    header("Location: ../Views/Admin/Admin_Order.php?message=".($success?'delete_success':'delete_error'));
    exit;
}

// Quản lý vận chuyển
if(isset($_POST['update_shipping_status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $success = $shippingModel->updateStatus($order_id,$status);
    header("Location: ../Views/Admin/Admin_Order.php?message=".($success?'edit_success':'edit_error'));
    exit;
}

// Quản lý thanh toán
if(isset($_POST['update_payment_status'])){
    $id = intval($_POST['id']);
    $status = $_POST['payment_status'];
    $success = $paymentModel->updateStatus($id,$status);
    header("Location: ../Views/Admin/Admin_Order.php?message=".($success?'edit_success':'edit_error'));
    exit;
}
?>
