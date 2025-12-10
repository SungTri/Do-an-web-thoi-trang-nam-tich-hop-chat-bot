<?php
require_once __DIR__ . '/../../Controller/ProductList_Process.php'; 
require_once __DIR__ . '/../../Models/Categories.php';
require_once __DIR__ . '/../../Models/ProductImages.php';
include '../Partials/header.php';
$categoryModel = new Category();
$categories = $categoryModel->getAll();
$imageModel = new ProductImages();
// Kiểm tra nếu có edit_id từ GET (hoặc POST) để hiển thị form sửa
$editId = $_GET['edit_id'] ?? null;
$editProduct = null;
if ($editId) {
    $editProduct = $productModel->findById(intval($editId));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
</head>
<body>
  <div class="main-contents">
    <h2>QUẢN LÝ SẢN PHẨM</h2>

    <!-- Form thêm / sửa sản phẩm -->
    <div class="form-container">
        <h3><?= $editProduct ? "Sửa sản phẩm #{$editProduct['id']}" : "Thêm sản phẩm mới" ?></h3>
        <form action="../../Controller/ProductList_Process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $editProduct ? "update" : "create" ?>">
            <?php if($editProduct): ?>
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                <input type="hidden" name="old_image" value="<?= $editProduct['image_url'] ?>">
            <?php endif; ?>

            <label>Danh mục:</label>
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($editProduct && $editProduct['category_id']==$cat['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tên sản phẩm:</label>
            <input type="text" name="name" value="<?= $editProduct['name'] ?? '' ?>" required>

            <label>Mô tả:</label>
            <textarea name="description"><?= $editProduct['description'] ?? '' ?></textarea>

            <label>Giá:</label>
            <input type="number" step="0.01" name="price" value="<?= $editProduct['price'] ?? '' ?>" required>

            <label>Số lượng:</label>
            <input type="number" name="stock" value="<?= $editProduct['stock'] ?? '' ?>" required>

            <label>Ảnh chính:</label>
            <input type="file" name="image_url" accept="image/*">
            <?php if($editProduct): ?>
                <img src="../../../Public/<?= $editProduct['image_url'] ?>" alt="Ảnh cũ">
            <?php endif; ?>

            <label>Ảnh phụ:</label>
            <input type="file" name="sub_images[]" multiple accept="image/*">
            <button type="submit" class="btn <?= $editProduct ? "btn-edit" : "btn-add" ?>">
                <?= $editProduct ? "Cập nhật" : "Thêm sản phẩm" ?>
            </button>
        </form>
    </div>

    <!-- THANH TÌM KIẾM -->
    <form method="GET" class="search-box" style="margin: 20px 0;">
        <input type="text" name="keyword" placeholder="Tìm theo tên sản phẩm..."
            value="<?= $_GET['keyword'] ?? '' ?>" 
            style="padding: 8px; width: 250px;">

        <button type="submit" class="btn btn-search">Tìm kiếm</button>

        <a href="Admin_Products.php" class="btn btn-reset">Reset</a>
    </form>

    <!-- Danh sách sản phẩm -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Ảnh phụ</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Danh mục</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
            <tr>
                <td data-label="ID"><?= $p['id'] ?></td>
                <td data-label="Ảnh"><img src="../../../Public/<?= htmlspecialchars($p['image_url']) ?>" alt=""></td>
                <td data-label="Ảnh phụ">
                    <?php
                    $subImages = $imageModel->getImagesByProductId($p['id']);
                    if (!empty($subImages)) {
                        foreach ($subImages as $img) {
                            echo '<img src="../../../Public/' . htmlspecialchars($img) . '" alt="Ảnh phụ">';
                        }
                    } else {
                        echo 'Chưa có';
                    }
                    ?>
                </td>
                <td data-label="Tên sản phẩm"><?= htmlspecialchars($p['name']) ?></td>
                <td data-label="Giá"><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
                <td data-label="Tồn kho"><?= $p['stock'] ?></td>
                <td data-label="Danh mục"><?= $p['category_id'] ?></td>
                <td data-label="Ngày tạo"><?= $p['created_at'] ?></td>
                <td data-label="Hành động">
                    <a href="?edit_id=<?= $p['id'] ?>" class="btn btn-edit">Sửa</a>
                    <a href="../../Controller/ProductList_Process.php?action=delete&id=<?= $p['id'] ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" 
                       class="btn btn-delete">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9">Không tìm thấy sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
