<?php
require_once __DIR__ . '/../../Config/db.php';

class Report {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // --- Doanh thu theo ngày ---
    public function revenueByDay($start_date = null, $end_date = null) {
        $sql = "SELECT DATE(created_at) AS revenue_date,
                       COUNT(*) AS total_orders,
                       SUM(total_price) AS total_revenue
                FROM orders
                WHERE order_status != 'Hủy'";

        if ($start_date) $sql .= " AND DATE(created_at) >= '$start_date'";
        if ($end_date) $sql .= " AND DATE(created_at) <= '$end_date'";

        $sql .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC";

        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    // --- Doanh thu theo tháng ---
    public function revenueByMonth($start_date = null, $end_date = null) {
        $sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS revenue_month,
                       COUNT(*) AS total_orders,
                       SUM(total_price) AS total_revenue
                FROM orders
                WHERE order_status != 'Hủy'";

        if ($start_date) $sql .= " AND DATE(created_at) >= '$start_date'";
        if ($end_date) $sql .= " AND DATE(created_at) <= '$end_date'";

        $sql .= " GROUP BY revenue_month ORDER BY revenue_month ASC";

        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    // --- Doanh thu theo năm ---
    public function revenueByYear($start_date = null, $end_date = null) {
        $sql = "SELECT YEAR(created_at) AS revenue_year,
                       COUNT(*) AS total_orders,
                       SUM(total_price) AS total_revenue
                FROM orders
                WHERE order_status != 'Hủy'";

        if ($start_date) $sql .= " AND DATE(created_at) >= '$start_date'";
        if ($end_date) $sql .= " AND DATE(created_at) <= '$end_date'";

        $sql .= " GROUP BY revenue_year ORDER BY revenue_year ASC";

        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    // --- Tồn kho sản phẩm ---
    public function stockReport() {
        $sql = "SELECT id, name, stock, price FROM products ORDER BY stock ASC";
        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    // --- Sản phẩm bán ra ---
    public function soldProducts($start_date = null, $end_date = null) {
        $sql = "SELECT p.id, p.name, SUM(od.quantity) AS quantity_sold, SUM(od.quantity*od.price) AS revenue
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                JOIN orders o ON od.order_id = o.id
                WHERE o.order_status != 'Hủy'";

        if ($start_date) $sql .= " AND DATE(o.created_at) >= '$start_date'";
        if ($end_date) $sql .= " AND DATE(o.created_at) <= '$end_date'";

        $sql .= " GROUP BY p.id, p.name ORDER BY quantity_sold DESC";

        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }
}
?>
