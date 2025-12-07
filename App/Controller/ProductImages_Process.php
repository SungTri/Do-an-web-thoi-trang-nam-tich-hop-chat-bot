<?php
require_once __DIR__ . '/../Models/ProductImages.php';
$productImagesModel = new ProductImages();

// Xóa ảnh chi tiết
if (isset($_POST['delete_detail_image'])) {
    $image_id = intval($_POST['image_id']);
    $productImagesModel->deleteImage($image_id);
    header("Location: ../admin/admin_products.php");
    exit();
}
?>