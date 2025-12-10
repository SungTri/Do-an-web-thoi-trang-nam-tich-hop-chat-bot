<?php
session_start();
if (!isset($_SESSION['order_success'])) {
    $orders = $_SESSION['orders'] ?? [];
} else {
    $order_data = $_SESSION['order_success'];
    $order           = $order_data['order'];
    $items           = $order_data['items'];
    $shipping_status = $order_data['shipping_status'];
    $updated_at      = $order_data['updated_at'];
    unset($_SESSION['order_success']);
}

include __DIR__ . '/../Partials/header.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách đơn hàng</title>
    <link rel="stylesheet" href="/WebThoiTrangNam/Public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/script.js"></script>
</head>
<body>
<div class="main-contents">
    <div class="order-container">
        <?php if (isset($order)): ?>
            <h2>Đơn Hàng Mới Đặt</h2>
            <p>Mã đơn: <?= $order['id'] ?></p>
            <p>Ngày đặt: <?= isset($order['order_date']) ? htmlspecialchars($order['order_date']) : '-' ?></p>
            <p>Tổng tiền: <?= number_format($order['total_price'], 0, ',', '.') ?>đ</p>
            <h3>Sản phẩm:</h3>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <?php if (!empty($item['image_url'])): ?>
                        <img src="../../../Public/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" >
                        <?php endif; ?>
                        <?= htmlspecialchars($item['name']) ?> x<?= htmlspecialchars($item['quantity']) ?>
                        <?= isset($item['size']) ? ' (' . htmlspecialchars($item['size']) . ')' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="../Pages/cart.php" class="btn">Quay lại giỏ hàng</a>

        <?php elseif (!empty($orders)): ?>
            <h2>Danh Sách Tất Cả Đơn Hàng</h2>
            <?php foreach ($orders as $order): ?>
                <div class="order-box">
                    <p><strong>Mã đơn:</strong> #<?= $order['id'] ?></p>
                    <p><strong>Ngày đặt:</strong> <?= $order['order_date'] ?></p>
                    <p><strong>Thanh toán:</strong> <?= $order['payment_status'] ?></p>
                    <p><strong>Vận chuyển:</strong> <?= $order['shipping_status'] ?></p>
                    <p><strong>Tổng tiền:</strong> <?= number_format($order['total_price'], 0, ',', '.') ?>đ</p>
                    <div>
                        <strong>Sản phẩm:</strong>
                        <ul>
                            <?php if (!empty($order['items']) && is_array($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <li>
                                        <?php if (!empty($item['image_url'])): ?>
                                            <a href="ProductDetail.php?id=<?= $item['product_id'] ?>">
                                                <img src="../../../Public/<?= htmlspecialchars($item['image_url']) ?>" 
                                                    alt="<?= htmlspecialchars($item['name']) ?>" style="max-width:80px;">
                                            </a>
                                        <?php endif; ?>
                                        <a href="ProductDetail.php?id=<?= $item['product_id'] ?>">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </a> 
                                        -x<?= htmlspecialchars($item['quantity']) ?>
                                        <?= isset($item['size']) ? ' (' . htmlspecialchars($item['size']) . ')' : '' ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Không có sản phẩm nào trong đơn hàng này.</li>
                            <?php endif; ?>
                        </ul>
                        <?php $status = $order['order_status'] ?? 'Đang xử lý'; 
                        if ($status !== 'Hủy' && $status !== 'Hoàn thành'): ?>
                            <div class="order-actions" style="margin-top: 10px;">
                                <button type="button" class="btn btn-cancel" onclick="confirmCancel(<?= $order['id'] ?>)">
                                    ❌ Hủy đơn hàng
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> 
            <?php endforeach; ?>
        <?php else: ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php include __DIR__ . '/../Partials/footer.php'; ?>
