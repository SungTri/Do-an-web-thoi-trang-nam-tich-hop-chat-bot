<?php
require_once __DIR__ . '/../Models/Order.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_POST['checkout_next'])) {
    $fullname  = $_POST['fullname'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $city      = $_POST['city_name'];
    $district  = $_POST['district_name'];
    $ward      = $_POST['ward_name'];
    $address   = $_POST['shipping_address'];
    $promo_code = $_POST['promo_code'] ?? null;
    
    $full_shipping_address = $address . ', ' . $ward . ', ' . $district . ', ' . $city;
    $cart_items  = json_decode($_POST['cart_items'], true);
    $total_price = floatval($_POST['total_price']);

    // Xử lý mã giảm giá
    $discount = 0;
    $promo = null;
    $original_price = $total_price;

    if (!empty($promo_code)) {
        $orderModel = new Order();
        $promo = $orderModel->applyPromotion($promo_code);
        if ($promo) {
            $discount = ($promo['discount_percentage'] / 100) * $total_price;
            $total_price -= $discount;
        }
    }

    // Lưu vào session để Confirm_payment.php dùng
    $_SESSION['confirm_data'] = [
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'shipping_address' => $full_shipping_address,
        'promo_code' => $promo_code,
        'cart_items' => $cart_items,
        'original_price' => $original_price,
        'discount' => $discount,
        'final_price' => $total_price,
        'promo' => $promo
    ];

    header("Location: ../Views/Pages/Confirm_payment.php");
    exit;
}


?>
