<?php
require_once __DIR__ . '/../Models/Cart.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$cartModel = new Cart();

// Thêm sản phẩm vào giỏ
if (isset($_POST['add_to_cart'])) {
    session_start();
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $size = $_POST['size'];
    $cartModel->addToCart($user_id, $product_id, $quantity, $size);
    header("Location: ../Views/Pages/Cart.php");
    exit();
}

// Xóa sản phẩm khỏi giỏ
if (isset($_POST['remove_cart_item'])) {
    $cart_id = intval($_POST['cart_id']);
    $cartModel->removeFromCart($cart_id);
    header("Location: ../Views/Pages/Cart.php");
    exit();
}

// Cập nhật số lượng sản phẩm trong giỏ
if (isset($_POST['update_cart_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    $cartModel->updateQuantity($cart_id, $quantity);
    header("Location: ../Views/Pages/Cart.php");
    exit();
}

// Xóa toàn bộ giỏ hàng (sau khi đặt hàng)
if (isset($_POST['clear_cart'])) {
    session_start();
    $user_id = $_SESSION['user_id'];
    $cartModel->clearCart($user_id);
    header("Location: ../Views/Pages/Cart.php");
    exit();
}

// Cập nhật size sản phẩm trong giỏ
if (isset($_POST['update_cart_size'])) {
    $cart_id = intval($_POST['cart_id']);
    $size = $_POST['size'];
    $cartModel->updateSize($cart_id, $size);
    header("Location: ../Views/Pages/Cart.php");
    exit();
}

// Khi nhấn "Mua ngay"
if (isset($_POST['buy_now']) && isset($_POST['cart_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_id = intval($_POST['cart_id']);
    $selected_item = $cartModel->getCartItemById($cart_id);

    if ($selected_item && $selected_item['user_id'] == $user_id) {
        // reset dữ liệu cũ
        $_SESSION['checkout_items'] = [];
        $_SESSION['checkout_total'] = 0;

        // chỉ lấy duy nhất 1 sản phẩm
        $_SESSION['checkout_items'][] = $selected_item;
        $_SESSION['checkout_total'] = $selected_item['price'] * $selected_item['quantity'];

        header("Location: ../Views/Pages/Checkout.php");
        exit();
    } else {
        echo "<script>alert('Không tìm thấy sản phẩm!'); window.location.href='../Views/Pages/Cart.php';</script>";
        exit();
    }
}
// Khi nhấn "Mua tất cả" (có thể chọn sản phẩm hoặc mua hết)
if (isset($_POST['buy_all'])) {
    $user_id = $_SESSION['user_id'];

    // Nếu có chọn sản phẩm
    if (!empty($_POST['selected_items'])) {
        $selected_ids = $_POST['selected_items'];
        $cartItems = [];
        $total = 0;

        foreach ($selected_ids as $cart_id) {
            $item = $cartModel->getCartItemById(intval($cart_id));
            if ($item && $item['user_id'] == $user_id) {
                $cartItems[] = $item;
                $total += $item['price'] * $item['quantity'];
            }
        }
    } else {
        // Không chọn thì lấy toàn bộ giỏ
        $cartItems = $cartModel->getCartByUser($user_id);
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }

    $_SESSION['checkout_items'] = $cartItems;
    $_SESSION['checkout_total'] = $total;

    header("Location: ../Views/Pages/Checkout.php");
    exit();
}

?>