<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class SystemTest extends TestCase {
    private $driver;
    private $baseUrl = "http://localhost/WebThoiTrangNam/App/Views/Pages";
    private static $testEmail;
    private static $testPassword = "123456";

    protected function setUp(): void {
        $host = 'http://localhost:9515';
        $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create($host, $capabilities);
        $this->driver->manage()->window()->maximize();
    }

    private function login($email, $password) {
        $this->driver->get("{$this->baseUrl}/Login.php");
        $this->driver->findElement(WebDriverBy::name("email"))->sendKeys($email);
        $this->driver->findElement(WebDriverBy::name("password"))->sendKeys($password);
        $this->driver->findElement(WebDriverBy::name("login"))->click();

        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(WebDriverExpectedCondition::urlContains("index.php"));
    }

    public function testRegisterAndLogin() {
        $uniqueEmail = "selenium" . uniqid() . "@test.com";
        self::$testEmail = $uniqueEmail;

        $this->driver->get("{$this->baseUrl}/Register.php");
        $this->driver->findElement(WebDriverBy::name("full_name"))->sendKeys("Selenium User");
        $this->driver->findElement(WebDriverBy::name("email"))->sendKeys($uniqueEmail);
        $this->driver->findElement(WebDriverBy::name("password"))->sendKeys(self::$testPassword);
        $this->driver->findElement(WebDriverBy::name("phone"))->sendKeys("0987654321");
        $this->driver->findElement(WebDriverBy::name("address"))->sendKeys("Hà Nội");
        $this->driver->findElement(WebDriverBy::name("register"))->click();

        $this->login($uniqueEmail, self::$testPassword);

        $this->assertStringContainsString("index.php", $this->driver->getCurrentURL());
    }

    public function testAddToCartAndCheckout() {
        // login bằng account vừa tạo ở testRegisterAndLogin
        $this->login(self::$testEmail, self::$testPassword);

        $this->driver->get("{$this->baseUrl}/ProductDetail.php?id=6");
        $this->driver->findElement(WebDriverBy::name("product_id"))->sendKeys("6");
        $this->driver->findElement(WebDriverBy::name("size"))->sendKeys("M");
        $this->driver->findElement(WebDriverBy::name("quantity"))->sendKeys("1");
        $this->driver->findElement(WebDriverBy::name("add_to_cart"))->click();

        // mở trang giỏ hàng
        $this->driver->get("{$this->baseUrl}/Cart.php");

        $wait = new WebDriverWait($this->driver, 10);

        // chờ cho nút "Mua ngay" thật sự clickable
        $buyNowBtn = $wait->until(
            WebDriverExpectedCondition::elementToBeClickable(
                WebDriverBy::cssSelector("button[name='buy_now']")
            )
        );

        // nếu nút ở dưới viewport thì cuộn xuống
        $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$buyNowBtn]);
        $buyNowBtn->click();

        $this->assertStringContainsString("Confirm_payment.php", $this->driver->getCurrentURL());
    }

    protected function tearDown(): void {
        $this->driver->quit();
    }
}
