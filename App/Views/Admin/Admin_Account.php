<?php 
require_once __DIR__ . '/../../Controller/User_Process.php';
include '../Partials/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tài khoản</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alert.js"></script>
</head>
<body>
    <div class="main-contents">
    <h2>QUẢN LÝ TÀI KHOẢN</h2>

    <!-- Form thêm / sửa user -->
    <div class="form-container">
        <h3><?= isset($_GET['edit_id']) ? "Sửa tài khoản" : "Thêm tài khoản mới" ?></h3>
        <form action="../../Controller/User_Process.php" method="POST">
            <?php
            $editUser = null;
            if (isset($_GET['edit_id'])) {
                $editUser = $userModel->findById(intval($_GET['edit_id']));
            }
            ?>
            <input type="hidden" name="<?= $editUser ? 'edit_user' : 'add_user' ?>" value="1">
            <?php if($editUser): ?>
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>

            <label>Họ và tên:</label>
            <input type="text" name="full_name" value="<?= $editUser['full_name'] ?? '' ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $editUser['email'] ?? '' ?>" required>

            <label>Số điện thoại:</label>
            <input type="text" name="phone" value="<?= $editUser['phone'] ?? '' ?>">

            <label>Địa chỉ:</label>
            <input type="text" name="address" value="<?= $editUser['address'] ?? '' ?>">

            <label>Vai trò:</label>
            <select name="role">
                <option value="customer" <?= ($editUser && $editUser['role']=='customer') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= ($editUser && $editUser['role']=='admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <label>Mật khẩu: <?= $editUser ? '<small>(để trống nếu không đổi)</small>' : '' ?></label>
            <input type="password" name="password" <?= $editUser ? '' : 'required' ?>>

            <button type="submit" class="btn <?= $editUser ? 'btn-edit' : 'btn-add' ?>">
                <?= $editUser ? 'Cập nhật' : 'Thêm tài khoản' ?>
            </button>
        </form>
    </div>

    <!-- Danh sách user -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = $conn->query("SELECT * FROM users ORDER BY id DESC");
            if ($users && $users->num_rows > 0):
                while ($u = $users->fetch_assoc()):
            ?>
            <tr>
                <td data-label="ID"><?= $u['id'] ?></td>
                <td data-label="Họ và tên"><?= htmlspecialchars($u['full_name']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                <td data-label="Số điện thoại"><?= htmlspecialchars($u['phone']) ?></td>
                <td data-label="Địa chỉ"><?= htmlspecialchars($u['address']) ?></td>
                <td data-label="Vai trò"><?= htmlspecialchars($u['role']) ?></td>
                <td data-label="Hành động">
                    <a href="?edit_id=<?= $u['id'] ?>" class="btn btn-edit">Sửa</a>
                    <button class="btn btn-delete" onclick="confirmDelete(<?= $u['id'] ?>)">Xóa</button>
                </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr><td colspan="7">Chưa có tài khoản nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        function confirmDelete(id) {
            showConfirm('Bạn có chắc muốn xóa tài khoản này?').then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../../Controller/User_Process.php?action=delete&id=" + id;
                }
            });
        }

        // Hiển thị thông báo thành công/ lỗi từ URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        if (message) {
            let text = '';
            switch(message) {
                case 'add_success': text='Thêm tài khoản thành công!'; showSuccess(text); break;
                case 'add_error': text='Thêm tài khoản thất bại!'; showError(text); break;
                case 'edit_success': text='Cập nhật tài khoản thành công!'; showSuccess(text); break;
                case 'edit_error': text='Cập nhật tài khoản thất bại!'; showError(text); break;
                case 'delete_success': text='Xóa tài khoản thành công!'; showSuccess(text); break;
                case 'delete_error': text='Xóa tài khoản thất bại!'; showError(text); break;
            }
        }
    </script>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
