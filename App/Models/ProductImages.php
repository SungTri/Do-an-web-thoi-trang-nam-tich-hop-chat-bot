<?php
require_once __DIR__ . '/../../Config/db.php';

class ProductImages
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }
     // Lấy tất cả ảnh phụ của sản phẩm
    public function getByProductId($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    // Thêm ảnh phụ cho sản phẩm
    public function addImage($product_id, $image)
    {
        $query = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $product_id, $image);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function deleteImage($image_id)
    {
        // Xóa file ảnh
        $query = "SELECT image FROM product_images WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row && file_exists("../" . $row['image'])) {
            unlink("../" . $row['image']);
        }
        $stmt->close();

        // Xóa khỏi database
        $query = "DELETE FROM product_images WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function deleteImagesByProductId($product_id)
    {
        // Xóa file ảnh chi tiết
        $query = "SELECT image FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (file_exists("../" . $row['image'])) {
                unlink("../" . $row['image']);
            }
        }
        $stmt->close();

        // Xóa khỏi database
        $query = "DELETE FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function getImagesByProductId($product_id)
    {
        $query = "SELECT image FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image'];
        }
        $stmt->close();
        return $images;
    }
}