<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../Config/db.php';
require_once __DIR__ . '/../Models/Users.php';

$userModel = new User();

// ----------------------
// Thêm user mới
// ----------------------
if (isset($_POST['add_user'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $name = trim($_POST['full_name']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = $_POST['role'] ?? 'customer';

    $success = $userModel->create($email, $password, $name, $phone, $address, $role);

    if ($success) {
        header("Location: ../Views/Admin/Admin_Account.php?message=add_success");
    } else {
        header("Location: ../Views/Admin/Admin_Account.php?message=add_error");
    }
    exit();
}

// ----------------------
// Sửa user
// ----------------------
if (isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $data = [
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'role' => $_POST['role'] ?? 'customer'
    ];

    // Nếu có thay đổi mật khẩu
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $success = $userModel->update($id, $data);

    if ($success) {
        header("Location: ../Views/Admin/Admin_Account.php?message=edit_success");
    } else {
        header("Location: ../Views/Admin/Admin_Account.php?message=edit_error");
    }
    exit();
}

// ----------------------
// Xóa user
// ----------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $success = $userModel->delete($id);

    if ($success) {
        header("Location: ../Views/Admin/Admin_Account.php?message=delete_success");
    } else {
        header("Location: ../Views/Admin/Admin_Account.php?message=delete_error");
    }
    exit();
}

// ----------------------
// Reset mật khẩu
// ----------------------
if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $newPassword = trim($_POST['new_password']);
    $success = $userModel->resetPassword($email, $newPassword);

    if ($success) {
        header("Location: ../Views/Admin/Admin_Account.php?message=reset_success");
    } else {
        header("Location: ../Views/Admin/Admin_Account.php?message=reset_error");
    }
    exit();
}

// ----------------------
// Lấy danh sách tất cả user (cho view)
// ----------------------
if (isset($_GET['action']) && $_GET['action'] === 'get_all') {
    $query = "SELECT * FROM users ORDER BY id DESC";
    $result = $conn->query($query);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    // Trả về $users cho view
}
?>
