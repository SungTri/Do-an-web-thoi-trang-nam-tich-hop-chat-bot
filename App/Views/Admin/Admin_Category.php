<?php
require_once __DIR__ . '/../../Controller/Category_Process.php';
include '../Partials/header.php';

// --- Xử lý tìm kiếm ---
$keyword = $_GET['keyword'] ?? '';

if (!empty($keyword)) {
    // Tìm kiếm danh mục theo keyword
    $categories = $categoryModel->searchByName($keyword);
} else {
    // Hiển thị toàn bộ danh mục
    $categories = $categoryModel->getAll();
}

// Kiểm tra nếu có edit_id từ GET để hiển thị form sửa
$editId = $_GET['edit_id'] ?? null;
$editCategory = null;
if ($editId) {
    $editCategory = $categoryModel->findById(intval($editId));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alerts.js"></script>

    <?php if(isset($_GET['message']) && isset($_GET['status'])): ?>
    <script>
        const message = "<?= htmlspecialchars($_GET['message']) ?>";
        const status = "<?= htmlspecialchars($_GET['status']) ?>";

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

<h2>QUẢN LÝ DANH MỤC SẢN PHẨM</h2>

<!-- Form tìm kiếm -->
<form method="GET" style="margin-bottom: 20px;">
    <input type="text" name="keyword" placeholder="Tìm danh mục..."
           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
           style="padding: 6px; width: 250px;">

    <button type="submit" class="btn btn-add">Tìm kiếm</button>

    <!-- Nút reset về toàn bộ danh sách -->
    <a href="Admin_Category.php" class="btn btn-delete">Reset</a>
</form>

<!-- Form thêm / sửa danh mục -->
<div class="form-container">
    <h3><?= $editCategory ? "Sửa danh mục #{$editCategory['id']}" : "Thêm danh mục mới" ?></h3>

    <form action="../../Controller/Category_Process.php" method="POST">
        <?php if ($editCategory): ?>
            <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
            <input type="hidden" name="edit_category" value="1">
        <?php else: ?>
            <input type="hidden" name="add_category" value="1">
        <?php endif; ?>

        <label>Tên danh mục:</label>
        <input type="text" name="name" value="<?= $editCategory['name'] ?? '' ?>" required>

        <button type="submit" class="btn <?= $editCategory ? 'btn-edit' : 'btn-add' ?>">
            <?= $editCategory ? "Cập nhật" : "Thêm danh mục" ?>
        </button>

        <?php if ($editCategory): ?>
            <a href="Admin_Category.php" class="btn btn-delete">Hủy</a>
        <?php endif; ?>
    </form>
</div>

<!-- Bảng danh sách danh mục -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
        <tr>
            <td data-label="ID"><?= $cat['id'] ?></td>
            <td data-label="Tên danh mục"><?= htmlspecialchars($cat['name']) ?></td>
            <td data-label="Ngày tạo"><?= $cat['created_at'] ?></td>

            <td data-label="Hành động">
                <a href="?edit_id=<?= $cat['id'] ?>" class="btn btn-edit">Sửa</a>

                <a href="../../Controller/Category_Process.php?action=delete&id=<?= $cat['id'] ?>" 
                   onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')" 
                   class="btn btn-delete">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align:center; padding:15px;">
                Không tìm thấy danh mục nào.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</div>
</body>
</html>

<?php include '../Partials/footer.php'; ?>
