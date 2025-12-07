<?php
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class AddToCartTest extends TestCase {
    protected $driver;

    protected function setUp(): void {
        // kết nối tới chromedriver
        $host = 'http://localhost:9515'; // cổng bạn chạy chromedriver
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    protected function tearDown(): void {
        if ($this->driver) {
            $this->driver->quit();
        }
    }

    private function loginBeforeTest() {
        $this->driver->get("http://localhost/WebThoiTrangNam/App/Views/Pages/Login.php");
        $this->driver->findElement(WebDriverBy::name("email"))->sendKeys("Sunggaygay2910@gmail.com
");
        $this->driver->findElement(WebDriverBy::name("password"))->sendKeys("Sung2910");
        $this->driver->findElement(WebDriverBy::name("login"))->click();

        // chờ login thành công (url chuyển hướng về index.php)
        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(
            WebDriverExpectedCondition::urlContains("index.php")
        );
    }

    public function testAddToCartAndCheckout() {
        $this->loginBeforeTest();

        $this->driver->get("http://localhost/WebThoiTrangNam/App/Views/Pages/ProductDetail.php?id=6");

        $wait = new WebDriverWait($this->driver, 20);

        // chọn size
        $this->driver->findElement(WebDriverBy::id("size"))->sendKeys("M");

        // chờ nút add_to_cart có thể click
        $addToCartButton = $wait->until(
            WebDriverExpectedCondition::elementToBeClickable(
                WebDriverBy::cssSelector("form button[name='add_to_cart']")
            )
        );
        $addToCartButton->click();

        // mở Cart
        $this->driver->get("http://localhost/WebThoiTrangNam/App/Views/Pages/Cart.php");

        // chờ giỏ hàng có ít nhất 1 dòng
        $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector(".cart-table tbody tr")
            )
        );

        // click Mua ngay
        $buyNowButton = $wait->until(
            WebDriverExpectedCondition::elementToBeClickable(
                WebDriverBy::name("buy_now")
            )
        );
        $buyNowButton->click();

        $this->assertStringContainsString("Checkout.php", $this->driver->getCurrentURL());
    }
}
