<?php
include '../Partials/header.php';
require_once '../../Controller/ProductList_Process.php';
require_once '../../Controller/Category_Process.php';
// Lấy tất cả danh mục để hiển thị bộ lọc
$categoryModel = new Category();
$categories = $categoryModel->getAll();
// Lấy tham số lọc/tìm kiếm
$action = $_GET['action'] ?? null;
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$search_name = $_GET['name'] ?? null;

$category_name = "Tất cả sản phẩm";
$products = [];

// Nếu có danh mục
if ($category_id) {
    $category = $categoryModel->findById($category_id);
    if ($category) {
        $category_name = $category['name'];
    }
}

// Lấy sản phẩm theo action
if ($action === "search" && $search_name) {
    $products = $productModel->searchByName($search_name);

} elseif ($action === "category_price_asc" && $category_id) {
    $products = $productModel->filterByCategoryPriceAsc($category_id);

} elseif ($action === "category_price_desc" && $category_id) {
    $products = $productModel->filterByCategoryPriceDesc($category_id);

} elseif ($action === "price_asc") {
    $products = $productModel->getAllByPriceAsc();

} elseif ($action === "price_desc") {
    $products = $productModel->getAllByPriceDesc();

} elseif ($category_id) {
    $products = $productModel->findByCategory($category_id);

} else {
    $products = $productModel->getAll();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Shop - <?= htmlspecialchars($category_name) ?></title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
<div class="main-contents">
    <!-- Bộ lọc -->
    <form method="get" action="Shop.php" class="filter-form">
        <!-- Chọn danh mục -->
        <select name="category_id" onchange="this.form.submit()">
            <option value="">Tất cả sản phẩm</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Chọn sắp xếp -->
        <select name="action" onchange="this.form.submit()">
            <option value="">Mặc định</option>
            <option value="price_asc" <?= ($action === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
            <option value="price_desc" <?= ($action === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
            <?php if ($category_id): ?>
                <option value="category_price_asc" <?= ($action === 'category_price_asc') ? 'selected' : '' ?>>Giá tăng dần (trong danh mục)</option>
                <option value="category_price_desc" <?= ($action === 'category_price_desc') ? 'selected' : '' ?>>Giá giảm dần (trong danh mục)</option>
            <?php endif; ?>
        </select>

        <!-- Ô tìm kiếm -->
       <input type="text" name="name" placeholder="Tìm sản phẩm..." 
       value="<?= htmlspecialchars($search_name ?? '') ?>">
       <button type="submit" name="action" value="search">Tìm</button>
    </form>
    <!-- Danh sách sản phẩm -->
    <div class="product-list">
        <?php if (!empty($products)) { ?>
            <?php foreach ($products as $row): ?>
                <?php $secondaryImage = $productModel->getSecondaryImage($row['id']); ?>
                <div class="product-item">
                    <a href="ProductDetail.php?id=<?= $row['id'] ?>">
                        <!-- Ảnh chính -->
                        <img src="../../../Public/<?= htmlspecialchars($row['image_url']) ?>"
                            alt="<?= htmlspecialchars($row['name']) ?>"
                            class="primary">

                        <!-- Ảnh hover (secondary) nếu có -->
                        <?php if ($secondaryImage): ?>
                            <img src="../../../Public/<?= htmlspecialchars($secondaryImage) ?>" 
                                alt="<?= htmlspecialchars($row['name']) ?> hover" 
                                class="secondary">
                        <?php endif; ?>
                    </a>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p><?= number_format($row['price'], 0, ',', '.') ?>đ</p>
                </div>
            <?php endforeach; ?>
        <?php } else { ?>
            <p class="no-products">Không có sản phẩm nào.</p>
        <?php } ?>
    </div>
</div>
<div class="separator"></div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
