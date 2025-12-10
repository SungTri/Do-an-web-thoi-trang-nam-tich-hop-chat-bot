<?php
session_start();
require_once __DIR__ . '/../Models/Review.php';
require_once __DIR__ . '/../../Config/db.php';

class ReviewController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new Review();
    }

    // Xóa 1 đánh giá
    public function deleteReview($review_id) {
        return $this->reviewModel->deleteReview($review_id);
    }

    // Xóa tất cả đánh giá theo sản phẩm
    public function deleteAllReviews($product_id) {
        return $this->reviewModel->deleteAllReviewsByProduct($product_id);
    }
}

// =============================
// Router nhỏ cho thao tác CRUD
// =============================
$controller = new ReviewController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Xóa 1 đánh giá
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['review_id']) && isset($_GET['product_id'])) {
        $success = $controller->deleteReview(intval($_GET['review_id']));
        $msg = $success ? 'Xóa đánh giá thành công' : 'Xóa đánh giá thất bại';
        $status = $success ? 'success' : 'error';
        header("Location: ../Views/Admin/Admin_Review.php?product_id=" . intval($_GET['product_id']) . "&status=$status&message=" . urlencode($msg));
        exit;
    }

    // Xóa tất cả đánh giá của 1 sản phẩm
    if (isset($_GET['action']) && $_GET['action'] === 'deleteAll' && isset($_GET['product_id'])) {
        $success = $controller->deleteAllReviews(intval($_GET['product_id']));
        $msg = $success ? 'Xóa tất cả đánh giá & phản hồi thành công' : 'Xóa tất cả thất bại';
        $status = $success ? 'success' : 'error';
        header("Location: ../Views/Admin/Admin_Review.php?product_id=" . intval($_GET['product_id']) . "&status=$status&message=" . urlencode($msg));
        exit;
    }
}
