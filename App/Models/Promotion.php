<?php
require_once __DIR__ . '/../../Config/db.php';

class Promotion {
    private $conn;

    public function __construct() {
        global $conn; // dùng biến $conn trong db.php
        $this->conn = $conn;
    }

        // Lấy tất cả promotion
    public function getAll() {
        $sql = "SELECT * FROM promotions ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC); // ✅ MySQLi dùng fetch_all
        }
        return []; // nếu không có dữ liệu thì trả về mảng rỗng
    }

    // Lấy promotion theo ID
    public function getById($id) {
        $sql = "SELECT * FROM promotions WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // ✅ MySQLi dùng fetch_assoc
    }

    // Thêm promotion
    public function create($data) {
        $sql = "INSERT INTO promotions (code, discount_percentage, start_date, end_date) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siss", 
            $data['code'], 
            $data['discount_percentage'], 
            $data['start_date'], 
            $data['end_date']
        );
        return $stmt->execute();
    }

    // Cập nhật promotion
    public function update($data) {
        $sql = "UPDATE promotions 
                SET code = ?, discount_percentage = ?, start_date = ?, end_date = ? 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissi", 
            $data['code'], 
            $data['discount_percentage'], 
            $data['start_date'], 
            $data['end_date'], 
            $data['id']
        );
        return $stmt->execute();
    }

    // Xóa promotion
    public function delete($id) {
        $sql = "DELETE FROM promotions WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}
