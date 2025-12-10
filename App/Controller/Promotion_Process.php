<?php
require_once __DIR__ . '/../Models/Promotion.php';

class PromotionController {
    private $promotionModel;

    public function __construct() {
        $this->promotionModel = new Promotion(); // tự khởi tạo Model
    }

    // Lấy danh sách promotions
    public function index() {
        return $this->promotionModel->getAll();
    }

    // Xử lý thêm promotion
    public function add($data) {
        return $this->promotionModel->create($data);
    }

    // Xử lý sửa promotion
    public function edit($data) {
        return $this->promotionModel->update($data);
    }

    // Xử lý xóa promotion
    public function delete($id) {
        return $this->promotionModel->delete($id);
    }
}

// =============================
// Router nhỏ cho thao tác CRUD
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PromotionController(); // ❌ bỏ $conn đi

    if (isset($_POST['add_promotion'])) {
    $success = $controller->add($_POST);
    $msg = $success ? 'Thêm khuyến mại thành công' : 'Thêm khuyến mại thất bại';
    $status = $success ? 'success' : 'error';
    header("Location: ../Views/Admin/Admin_Promotion.php?status=$status&message=" . urlencode($msg));
    exit;
    }

    if (isset($_POST['update_promotion'])) {
        $success = $controller->edit($_POST);
        $msg = $success ? 'Cập nhật khuyến mại thành công' : 'Cập nhật khuyến mại thất bại';
        $status = $success ? 'success' : 'error';
        header("Location: ../Views/Admin/Admin_Promotion.php?status=$status&message=" . urlencode($msg));
        exit;
    }

    if (isset($_POST['delete_promotion'])) {
        $success = $controller->delete($_POST['id']);
        $msg = $success ? 'Xóa khuyến mại thành công' : 'Xóa khuyến mại thất bại';
        $status = $success ? 'success' : 'error';
        header("Location: ../Views/Admin/Admin_Promotion.php?status=$status&message=" . urlencode($msg));
        exit;
    }

}

