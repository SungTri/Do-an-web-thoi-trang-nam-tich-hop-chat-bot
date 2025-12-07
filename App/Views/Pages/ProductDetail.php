<?php
include '../Partials/header.php';
require_once '../../Controller/ProductList_Process.php';
require_once '../../Controller/ProductImages_Process.php';
require_once '../../Models/Review.php'; // thêm model Review

// Lấy ID sản phẩm từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy sản phẩm!'); window.location.href='Shop.php';</script>";
    exit;
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$product = $productModel->findById($product_id);
if (!$product) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='Shop.php';</script>";
    exit;
}

// Lấy ảnh chi tiết sản phẩm
$images = $productImagesModel->getImagesByProductId($product_id);
if (empty($images)) {
    $images[] = $product['image_url'];
}

// Khởi tạo model Review
$reviewModel = new Review($conn);
$reviews = $reviewModel->getReviewsByProduct($product_id);

// Kiểm tra người dùng đã mua sản phẩm chưa (nếu đăng nhập)
$isLoggedIn = isset($_SESSION['user_id']);
$userHasPurchased = false;
if ($isLoggedIn) {
    $userHasPurchased = $reviewModel->hasPurchased($_SESSION['user_id'], $product_id);
}
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';

// Lấy sản phẩm liên quan
$relatedProducts = $productModel->findByCategory($product['category_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
<div class="product-detail">
    <!-- Hình ảnh -->
    <div class="image-gallery">
        <div class="main-image">
            <img id="main-img" src="../../../Public/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="thumbnails">
            <?php foreach ($images as $img): ?>
                <img src="../../../Public/<?= htmlspecialchars($img) ?>" onclick="changeMainImage(this.src)" class="thumbnail">
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Thông tin -->
    <div class="product-info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p class="price"><?= number_format($product['price'], 0, ',', '.') ?>đ</p>

        <label for="size">Chọn Size:</label>
        <select id="size" name="size" required>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
        </select>

        <div class="buttons">
            <?php if ($product['stock'] > 0): ?>
                <form action="../../Controller/Cart_Process.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="size" id="selected-size" value="S">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                    <button type="submit" name="add_to_cart">Thêm Vào Giỏ</button>
                </form> 
            <?php else: ?>
                <p class="out-of-stock">Sản phẩm đã hết hàng</p>
            <?php endif; ?>
        </div>

        <div class="description">
            <h3>Mô Tả Sản Phẩm</h3>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
    </div>
</div>
<hr>

<!-- Đánh giá -->
<div class="product-reviews">
    <h3>Đánh Giá Sản Phẩm</h3>

    <?php if ($isLoggedIn): ?>
        <?php if ($userHasPurchased): ?>
            <form action="../../Controller/Review_Process.php" method="POST" class="review-form">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <label>Chọn Đánh Giá:</label>
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ - Rất tốt</option>
                    <option value="4">⭐⭐⭐⭐ - Tốt</option>
                    <option value="3">⭐⭐⭐ - Bình thường</option>
                    <option value="2">⭐⭐ - Tệ</option>
                    <option value="1">⭐ - Rất tệ</option>
                </select>
                <textarea name="review" rows="3" required placeholder="Nhận xét của bạn..."></textarea>
                <button type="submit">Gửi Đánh Giá</button>
            </form>
        <?php else: ?>
            <p><i>Bạn cần mua sản phẩm này để đánh giá.</i></p>
        <?php endif; ?>
    <?php else: ?>
        <p><i>Vui lòng <a href='../Pages/Login.php'>đăng nhập</a> để đánh giá.</i></p>
    <?php endif; ?>

    <h3>Đánh giá từ khách hàng:</h3>
    <?php foreach ($reviews as $review): ?>
        <div class="review">
            <strong><?= htmlspecialchars($review['full_name']) ?></strong>
            <?= str_repeat('⭐', $review['rating']) ?>
            <p><?= htmlspecialchars($review['review']) ?></p>
            <small><?= $review['created_at'] ?></small>

            <!-- Hiển thị phản hồi admin nếu có -->
            <?php
                $responses = $reviewModel->getResponses($review['review_id']);
                foreach ($responses as $response):
            ?>
                <div class="response">
                    <strong>Quý Xốp</strong>
                    <p><?= htmlspecialchars($response['response']) ?></p>
                    <small><?= $response['created_at'] ?></small>
                </div>
            <?php endforeach; ?>

            <!-- Form phản hồi admin -->
            <?php if ($isAdmin): ?>
                <form action="../../Controller/Response_Process.php" method="POST" class="response-form">
                    <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <label for="response">Phản hồi:</label>
                    <textarea name="response" rows="2" required placeholder="Phản hồi của bạn..."></textarea>
                    <button type="submit">Gửi phản hồi</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<hr>

<!-- Sản phẩm liên quan -->
<div class="related-products">
    <h3>OTHERS HAVE VIEWED</h3>
    <div class="product-list">
        <?php foreach ($relatedProducts as $related): ?>
            <?php if ($related['id'] != $product['id']): ?>
            <div class="product-item">
                <a href="../Pages/ProductDetail.php?id=<?= $related['id'] ?>">
                    <img src="../../../Public/<?= htmlspecialchars($related['image_url']) ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                </a>
                <h3><?= htmlspecialchars($related['name']) ?></h3>
                <p><?= number_format($related['price'], 0, ',', '.') ?>đ</p>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById("main-img").src = src;
}
document.getElementById("size").addEventListener("change", function() {
    document.getElementById("selected-size").value = this.value;
    document.getElementById("selected-size-buy").value = this.value;
});
</script>

</body>
</html>
<?php include '../Partials/footer.php'; ?>
