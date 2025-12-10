<?php
require_once __DIR__ . '/../../Controller/Promotion_Process.php';
include '../Partials/header.php';

// Khởi tạo controller
$promotionController = new PromotionController();
$promotions = $promotionController->index();

// Kiểm tra nếu có edit_id từ GET để hiển thị form sửa
$editId = $_GET['edit_id'] ?? null;
$editPromotion = null;
if ($editId) {
    $editPromotion = $promotionController->index();
    $editPromotion = array_filter($editPromotion, fn($p) => $p['id'] == intval($editId));
    $editPromotion = $editPromotion ? array_values($editPromotion)[0] : null;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khuyến mại</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alerts.js"></script>
    <?php if(isset($_GET['status']) && isset($_GET['message'])): ?>
<script>
    const status = "<?= htmlspecialchars($_GET['status']) ?>";
    const message = "<?= htmlspecialchars($_GET['message']) ?>";

    if(status === 'success') {
        showSuccess(message);
    } else if(status === 'error') {
        showError(message);
    }
</script>
<?php endif; ?>
</head>
<body>
<div class="main-contents">
<h2>QUẢN LÝ KHUYẾN MẠI</h2>

<!-- Form thêm / sửa khuyến mại -->
<div class="form-container">
    <h3><?= $editPromotion ? "Sửa khuyến mại #{$editPromotion['id']}" : "Thêm khuyến mại mới" ?></h3>
    <form action="../../Controller/Promotion_Process.php" method="POST">
        <?php if ($editPromotion): ?>
            <input type="hidden" name="id" value="<?= $editPromotion['id'] ?>">
            <input type="hidden" name="update_promotion" value="1">
        <?php else: ?>
            <input type="hidden" name="add_promotion" value="1">
        <?php endif; ?>

        <label>Mã khuyến mại:</label>
        <input type="text" name="code" value="<?= $editPromotion['code'] ?? '' ?>" required>

        <label>Phần trăm giảm giá (%):</label>
        <input type="number" name="discount_percentage" value="<?= $editPromotion['discount_percentage'] ?? '' ?>" min="0" max="100" required>

        <label>Ngày bắt đầu:</label>
        <input type="date" name="start_date" value="<?= $editPromotion['start_date'] ?? '' ?>" required>

        <label>Ngày kết thúc:</label>
        <input type="date" name="end_date" value="<?= $editPromotion['end_date'] ?? '' ?>" required>

        <button type="submit" class="btn <?= $editPromotion ? 'btn-edit' : 'btn-add' ?>">
            <?= $editPromotion ? "Cập nhật" : "Thêm khuyến mại" ?>
        </button>
        <?php if ($editPromotion): ?>
            <a href="Admin_Promotion.php" class="btn btn-delete">Hủy</a>
        <?php endif; ?>
    </form>
</div>

<!-- Bảng danh sách khuyến mại -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Mã khuyến mại</th>
            <th>Giảm giá (%)</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($promotions)): ?>
        <?php foreach ($promotions as $promo): ?>
        <tr>
            <td data-label="ID"><?= $promo['id'] ?></td>
            <td data-label="Mã khuyến mại"><?= htmlspecialchars($promo['code']) ?></td>
            <td data-label="Giảm giá"><?= $promo['discount_percentage'] ?>%</td>
            <td data-label="Ngày bắt đầu"><?= $promo['start_date'] ?></td>
            <td data-label="Ngày kết thúc"><?= $promo['end_date'] ?></td>
            <td data-label="Hành động">
                <a href="?edit_id=<?= $promo['id'] ?>" class="btn btn-edit">Sửa</a>
                <form action="../../Controller/Promotion_Process.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mại này?')">
                    <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                    <button type="submit" name="delete_promotion" class="btn btn-delete">Xóa</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">Chưa có khuyến mại nào.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>

