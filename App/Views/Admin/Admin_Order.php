<?php
require_once __DIR__ . '/../../Controller/Order_Process.php';
include '../Partials/header.php';

$orders = $orderModel->getAll();
$payments = $paymentModel->getAll();
$shippings = $shippingModel->getAll();

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
if ($search) {
    $orders = array_filter($orders, function($o) use ($search) {
        return strpos($o['full_name'], $search) !== false || 
               strpos($o['email'], $search) !== false ||
               strpos($o['id'], $search) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alert.js"></script>
    <script src="../../../Public/js/script.js"></script>
</head>
<body>
<div class="main-contents">
<h2>QUẢN LÝ ĐƠN HÀNG</h2>

<div style="margin-bottom:15px;">
    <form method="GET">
        <input type="text" name="search" placeholder="Tìm kiếm theo ID, khách hàng..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-add">Tìm kiếm</button>
        <a href="Admin_Order.php" class="btn btn-edit">Xóa tìm kiếm</a>
    </form>
</div>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Khách hàng</th>
    <th>Tổng tiền</th>
    <th>Trạng thái đơn hàng</th>
    <th>Trạng thái thanh toán</th>
    <th>Trạng thái vận chuyển</th>
    <th>Địa chỉ giao hàng</th>
    <th>Ngày tạo</th>
    <th>Hành động</th>
</tr>
</thead>
<tbody>
<?php if(!empty($orders)): foreach($orders as $o): 
    $payment = array_filter($payments, fn($p) => $p['order_id']==$o['id']);
    $payment = $payment ? array_values($payment)[0] : null;

    $shipping = array_filter($shippings, fn($s) => $s['order_id']==$o['id']);
    $shipping = $shipping ? array_values($shipping)[0] : null;
?>
<tr>
    <td data-label="ID"><?= $o['id'] ?></td>
    <td data-label="Khách hàng"><?= htmlspecialchars($o['full_name']) ?> (<?= htmlspecialchars($o['email']) ?>)</td>
    <td data-label="Tổng tiền"><?= number_format($o['total_price'],0,',','.') ?> đ</td>
    
    <!-- Trạng thái đơn hàng -->
    <td data-label="Trạng thái">
    <?php if($o['order_status'] === 'Hủy'): ?>
        <span><?= $o['order_status'] ?></span>
    <?php else: ?>
        <form action="../../Controller/Order_Process.php" method="POST">
            <input type="hidden" name="id" value="<?= $o['id'] ?>">
            <select name="order_status" onchange="this.form.submit()">
                <?php 
                $statuses = ['Chờ xác nhận','Đang xử lý','Đang giao','Hoàn thành','Hủy'];
                foreach($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $o['order_status']==$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="update_order_status" value="1">
        </form>
    <?php endif; ?>
    </td>

    <!-- Trạng thái thanh toán -->
    <td data-label="Thanh toán">
    <?php if($payment): ?>
        <form action="../../Controller/Order_Process.php" method="POST">
            <input type="hidden" name="id" value="<?= $payment['id'] ?>">
            <select name="payment_status" onchange="this.form.submit()">
                <?php 
                $p_statuses = ['Chưa thanh toán','Đã thanh toán','Đang xử lý'];
                foreach($p_statuses as $st): ?>
                    <option value="<?= $st ?>" <?= $payment['payment_status']==$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="update_payment_status" value="1">
        </form>
    <?php else: ?>
        <span>Chưa có</span>
    <?php endif; ?>
    </td>

    <!-- Trạng thái vận chuyển -->
    <td data-label="Vận chuyển">
    <?php if($shipping): ?>
        <form action="../../Controller/Order_Process.php" method="POST">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="status" onchange="this.form.submit()">
                <?php 
                $s_statuses = ['Chờ xác nhận','Đang giao','Đã giao','Hủy đơn'];
                foreach($s_statuses as $st): ?>
                    <option value="<?= $st ?>" <?= $shipping['status']==$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="update_shipping_status" value="1">
        </form>
    <?php else: ?>
        <span>Chưa có</span>
    <?php endif; ?>
    </td>

    <td data-label="Địa chỉ"><?= htmlspecialchars($o['shipping_address']) ?></td>
    <td data-label="Ngày tạo"><?= $o['created_at'] ?></td>

    <td data-label="Hành động">
        <a href="../../Controller/Order_Process.php?action=delete&id=<?= $o['id'] ?>" 
        onclick="event.preventDefault(); showConfirm('Bạn có chắc muốn xóa đơn hàng này?').then((res)=>{ if(res.isConfirmed){ window.location.href=this.href; }})" 
        class="btn btn-delete">Xóa</a>
        <button type="button" class="btn btn-print" onclick="printOrder(<?= $o['id'] ?>)">Print</button>
    </td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="9">Chưa có đơn hàng nào.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
