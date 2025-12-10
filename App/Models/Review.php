<?php
require_once __DIR__ . '/../../Config/db.php';
class Review {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Lấy danh sách đánh giá của một sản phẩm
    public function getReviewsByProduct($product_id) {
        $stmt = $this->conn->prepare(
            "SELECT u.full_name, r.id as review_id, r.rating, r.review, r.created_at
             FROM product_reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.product_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        $stmt->close();
        return $reviews;
    }

    // Kiểm tra người dùng đã mua sản phẩm chưa
    public function hasPurchased($user_id, $product_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) 
             FROM order_details od
             JOIN orders o ON od.order_id = o.id
             WHERE o.user_id = ? AND od.product_id = ?"
        );
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    // Thêm đánh giá
    public function addReview($user_id, $product_id, $rating, $review) {
        $stmt = $this->conn->prepare(
            "INSERT INTO product_reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Lấy phản hồi của admin
    public function getResponses($review_id) {
        $stmt = $this->conn->prepare(
            "SELECT response, created_at FROM review_responses WHERE review_id = ?"
        );
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $responses = [];
        while ($row = $result->fetch_assoc()) {
            $responses[] = $row;
        }
        $stmt->close();
        return $responses;
    }
    // Xóa 1 đánh giá theo review_id
    public function deleteReview($review_id) {
        // Xóa phản hồi trước
        $stmt = $this->conn->prepare("DELETE FROM review_responses WHERE review_id = ?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        $stmt->close();
        // Xóa đánh giá
        $stmt2 = $this->conn->prepare("DELETE FROM product_reviews WHERE id = ?");
        $stmt2->bind_param("i", $review_id);
        $result = $stmt2->execute();
        $stmt2->close();

        return $result;
    }
    // Xóa tất cả đánh giá và phản hồi theo product_id
    public function deleteAllReviewsByProduct($product_id) {
        // Xóa phản hồi trước
        $stmt = $this->conn->prepare(
            "DELETE FROM review_responses 
            WHERE review_id IN (SELECT id FROM product_reviews WHERE product_id = ?)"
        );
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();

        // Xóa đánh giá
        $stmt2 = $this->conn->prepare("DELETE FROM product_reviews WHERE product_id = ?");
        $stmt2->bind_param("i", $product_id);
        $result = $stmt2->execute();
        $stmt2->close();

        return $result;
    }
    // Lấy tất cả đánh giá (mọi sản phẩm)
    public function getAllReviews() {
        $stmt = $this->conn->prepare(
            "SELECT r.id as review_id, r.product_id, r.rating, r.review, r.created_at,
                    u.full_name, p.name as product_name
            FROM product_reviews r
            JOIN users u ON r.user_id = u.id
            JOIN products p ON r.product_id = p.id
            ORDER BY r.created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        $stmt->close();
        return $reviews;
    }

}
