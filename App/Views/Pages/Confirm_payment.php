<?php 
include '../Partials/header.php'; 
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['confirm_data'])) {
    die("<script>alert('Không có dữ liệu xác nhận!'); window.location.href='Checkout.php';</script>");
}

$data = $_SESSION['confirm_data'];
$fullname = $data['fullname'];
$email = $data['email'];
$phone = $data['phone'];
$shipping_address = $data['shipping_address'];
$promo_code = $data['promo_code'];
$cart_items = $data['cart_items'];
$original_price = $data['original_price'];
$discount = $data['discount'];
$total_price = $data['final_price'];
$promo = $data['promo'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Xác Nhận Thanh Toán</title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
<div class="main-content">
    <div class="confirm-payment-container">
        <h2>Xác Nhận Thanh Toán</h2>
        <div class="customer-info">
            <p><strong>Người nhận:</strong> <?= htmlspecialchars($fullname) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($phone) ?></p>
            <p><strong>Địa chỉ:</strong> 
                <?= htmlspecialchars($shipping_address) ?>, 
            </p>
        </div>
        <h3>Sản phẩm đã chọn</h3>
        <div class="cart-summary">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="../../../Public/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="item-info">
                        <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                        <p>Kích thước: <?= htmlspecialchars($item['size'] ?? 'N/A') ?></p>
                        <p>Số lượng: <?= (int)$item['quantity'] ?></p>
                        <p>Giá: <?= number_format($item['price'], 0, ',', '.') ?>đ</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="total-row">
            <strong>Tạm tính:</strong>
            <span><?= number_format($original_price, 0, ',', '.') ?>đ</span>
        </div>
        <?php if ($discount > 0): ?>
            <div class="total-row" style="color:green;">
                <strong>Giảm giá (<?= $promo['discount_percentage'] ?>%):</strong>
                <span>-<?= number_format($discount, 0, ',', '.') ?>đ</span>
            </div>
        <?php endif; ?>
        <div class="total-row">
            <strong>Tổng cộng:</strong>
            <strong style="color:#d00;"><?= number_format($total_price, 0, ',', '.') ?>đ</strong>
        </div>

        <form action="../../Controller/Payment_Process.php" method="POST">
            <!-- Truyền dữ liệu cần thiết -->
            <input type="hidden" name="confirm_payment" value="1">
            <label>Chọn phương thức thanh toán:</label>
            <select name="payment_method" required>
                <option value="">-- Chọn phương thức --</option>
                <option value="COD">Thanh toán khi nhận hàng</option>
                <option value="VNPAY">Thanh toán qua VNPay</option>
                <option value="MOMO">Thanh toán qua MoMo</option>
            </select>
            <button type="submit">Xác Nhận Thanh Toán</button>
        </form>
    </div>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
