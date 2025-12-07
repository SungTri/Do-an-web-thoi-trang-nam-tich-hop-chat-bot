<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . "/../../App/Models/Users.php";

class UserTest extends TestCase {
    private $userModel;

    protected function setUp(): void {
        $this->userModel = new User();
    }

    public function testCreateUser() {
        $result = $this->userModel->create("test@example.com", "123456", "Test User", "123456789", "Hà Nội");
        $this->assertTrue($result);
    }

    public function testLoginSuccess() {
        $user = $this->userModel->login("test@example.com", "123456");
        $this->assertNotNull($user);
    }

    public function testLoginFail() {
        $user = $this->userModel->login("wrong@example.com", "wrongpass");
        $this->assertNull($user);
    }
}
