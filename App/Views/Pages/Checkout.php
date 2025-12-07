<?php 
include '../Partials/header.php'; 
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='../Pages/login.php';</script>";
    exit;
}
$cart_items = $_SESSION['checkout_items'] ?? [];
$total_price = $_SESSION['checkout_total'] ?? 0;
// 🔹 Lưu giỏ hàng vào session để Confirm_payment.php dùng
$_SESSION['cart_items'] = $cart_items;
$_SESSION['total_price'] = $total_price;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
    <div class="main-contents">
      <div class="checkout-container">
        <div class="checkout-form">
            <h2>Thông tin giao hàng</h2>
            <form action="../../Controller/ConfirmPayment_Process.php" method="POST">
                <div class="row">
                    <input type="text" name="fullname" placeholder="Họ và tên" required>
                </div>
                <div class="row">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="row">
                    <input type="text" name="shipping_address" placeholder="Địa chỉ" required>
                </div>
                <div class="row">
                    <select name="city" id="city" required></select>
                    <select name="district" id="district" required></select>
                    <select name="ward" id="ward" required></select>
                </div>
                <div class="row">
                    <input type="text" name="promo_code" id="promo_code" placeholder="Mã giảm giá (nếu có)">
                </div>

                <input type="hidden" name="city_name" id="city_name">
                <input type="hidden" name="district_name" id="district_name">
                <input type="hidden" name="ward_name" id="ward_name">
                <input type="hidden" name="cart_items" value='<?= json_encode($cart_items) ?>'>
                <input type="hidden" name="total_price" value="<?= $total_price ?>">

                <button type="submit" name="checkout_next">Tiếp Tục Đến Thanh Toán</button>
            </form>
        </div>
        <div class="cart-summary">
            <h3>Giỏ hàng</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="../../../Public/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="item-info">
                        <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                        <p><?= htmlspecialchars($item['size'] ?? 'N/A') ?></p>
                        <p>Số lượng: <?= $item['quantity'] ?></p>
                        <p><?= number_format($item['price'], 0, ',', '.') ?>đ</p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="total-row"> 
                <span>Tạm tính</span>
                <span><?= number_format($total_price, 0, ',', '.') ?>đ</span>
            </div>
            <div class="total-row">
                <span>Phí vận chuyển</span>
                <span>—</span>
            </div>
            <div class="total-row" style="margin-top:20px;">
                <strong>Tổng cộng</strong>
                <strong style="color:#222;"><?= number_format($total_price, 0, ',', '.') ?>đ</strong>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>