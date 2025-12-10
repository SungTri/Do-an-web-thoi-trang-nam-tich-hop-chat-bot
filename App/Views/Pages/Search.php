<?php
include '../Partials/header.php';
require_once '../../Controller/ProductList_Process.php';

// Lấy từ khóa tìm kiếm
$search_name = $_GET['name'] ?? null;
$products = [];

if ($search_name) {
    $products = $productModel->searchByName($search_name);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Tìm kiếm sản phẩm</title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
<div class="main-contents">
    <!-- Danh sách sản phẩm -->
    <div class="product-list">
        <?php if ($search_name && !empty($products)) { ?>
            <?php foreach ($products as $row) { ?>
                <div class="product-item">
                    <a href="ProductDetail.php?id=<?= $row['id'] ?>">
                        <img src="../../../Public/<?= htmlspecialchars($row['image_url']) ?>"
                             alt="<?= htmlspecialchars($row['name']) ?>"
                             onerror="this.onerror=null; this.src='../images/default.png'">
                    </a>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p>Giá: <?= number_format($row['price'], 0, ',', '.') ?>đ</p>
                </div>
            <?php } ?>
        <?php } elseif ($search_name) { ?>
            <p class="no-products">Không tìm thấy sản phẩm nào phù hợp với từ khóa "<b><?= htmlspecialchars($search_name) ?></b>".</p>
        <?php } else { ?>
            <p class="no-products">Hãy nhập từ khóa để tìm kiếm sản phẩm.</p>
        <?php } ?>
    </div>
</div>
<div class="separator"></div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
