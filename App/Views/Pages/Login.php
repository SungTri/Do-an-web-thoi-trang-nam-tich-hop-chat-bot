<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="../../../public/css/style.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="main-content">
    <div class="login-container">
        <h2>Đăng Nhập</h2>
        <form action="../../Controller/Login_Process.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <div style="position:relative;">
                <input type="password" name="password" id="password" placeholder="Mật khẩu" required style="padding-right:40px;">
                <span id="togglePassword" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="fa-solid fa-eye" id="eyeOpen"></i>
                    <i class="fa-solid fa-eye-slash" id="eyeClosed" style="display:none;"></i>
                </span>
            </div>
            <button type="submit" name="login">Đăng Nhập</button>
            <a href="Forgot_Password.php" class="forgot-link">Quên Mật Khẩu?</a>
        </form>
        <p>Chưa có tài khoản? <a href="Register.php">Đăng Ký</a></p>
    </div>
</div>
<script>
    
</script>
</body>
</html>
<?php include __DIR__ . '/../Partials/footer.php'; ?>