<?php
require_once __DIR__ . '/../../Controller/Report_Process.php';
include '../Partials/header.php';

// Khởi tạo mặc định
$revenue_day   = $revenue_day ?? [];
$revenue_month = $revenue_month ?? [];
$revenue_year  = $revenue_year ?? [];
$stock         = $stock ?? [];
$sold_products = $sold_products ?? [];

// Lọc dữ liệu từ GET
$search      = $_GET['search'] ?? '';
$report_type = $_GET['report_type'] ?? 'revenue_day';

if (!function_exists('filterSearch')) {
    function filterSearch($item, $key, $search) {
        return empty($search) || (isset($item[$key]) && strpos(strtolower($item[$key]), strtolower($search)) !== false);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo thống kê</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alert.js"></script>
</head>
<body>
<div class="main-contents">
    <h2>BÁO CÁO THỐNG KÊ</h2>
    <div class="form-container">
        <form method="GET">
            <label>Chọn khoảng thời gian:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
            <input type="date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
            <input type="text" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search) ?>">

            <label>Loại báo cáo:</label>
            <select name="report_type">
                <option value="revenue_day"   <?= $report_type==='revenue_day'?'selected':'' ?>>Doanh thu ngày</option>
                <option value="revenue_month" <?= $report_type==='revenue_month'?'selected':'' ?>>Doanh thu tháng</option>
                <option value="revenue_year"  <?= $report_type==='revenue_year'?'selected':'' ?>>Doanh thu năm</option>
                <option value="stock"         <?= $report_type==='stock'?'selected':'' ?>>Tồn kho</option>
                <option value="sold"          <?= $report_type==='sold'?'selected':'' ?>>Sản phẩm bán ra</option>
            </select>

            <button type="submit" class="btn btn-add">Lọc</button>
        </form>

        <div style="margin-top:10px;">
            <a href="../../Controller/Report_Process.php?export=excel&type=<?= $report_type ?><?= isset($_GET['start_date']) ? '&start_date='.$_GET['start_date'] : '' ?><?= isset($_GET['end_date']) ? '&end_date='.$_GET['end_date'] : '' ?>&search=<?= urlencode($search) ?>" 
               class="btn btn-print">Xuất Excel</a>
        </div>
    </div>

    <?php
    // Hàm render bảng
    function renderTable($type, $data, $search) {
        switch ($type) {
            case 'revenue_day':
                echo "<h3>Doanh thu theo ngày</h3>
                <table><thead><tr><th>Ngày</th><th>Số đơn</th><th>Doanh thu</th></tr></thead><tbody>";
                $filtered = array_filter($data, fn($r) => filterSearch($r,'revenue_date',$search));
                if ($filtered) foreach($filtered as $r){
                    echo "<tr><td>{$r['revenue_date']}</td><td>{$r['total_orders']}</td><td>".number_format($r['total_revenue'],0,',','.')." đ</td></tr>";
                } else echo "<tr><td colspan='3'>Chưa có dữ liệu</td></tr>";
                echo "</tbody></table>";
                break;

            case 'revenue_month':
                echo "<h3>Doanh thu theo tháng</h3>
                <table><thead><tr><th>Tháng</th><th>Số đơn</th><th>Doanh thu</th></tr></thead><tbody>";
                $filtered = array_filter($data, fn($r) => filterSearch($r,'revenue_month',$search));
                if ($filtered) foreach($filtered as $r){
                    echo "<tr><td>{$r['revenue_month']}</td><td>{$r['total_orders']}</td><td>".number_format($r['total_revenue'],0,',','.')." đ</td></tr>";
                } else echo "<tr><td colspan='3'>Chưa có dữ liệu</td></tr>";
                echo "</tbody></table>";
                break;

            case 'revenue_year':
                echo "<h3>Doanh thu theo năm</h3>
                <table><thead><tr><th>Năm</th><th>Số đơn</th><th>Doanh thu</th></tr></thead><tbody>";
                $filtered = array_filter($data, fn($r) => filterSearch($r,'revenue_year',$search));
                if ($filtered) foreach($filtered as $r){
                    echo "<tr><td>{$r['revenue_year']}</td><td>{$r['total_orders']}</td><td>".number_format($r['total_revenue'],0,',','.')." đ</td></tr>";
                } else echo "<tr><td colspan='3'>Chưa có dữ liệu</td></tr>";
                echo "</tbody></table>";
                break;

            case 'stock':
                echo "<h3>Tồn kho sản phẩm</h3>
                <table><thead><tr><th>ID</th><th>Tên SP</th><th>Số lượng tồn</th><th>Giá</th></tr></thead><tbody>";
                $filtered = array_filter($data, fn($s) => filterSearch($s,'name',$search));
                if ($filtered) foreach($filtered as $s){
                    echo "<tr><td>{$s['id']}</td><td>".htmlspecialchars($s['name'])."</td><td>{$s['stock']}</td><td>".number_format($s['price'],0,',','.')." đ</td></tr>";
                } else echo "<tr><td colspan='4'>Chưa có dữ liệu</td></tr>";
                echo "</tbody></table>";
                break;

            case 'sold':
                echo "<h3>Sản phẩm bán ra</h3>
                <table><thead><tr><th>ID</th><th>Tên SP</th><th>Số lượng bán</th><th>Doanh thu</th></tr></thead><tbody>";
                $filtered = array_filter($data, fn($p) => filterSearch($p,'name',$search));
                if ($filtered) foreach($filtered as $p){
                    echo "<tr><td>{$p['id']}</td><td>".htmlspecialchars($p['name'])."</td><td>{$p['quantity_sold']}</td><td>".number_format($p['revenue'],0,',','.')." đ</td></tr>";
                } else echo "<tr><td colspan='4'>Chưa có dữ liệu</td></tr>";
                echo "</tbody></table>";
                break;
        }
    }

    // Map dữ liệu
    $dataMap = [
        'revenue_day'   => $revenue_day,
        'revenue_month' => $revenue_month,
        'revenue_year'  => $revenue_year,
        'stock'         => $stock,
        'sold'          => $sold_products
    ];

    renderTable($report_type, $dataMap[$report_type] ?? [], $search);
    ?>
</div>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
