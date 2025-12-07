<?php
require_once __DIR__ . '/../../Config/db.php';

class Category
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // ===================== LẤY TẤT CẢ DANH MỤC =====================
    public function getAll()
    {
        $query = "SELECT * FROM categories ORDER BY created_at DESC";
        $result = $this->conn->query($query);

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    // ===================== LẤY DANH MỤC THEO ID =====================
    public function findById($id)
    {
        $query = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $category = $result->fetch_assoc();

        $stmt->close();
        return $category;
    }

    // ===================== TẠO DANH MỤC =====================
    public function create($name)
    {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $name);
        $success = $stmt->execute();

        $stmt->close();
        return $success;
    }

    // ===================== CẬP NHẬT DANH MỤC =====================
    public function update($id, $name)
    {
        $query = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("si", $name, $id);
        $success = $stmt->execute();

        $stmt->close();
        return $success;
    }

    // ===================== XÓA DANH MỤC =====================
    public function delete($id)
    {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        $stmt->close();
        return $success;
    }

    // ===================== 🔍 TÌM KIẾM DANH MỤC (LIKE %keyword%) =====================
    public function searchByName($keyword)
    {
        $keyword = "%" . $keyword . "%"; // Tìm kiếm gần đúng, chỉ cần 1 chữ cũng ra

        $query = "SELECT * FROM categories WHERE name LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $keyword);
        $stmt->execute();

        $result = $stmt->get_result();
        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        $stmt->close();
        return $categories;
    }
}
