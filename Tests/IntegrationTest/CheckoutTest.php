<?php
use PHPUnit\Framework\TestCase;
use Tests\ReportHelper;   // ⚡ import đúng namespace

require_once __DIR__ . '/../ReportHelper.php';
require_once __DIR__ . "/../../App/Models/Users.php";
require_once __DIR__ . "/../../App/Models/Cart.php";
require_once __DIR__ . "/../../App/Models/Order.php";

class CheckoutTest extends TestCase {
    private $user, $cart, $order;

    protected function setUp(): void {
        $this->user  = new User();
        $this->cart  = new Cart();
        $this->order = new Order();
    }

    public function testUserCheckout() {
        // 1. Đăng nhập
        $email = "test@example.com";
        $password = "123456";
        $user = $this->user->login($email, $password);
        $result = $user !== null;
        $this->assertNotNull($user);
        ReportHelper::addRow(
            "email=$email, pass=$password",
            json_encode($user),
            "User object not null",
            $user ? "User found" : "null",
            $result
        );

        // 2. Thêm vào giỏ
        $addCart = $this->cart->addToCart($user['id'], 2, 1, "M");
        $this->assertTrue($addCart);
        ReportHelper::addRow(
            "userId={$user['id']}, productId=2, qty=1, size=M",
            $addCart ? "true" : "false",
            "true",
            $addCart ? "true" : "false",
            $addCart
        );

        // 3. Đặt hàng
        $orderId = $this->order->createOrder($user['id'], 150000, "Hà Nội");
        $isInt = is_int($orderId);
        $this->assertIsInt($orderId);
        ReportHelper::addRow(
            "userId={$user['id']}, total=150000, address=Hà Nội",
            $orderId,
            "OrderId (int)",
            $orderId,
            $isInt
        );

        // 4. Thêm chi tiết đơn hàng
        $result = $this->order->addOrderDetail($orderId, 2, 1, 150000, "M");
        $this->assertTrue($result);
        ReportHelper::addRow(
            "orderId=$orderId, productId=2, qty=1, price=150000, size=M",
            $result ? "true" : "false",
            "true",
            $result ? "true" : "false",
            $result
        );
    }

    public static function tearDownAfterClass(): void {
        // Xuất file sau khi chạy hết test
        ReportHelper::exportExcel('OrderTestReport.xlsx');
    }
}
