<?php
session_start();
require_once '../../Config/db.php';
require_once '../Models/Users.php';

// Bật báo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['update_profile'])) {
    $user_id = intval($_POST['user_id']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $userModel = new User();

    // Nhúng SweetAlert2
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Thông báo</title>';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body>';

    // Kiểm tra số điện thoại đã tồn tại cho user khác chưa
    $existingPhone = $userModel->findByPhone($phone);
    if ($existingPhone && $existingPhone['id'] != $user_id) {
        ?>
        <script>
        Swal.fire({
            title: 'Lỗi!',
            text: 'Số điện thoại đã được sử dụng!',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../Views/Pages/account.php';
        });
        </script>
        <?php
        echo '</body></html>';
        exit();
    }

    $success = $userModel->updateProfile($user_id, $full_name, $phone, $address);

    if ($success) {
        // Cập nhật lại session tên nếu cần
        $_SESSION['full_name'] = $full_name;
        ?>
        <script>
        Swal.fire({
            title: 'Thành công!',
            text: 'Cập nhật thông tin thành công!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../Views/Pages/account.php';
        });
        </script>
        <?php
    } else {
        ?>
        <script>
        Swal.fire({
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra, vui lòng thử lại!',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../Views/Pages/account.php';
        });
        </script>
        <?php
    }

    echo '</body></html>';
    exit();
} else {
    header("Location: ../Views/Pages/account.php");
    exit();
}
