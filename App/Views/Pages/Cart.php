<?php
require_once '../../Controller/Cart_Process.php'; 
include '../Partials/header.php';  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Người dùng chưa đăng nhập -> hiện thông báo thay vì redirect
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <title>Giỏ hàng</title>
        <link rel="stylesheet" href="../../../Public/css/style.css">
    </head>
    <body>
         <div class="main-contents">
        <div class="cart-container">
        <h2>Giỏ Hàng Của Bạn</h2>
        <p>Bạn cần <a href="../Pages/Login.php">đăng nhập</a> để xem giỏ hàng.</p>
</div>
</div>
    </body>
    </html>
    <?php
    include '../Partials/footer.php';
    exit(); // dừng ở đây
}

$user_id = $_SESSION['user_id'];
$cartModel = new Cart();
$cartItems = $cartModel->getCartByUser($user_id);
?>  
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Giỏ hàng của bạn</title>
    <link rel="stylesheet" href="../../../Public/css/style.css">
</head>
<body>
 <div class="main-contents">
    <div class="cart-container">
     <h2>Giỏ Hàng Của Bạn</h2>
     <?php if (empty($cartItems)): ?>
        <p>Giỏ hàng trống.</p>
     <?php else: ?>
        <form action="../../Controller/Cart_Process.php" method="POST">
            <table class="cart-table">
                <thead> 
                    <tr>
                        <th>Chọn</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; foreach ($cartItems as $item): 
                        $total += $item['price'] * $item['quantity'];
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>">
                        </td>
                        <td><img src="../../../Public/<?= htmlspecialchars($item['image_url']) ?>"></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>
                            <form action="../../Controller/Cart_Process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <select name="size">
                                    <option value="S" <?= $item['size']=="S" ? "selected" : "" ?>>S</option>
                                    <option value="M" <?= $item['size']=="M" ? "selected" : "" ?>>M</option>
                                    <option value="L" <?= $item['size']=="L" ? "selected" : "" ?>>L</option>
                                    <option value="XL" <?= $item['size']=="XL" ? "selected" : "" ?>>XL</option>
                                    <option value="XXL" <?= $item['size']=="XXL" ? "selected" : "" ?>>XXL</option>
                                </select>
                                <button type="submit" name="update_cart_size" class="btn-update">Cập nhật</button>
                            </form>
                        </td>
                        <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                        <td>
                            <form action="../../Controller/Cart_Process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width:60px;text-align:center;">
                                <button type="submit" name="update_cart_quantity" class="btn-update">Cập nhật</button>
                            </form>
                        </td>
                        <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                        <td class="cart-actions">
                            <form action="../../Controller/Cart_Process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="remove_cart_item" class="btn-remove">Xóa</button>
                            </form>
                            <form action="../../Controller/Cart_Process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="buy_now">Mua ngay</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Tổng tiền: <?= number_format($total, 0, ',', '.') ?>đ</h3>

            <!-- Nút Mua hàng tất cả tận dụng luôn form -->
            <button type="submit" name="buy_all" class="checkout-button">Mua hàng tất cả</button>
        </form>

        <form action="../../Controller/Cart_Process.php" method="POST">
            <button type="submit" name="clear_cart">Xóa toàn bộ giỏ hàng</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
 <script src="../../../Public/js/script.js"></script>
</body>
</html>
<?php include '../Partials/footer.php'; ?>