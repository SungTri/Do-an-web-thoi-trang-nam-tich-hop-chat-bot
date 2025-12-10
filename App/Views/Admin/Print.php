<?php
require_once __DIR__ . '/../../Controller/Order_Process.php'; // hoặc model Order trực tiếp
require_once __DIR__ . '/../../Models/Order.php'; // hoặc model Order trực tiếp
$orderId = $_GET['id'] ?? null;
if(!$orderId) exit("Không có đơn hàng");

$order = $orderModel->findById($orderId);
$orderDetails = $orderModel->getDetailsByOrderId($orderId);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đơn hàng #<?= $orderId ?></title>
<style>
body { font-family: Arial; padding: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #333; padding: 8px; text-align: left; }
h2 { text-align: center; }
</style>
</head>
<body>
<h2>Đơn hàng #<?= $orderId ?></h2>
<p>Khách hàng: <?= htmlspecialchars($order['full_name']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
<p>Địa chỉ: <?= htmlspecialchars($order['shipping_address']) ?></p>
<p>Ngày tạo: <?= $order['created_at'] ?></p>

<table>
<tr>
    <th>Sản phẩm</th>
    <th>Số lượng</th>
    <th>Giá</th>
    <th>Size</th>
</tr>
<?php foreach($orderDetails as $d): ?>
<tr>
    <td><?= htmlspecialchars($d['product_name']) ?></td>
    <td><?= $d['quantity'] ?></td>
    <td><?= number_format($d['price'],0,',','.') ?> đ</td>
    <td><?= htmlspecialchars($d['size']) ?></td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="3" style="text-align:right"><strong>Tổng tiền:</strong></td>
    <td><?= number_format($order['total_price'],0,',','.') ?> đ</td>
</tr>
</table>

<script>
window.onload = function() {
    window.print(); // tự động mở hộp thoại in khi load trang
};
</script>
</body>
</html>
