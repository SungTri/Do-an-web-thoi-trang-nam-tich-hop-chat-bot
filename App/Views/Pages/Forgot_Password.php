<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../../../public/css/style.css">
</head>
<body>
    <div class="main-content">
       <div class="forget-container">
        <h2>Quên mật khẩu</h2>
        <form action="../../Controller/ForgotPass_Process.php" method="POST">
                <label for="email">Nhập email của bạn:</label>
                <input type="email" name="email" id="email" required placeholder="Email">
                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" id="new_password" required placeholder="Mật khẩu mới">
                <button type="submit" name="reset_password">Đặt lại mật khẩu</button>
        </form>
        <p><a href="Login.php">Quay lại đăng nhập</a></p>
       </div>
    </div>
</body>
</html>