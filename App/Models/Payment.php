<?php
require_once __DIR__ . '/../../Config/db.php';
class Payment {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Tạo bản ghi thanh toán
    public function createPayment($order_id, $user_id, $amount, $method, $status) {
    if ($status === 'Đã thanh toán') {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, user_id, amount, payment_method, payment_status, paid_at, created_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iidss", $order_id, $user_id, $amount, $method, $status);
    } else {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, user_id, amount, payment_method, payment_status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iidss", $order_id, $user_id, $amount, $method, $status);
    }
    return $stmt->execute();
}

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($payment_id, $status) {
        $stmt = $this->conn->prepare("UPDATE payments SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $payment_id);
        return $stmt->execute();
    }
    // Lấy tất cả thanh toán
    public function getAll() {
        $query = "SELECT p.*, u.full_name, o.total_price FROM payments p
                  LEFT JOIN users u ON p.user_id=u.id
                  LEFT JOIN orders o ON p.order_id=o.id
                  ORDER BY p.created_at DESC";
        $result = $this->conn->query($query);
        $payments = [];
        while($row = $result->fetch_assoc()){
            $payments[] = $row;
        }
        return $payments;
    }

    // Lấy thanh toán theo ID
    public function findById($id){
        $query = "SELECT * FROM payments WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();
        return $payment;
    }

    // Cập nhật trạng thái thanh toán
    public function updateStatus($id, $status){
        $query = "UPDATE payments SET payment_status=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si",$status,$id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>