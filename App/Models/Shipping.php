<?php
require_once __DIR__ . '/../../Config/db.php';
class Shipping {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Tạo trạng thái vận chuyển ban đầu
    public function createShippingStatus($order_id) {
        $status = "Chờ xác nhận";
        $stmt = $this->conn->prepare("INSERT INTO shipping_status (order_id, status, updated_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $order_id, $status);
        return $stmt->execute();
    }

    // Cập nhật trạng thái vận chuyển
    public function updateShippingStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE shipping_status SET status = ?, updated_at = NOW() WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }

     // Lấy trạng thái vận chuyển của tất cả đơn
    public function getAll() {
        $query = "SELECT s.*, o.user_id, o.shipping_address FROM shipping_status s
                  LEFT JOIN orders o ON s.order_id=o.id
                  ORDER BY s.updated_at DESC";
        $result = $this->conn->query($query);
        $shippings = [];
        while($row = $result->fetch_assoc()){
            $shippings[] = $row;
        }
        return $shippings;
    }

    // Lấy trạng thái vận chuyển theo order_id
    public function findByOrderId($order_id) {
        $query = "SELECT * FROM shipping_status WHERE order_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i",$order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $status = $result->fetch_assoc();
        $stmt->close();
        return $status;
    }

    // Cập nhật trạng thái vận chuyển
    public function updateStatus($order_id, $status) {
        // Nếu đã có record thì update, chưa có thì insert
        $existing = $this->findByOrderId($order_id);
        if($existing){
            $query = "UPDATE shipping_status SET status=? WHERE order_id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si",$status,$order_id);
            $success = $stmt->execute();
            $stmt->close();
        } else {
            $query = "INSERT INTO shipping_status(order_id, status) VALUES(?,?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("is",$order_id,$status);
            $success = $stmt->execute();
            $stmt->close();
        }
        return $success;
    }
}
?>  