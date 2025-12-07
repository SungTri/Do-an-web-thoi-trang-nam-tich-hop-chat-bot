<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . "/../../App/Models/Order.php";

class OrderTest extends TestCase {
    private $order;

    protected function setUp(): void {
        $this->order = new Order();
    }

    public function testCreateOrder() {
        $orderId = $this->order->createOrder(2, 200000, "Hà Nội");
        $this->assertIsInt($orderId);
    }
}
