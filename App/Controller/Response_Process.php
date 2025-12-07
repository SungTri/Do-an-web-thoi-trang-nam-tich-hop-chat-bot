<?php
session_start();
require_once __DIR__ . '/../Models/ReviewResponses.php';
require_once __DIR__ . '/../../Config/db.php';

// Chỉ admin mới được phép
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = intval($_POST['review_id']);
    $product_id = intval($_POST['product_id']);
    $response_text = trim($_POST['response']);

    if (empty($response_text)) {
        echo "<script>alert('Phản hồi không được để trống!'); window.history.back();</script>";
        exit;
    }

    $responseModel = new ReviewResponse($conn);
    $added = $responseModel->addResponse($review_id, $response_text);

    if ($added) {
        echo "<script>alert('Phản hồi đã được gửi!'); window.location.href='../Views/Pages/ProductDetail.php?id=$product_id';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Phương thức không hợp lệ!'); window.history.back();</script>";
}
