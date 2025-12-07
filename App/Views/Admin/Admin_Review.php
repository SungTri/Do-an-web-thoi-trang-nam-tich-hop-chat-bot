<?php
session_start();
require_once __DIR__ . '/../../Models/Review.php';
require_once __DIR__ . '/../../Models/ReviewResponses.php';
require_once __DIR__ . '/../../../Config/db.php';
include '../Partials/header.php';

// Chỉ admin mới được truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href='../Pages/Login.php';</script>";
    exit;
}

$reviewModel = new Review();
$responseModel = new ReviewResponse();

// Lấy tất cả đánh giá
$reviews = $reviewModel->getAllReviews();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đánh giá & phản hồi</title>
    <link rel="stylesheet" href="../../../Public/css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alerts.js"></script>
    <?php if(isset($_GET['message']) && isset($_GET['status'])): ?>
    <script>
        const message = "<?= htmlspecialchars($_GET['message']) ?>";
        const status = "<?= htmlspecialchars($_GET['status']) ?>";
        if(status === 'success') showSuccess(message);
        else if(status === 'error') showError(message);
    </script>
    <?php endif; ?>
</head>
<body>
<div class="main-contents">
    <h2>QUẢN LÝ ĐÁNH GIÁ & PHẢN HỒI</h2>
 <div class="form-container">
     <h3>Danh sách đánh giá & phản hồi</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Người dùng</th>
                <th>Đánh giá</th>
                <th>Nội dung</th>
                <th>Ngày</th>
                <th>Phản hồi Admin</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <tr>
                    <td><?= $r['review_id'] ?></td>
                    <td><?= htmlspecialchars($r['product_name']) ?> (#<?= $r['product_id'] ?>)</td>
                    <td><?= htmlspecialchars($r['full_name']) ?></td>
                    <td><?= $r['rating'] ?>/5</td>
                    <td><?= htmlspecialchars($r['review']) ?></td>
                    <td><?= $r['created_at'] ?></td>
                    <td>
                        <?php 
                        $responses = $responseModel->getResponsesByReview($r['review_id']);
                        if (!empty($responses)) {
                            foreach ($responses as $res) {
                                echo "<p><b>Admin:</b> ".htmlspecialchars($res['response'])." <br><small>".$res['created_at']."</small></p>";
                            }
                        } else {
                            echo "<i>Chưa có phản hồi</i>";
                        }
                        ?>
                        <!-- Form thêm phản hồi -->
                        <form method="POST" action="../../Controller/Response_Process.php">
                            <input type="hidden" name="review_id" value="<?= $r['review_id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $r['product_id'] ?>">
                            <textarea name="response" required placeholder="Nhập phản hồi..."></textarea><br>
                            <button type="submit">Gửi</button>
                        </form>
                    </td>
                    <td>
                        <a href="../../Controller/DeleteReview_Process.php?action=delete&review_id=<?= $r['review_id'] ?>&product_id=<?= $r['product_id'] ?>"
                           onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?')">Xóa</a>
                        <br><br>
                        <button onclick="confirmDeleteAll(<?= $r['product_id'] ?>)">Xóa tất cả của SP</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8">Chưa có đánh giá nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
  </div>
</div>

<script>
function confirmDeleteAll(productId) {
    showConfirm("Bạn có chắc chắn muốn xóa TẤT CẢ đánh giá & phản hồi cho sản phẩm này không?")
    .then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../../Controller/DeleteReview_Process.php?action=deleteAll&product_id=" + productId;
        }
    });
}
</script>
</body>
</html>
<?php include '../Partials/footer.php'; ?>
