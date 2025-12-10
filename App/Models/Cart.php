<?php
require_once __DIR__ . '/../../Config/db.php';

class Cart
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Thêm sản phẩm vào giỏ
    public function addToCart($user_id, $product_id, $quantity, $size)
    {
        $query = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $size);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Lấy giỏ hàng của user
    public function getCartByUser($user_id)
    {
        $query = "SELECT c.*, p.name, p.price, p.image_url FROM cart c 
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart = [];
        while ($row = $result->fetch_assoc()) {
            $cart[] = $row;
        }
        $stmt->close();
        return $cart;
    }

    // Xóa sản phẩm khỏi giỏ
    public function removeFromCart($cart_id)
    {
        $query = "DELETE FROM cart WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cart_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function updateQuantity($cart_id, $quantity)
    {
        $query = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $cart_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Xóa toàn bộ giỏ hàng của user (sau khi đặt hàng)
    public function clearCart($user_id)
    {
        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Cập nhật size sản phẩm trong giỏ
    public function updateSize($cart_id, $size)
    {
        $query = "UPDATE cart SET size = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $size, $cart_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    // Lấy thông tin một mục trong giỏ hàng theo cart_id
    public function getCartItemById($cart_id) {
    $query = "SELECT c.*, p.name, p.price, p.image_url 
              FROM cart c 
              JOIN products p ON c.product_id = p.id
              WHERE c.id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    return $item;
    }
    public function removeFromCartItem($user_id, $product_id, $size)
    {
    $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND size = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iis", $user_id, $product_id, $size);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
    }
}
?>