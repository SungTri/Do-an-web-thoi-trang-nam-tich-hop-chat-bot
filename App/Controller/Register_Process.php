<?php
require_once '../../Config/db.php';
require_once '../Models/Users.php';

// Bật báo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $userModel = new User();

    // Kiểm tra email hoặc số điện thoại đã tồn tại chưa
    $existingUser = $userModel->findByEmail($email);
    $existingPhone = $userModel->findByPhone($phone);

    if ($existingUser || $existingPhone) {
        echo "<script>alert('Email hoặc số điện thoại đã tồn tại!'); window.location.href='../Views/Pages/register.php';</script>";
        exit();
    }

    // Thêm người dùng vào database (mặc định role = 'customer')
    $success = $userModel->create($email, $password, $full_name, $phone, $address, 'customer');

    if (!$success) {
        echo "<script>alert('Lỗi khi đăng ký!'); window.location.href='../Views/Pages/register.php';</script>";
        exit();
    }
    header("Location: ../Views/Pages/login.php?message=success");
    exit();
}
?>