<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . "/../../App/Models/Cart.php";

class CartTest extends TestCase {
    private $cart;

    protected function setUp(): void {
        $this->cart = new Cart();
    }

    public function testAddToCart() {
        $result = $this->cart->addToCart(2, 2, 1, "M");
        $this->assertTrue($result);
    }
}
