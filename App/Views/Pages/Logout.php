<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng xuất</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    title: 'Đăng xuất!',
    text: 'Bạn đã đăng xuất thành công!',
    icon: 'success',
    confirmButtonText: 'OK'
}).then(() => {
    window.location.href = '../Pages/index.php';
});
</script>
</body>
</html>
