<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../Config/db.php';
require_once __DIR__ . '/../Models/Categories.php';
$categoryModel = new Category();

// Thêm danh mục mới
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $success = $categoryModel->create($name);
    $status = $success ? 'success' : 'error';
    $message = $success ? 'Thêm danh mục thành công!' : 'Thêm danh mục thất bại!';
    header("Location: ../Views/Admin/Admin_Category.php?status=$status&message=" . urlencode($message));
    exit();
}

// Sửa danh mục
if (isset($_POST['edit_category'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $success = $categoryModel->update($id, $name);
    $status = $success ? 'success' : 'error';
    $message = $success ? 'Sửa danh mục thành công!' : 'Sửa danh mục thất bại!';
    header("Location: ../Views/Admin/Admin_Category.php?status=$status&message=" . urlencode($message));
    exit();
}

// Xóa danh mục
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $success = $categoryModel->delete($id);
    $status = $success ? 'success' : 'error';
    $message = $success ? 'Xóa danh mục thành công!' : 'Xóa danh mục thất bại!';
    header("Location: ../Views/Admin/Admin_Category.php?status=$status&message=" . urlencode($message));
    exit();
}
// Lấy tất cả danh mục
if (isset($_GET['action']) && $_GET['action'] === 'get_all') {
    $categories = $categoryModel->getAll();
    // Trả về $categories cho view xử lý
}

// Lấy danh mục theo ID
if (isset($_GET['action']) && $_GET['action'] === 'get_by_id' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $category = $categoryModel->findById($id);
    // Trả về $category cho view xử lý
}
?>