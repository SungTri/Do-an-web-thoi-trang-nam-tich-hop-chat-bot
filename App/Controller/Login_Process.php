<?php
session_start();
include '../../Config/db.php';
include '../Models/Users.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$max_attempts = 5;
$lock_time = 300;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

$message = '';
$type = '';
$redirect = '../Views/Pages/Login.php'; // mặc định

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    // Kiểm tra số lần sai
    if ($_SESSION['login_attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lock_time) {
        $message = 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 5 phút!';
        $type = 'warning';
    } elseif (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['login_attempts'] += 1;
        $_SESSION['last_attempt_time'] = time();
        $message = 'Email hoặc mật khẩu không đúng!';
        $type = 'error';
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        $message = 'Đăng nhập thành công!';
        $type = 'success';
        $redirect = '../Views/Pages/index.php';
    }
} else {
    $message = 'Phương thức không hợp lệ!';
    $type = 'error';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    title: "<?= $type == 'success' ? 'Thành công' : ($type == 'warning' ? 'Cảnh báo' : 'Lỗi') ?>",
    text: "<?= htmlspecialchars($message) ?>",
    icon: "<?= $type ?>",
    confirmButtonText: "OK"
}).then(() => {
    window.location.href = "<?= $redirect ?>";
});
</script>
</body>
</html>
