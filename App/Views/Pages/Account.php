<?php
require_once '../../Models/Users.php'; // Đúng đường dẫn model
include '../Partials/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Bạn cần đăng nhập để xem thông tin tài khoản!'); window.location.href='Login.php';</script>";
    exit();
}

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thông tin tài khoản</title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
<div class="main-content">
    <div class="account-container">
        <h2>Thông tin tài khoản</h2>
        <form action="../../Controller/UpdateProfile_Process.php" method="POST">
            <label>Họ và tên:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            <label>Số điện thoại:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            <label>Địa chỉ:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <button type="submit" name="update_profile">Cập nhật thông tin</button>
        </form>
        <p>Vai trò: <?php echo htmlspecialchars($role); ?></p>
        <a href="logout.php">Đăng xuất</a>
        <?php if ($role == 'customer') { ?>
            <a href="../../Controller/OrderController.php?action=success" class="btn">Xem đơn hàng đã đặt</a>
        <?php } elseif ($role == 'admin') { ?>
            <a href="../admin/Admin_Order.php" class="btn">Quản Lý Đơn Hàng</a>
            <a href="../admin/Admin_Products.php" class="btn">Quản Lý Sản Phẩm</a>
            <a href="../admin/Admin_Category.php" class="btn">Quản Lý Danh Mục</a>
            <a href="../admin/Reports.php" class="btn">Báo Cáo Thống Kê</a>
            <a href="../admin/Admin_Promotion.php" class="btn">Quản Lý Khuyến Mại</a>
            <a href="../admin/Admin_Account.php" class="btn">Quản Lý Tài Khoản</a>
            <a href="../admin/Admin_Review.php" class="btn">Quản Lý Góp Ý</a>
        <?php } ?>
    </div>
</div>
<?php include '../Partials/footer.php'; ?>
</body>
</html>