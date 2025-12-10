<?php
require_once __DIR__ . '/../../Config/db.php';

class Order
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // 0. Áp dụng mã khuyến mãi
    public function applyPromotion($code)
    {
        $today = date("Y-m-d");
        $query = "SELECT * FROM promotions 
                  WHERE code = ? AND start_date <= ? AND end_date >= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $code, $today, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $promo = $result->fetch_assoc();
        $stmt->close();
        return $promo; // null nếu không hợp lệ
    }

     // 1. Tạo đơn hàng
    public function createOrder($user_id, $total_price, $shipping_address) {
        $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_price, shipping_address, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ids", $user_id, $total_price, $shipping_address);

        if ($stmt->execute()) {
            return $this->conn->insert_id; // trả về ID đơn hàng
        }
        return false;
    }

    // 2. Thêm chi tiết đơn hàng
    public function addOrderDetail($order_id, $product_id, $quantity, $price, $size) {
        $stmt = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $order_id, $product_id, $quantity, $price, $size);
        return $stmt->execute();
    }

    // 3. Cập nhật tồn kho sản phẩm
    public function updateStock($product_id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $product_id);
        return $stmt->execute();
    }

    // 4. Kiểm tra tồn kho trước khi đặt hàng
    public function checkStock($product_id, $quantity) {
        $query = "SELECT stock FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return ($result && $result['stock'] >= $quantity);
    }

    // Lấy thông tin đơn hàng theo id + user_id
    public function getOrderById($order_id, $user_id) {
        $query = "SELECT o.id, o.total_price, o.created_at AS order_date, p.payment_status 
                  FROM orders o 
                  LEFT JOIN payments p ON o.id = p.order_id
                  WHERE o.id = ? AND o.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        return $order;
    }

    // Lấy thông tin trạng thái vận chuyển
    public function getShippingStatus($order_id) {
        $query = "SELECT status, updated_at FROM shipping_status WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $shipping = $result->fetch_assoc();
        $stmt->close();

        return $shipping ?: null;
    }
    // Lấy tất cả đơn hàng của 1 user
    public function getOrdersByUser($user_id) {
        $sql = "SELECT o.id, o.total_price, o.created_at AS order_date,
                    o.order_status,
                    IFNULL(p.payment_status, 'Chưa thanh toán') AS payment_status,
                    IFNULL(s.status, 'Chưa có thông tin') AS shipping_status
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                LEFT JOIN shipping_status s ON o.id = s.order_id
                WHERE o.user_id = ? 
                AND o.order_status != 'Hủy'
                ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy danh sách sản phẩm trong đơn hàng
    public function getOrderItems($order_id) {
        $query = "SELECT 
                    od.product_id,
                    p.name, 
                    p.image_url, 
                    od.quantity, 
                    od.price,
                    od.size
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }
    public function cancelOrder($order_id, $user_id)
    {
        $query = "UPDATE orders SET order_status = 'Hủy' WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $order_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    public function getOrderItemsByOrderId($order_id) {
        $sql = "SELECT oi.product_id, oi.quantity, oi.size,
                    p.name, p.image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
        // Lấy tất cả đơn hàng
    public function getAll() {
        $query = "SELECT o.*, u.full_name, u.email FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        $result = $this->conn->query($query);
        $orders = [];
        while($row = $result->fetch_assoc()){
            $orders[] = $row;
        }
        return $orders;
    }
    
    // Lấy đơn hàng theo ID
    public function findById($id) {
        $query = "SELECT o.*, u.full_name, u.email FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();
        return $order;
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($id, $status) {
        $query = "UPDATE orders SET order_status=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Xóa đơn hàng
    public function delete($id){
        $query = "DELETE FROM orders WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i",$id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Lấy chi tiết đơn hàng
    public function getDetails($order_id){
        $query = "SELECT od.*, p.name FROM order_details od 
                  LEFT JOIN products p ON od.product_id = p.id
                  WHERE od.order_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i",$order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $details = [];
        while($row = $result->fetch_assoc()){
            $details[] = $row;
        }
        $stmt->close();
        return $details;
    }
    public function getDetailsByOrderId($order_id)
    {
        $query = "SELECT od.*, p.name AS product_name 
                FROM order_details od
                LEFT JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $details = [];
        while($row = $result->fetch_assoc()){
            $details[] = $row;
        }
        $stmt->close();
        return $details;
    }   
    // Trong class Order
    public function searchOrders($keyword) {
    $keyword = "%$keyword%";
    $sql = "SELECT o.*, u.full_name, u.email 
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?
            ORDER BY o.created_at DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    
    return $orders;
    }

}   

?>
 