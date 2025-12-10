<?php
session_start();
require_once __DIR__ . '/../Models/Review.php';
require_once __DIR__ . '/../../Config/db.php';

$reviewModel = new Review($conn);

// Thêm đánh giá
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if ($reviewModel->hasPurchased($user_id, $product_id)) {
        if ($reviewModel->addReview($user_id, $product_id, $rating, $review)) {
            echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location.href='../../App/Views/Pages/ProductDetail.php?id=$product_id';</script>";
        } else {
            echo "<script>alert('Lỗi khi gửi đánh giá!');</script>";
        }
    } else {
        echo "<script>alert('Bạn chỉ có thể đánh giá sản phẩm đã mua!');</script>";
    }
} else {
    echo "<script>alert('Vui lòng đăng nhập để gửi đánh giá!'); window.location.href='../../App/Views/Pages/Login.php';</script>";
}
