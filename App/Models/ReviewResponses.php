<?php
require_once __DIR__ . '/../../Config/db.php';

class ReviewResponse {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Thêm phản hồi mới
    public function addResponse($review_id, $response) {
        $stmt = $this->conn->prepare(
            "INSERT INTO review_responses (review_id, response) VALUES (?, ?)"
        );
        $stmt->bind_param("is", $review_id, $response);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Lấy phản hồi theo review
    public function getResponsesByReview($review_id) {
        $stmt = $this->conn->prepare(
            "SELECT response, created_at FROM review_responses WHERE review_id = ? ORDER BY created_at ASC"
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
}
