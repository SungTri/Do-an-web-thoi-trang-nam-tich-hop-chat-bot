<?php
require_once '../../Config/db.php';
require_once '../Models/Users.php';

// Bật báo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $newPassword = trim($_POST['new_password']);

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if (!$user) {
        echo "<script>alert('Email không tồn tại!'); window.location.href='../Views/Pages/Forgot_Password.php';</script>";
        exit();
    }

    $success = $userModel->resetPassword($email, $newPassword);

    if ($success) {
        echo "<script>alert('Đặt lại mật khẩu thành công!'); window.location.href='../Views/Pages/Login.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); window.location.href='../Views/Pages/Forgot_Password.php';</script>";
    }
    exit();
}
?>