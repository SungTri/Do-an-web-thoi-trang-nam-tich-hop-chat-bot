<?php
require_once __DIR__ . '/../../Config/db.php'; // Đường dẫn đến file db.php

class User
{
    private $conn;

    public function __construct()
    {
        global $conn; // Lấy biến $conn từ db.php
        $this->conn = $conn;
    }

    // Lấy thông tin user theo ID
    public function findById($id)
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }

    // Lấy thông tin user theo email
    public function findByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }

    // Đăng nhập: trả về user nếu đúng, null nếu sai
    public function login($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    // Tạo user mới
    public function create($email, $password, $name, $phone = '', $address = '', $role = 'customer')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (full_name, email, password, phone, address, role) 
              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hash, $phone, $address, $role);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    // Cập nhật thông tin user
    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        $types = '';

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
            $types .= 's';
        }
        $params[] = $id;
        $types .= 'i';

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    // Xóa user
    public function delete($id)
    {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    // Đặt lại mật khẩu qua email
    public function resetPassword($email, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $hash, $email);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    // Chỉnh sửa thông tin cá nhân (tên, điện thoại, địa chỉ)
    public function updateProfile($id, $full_name, $phone, $address)
    {
        $query = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $full_name, $phone, $address, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    // Lấy thông tin user theo số điện thoại
    public function findByPhone($phone)
    {
        $query = "SELECT * FROM users WHERE phone = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }
}