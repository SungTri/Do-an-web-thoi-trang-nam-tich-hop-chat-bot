<?php
require_once __DIR__ . '/../../Config/db.php';

class Product
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Upload ảnh sản phẩm
    public function uploadImage($file)
    {
        $targetDir = "/../../Public/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($file["name"]));
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return "uploads/" . $fileName;
        }
        return false;
    }

    // Lấy tất cả sản phẩm + khuyến mãi (nếu có)
    public function getAll()
    {
        $query = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

        // Lấy sản phẩm theo ID
    public function findById($id)
    {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        return $product;
    }
    // Thêm sản phẩm mới
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO products (category_id, name, description, price, stock, image_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issdis",
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['image_url']
        );
        $stmt->execute();
        return $this->conn->insert_id; 
    }

    // Cập nhật sản phẩm
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET category_id=?, name=?, description=?, price=?, stock=?, image_url=? 
            WHERE id=?
        ");
        $stmt->bind_param(
            "issdisi",
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['image_url'],
            $id
        );
        return $stmt->execute();
    }

    // Xóa sản phẩm
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    // Lấy ảnh chi tiết
    public function getImages($product_id) {
        $sql = "SELECT image FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['image'];
        }
        return $images;
    }
    // Lấy sản phẩm theo danh mục
    public function findByCategory($category_id)
    {
        $query = "SELECT * FROM products WHERE category_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }
    
    // Tìm kiếm sản phẩm theo tên (có thể dùng LIKE)
    public function searchByName($name)
    {
        $query = "SELECT * FROM products WHERE name LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $likeName = '%' . $name . '%';
        $stmt->bind_param("s", $likeName);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }

    // Lọc sản phẩm theo danh mục và sắp xếp giá tăng dần
    public function filterByCategoryPriceAsc($category_id)
    {
        $query = "SELECT * FROM products WHERE category_id = ? ORDER BY price ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }

    // Lọc sản phẩm theo danh mục và sắp xếp giá giảm dần
    public function filterByCategoryPriceDesc($category_id)
    {
        $query = "SELECT * FROM products WHERE category_id = ? ORDER BY price DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }

    // Lấy tất cả sản phẩm sắp xếp giá tăng dần
    public function getAllByPriceAsc()
    {
        $query = "SELECT * FROM products ORDER BY price ASC";
        $result = $this->conn->query($query);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    // Lấy tất cả sản phẩm sắp xếp giá giảm dần
    public function getAllByPriceDesc()
    {
        $query = "SELECT * FROM products ORDER BY price DESC";
        $result = $this->conn->query($query);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getSecondaryImage($product_id) {
        $sql = "SELECT image FROM product_images WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['image'] : null;
    }

}
