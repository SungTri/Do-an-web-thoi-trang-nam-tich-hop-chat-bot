<?php
// Trang quay lại sau khi thanh toán hoặc hủy
$previousPage = "http://localhost/WebThoiTrangNam/App/Views/Pages/Confirm_payment.php";

echo "<h2>Kết quả thanh toán MoMo</h2>";
echo "<pre>";

// Lấy dữ liệu trả về từ GET hoặc POST
$data = !empty($_GET) ? $_GET : $_POST;

// Nếu rỗng hoàn toàn → trả về lỗi
if (empty($data)) {
    echo "<h3 style='color:red'>❌ Không nhận được dữ liệu từ MoMo!</h3>";
    echo "<a href='$previousPage'>Quay về trang thanh toán</a>";
    exit;
}

print_r($data);

// Kiểm tra resultCode hợp lệ
if (!isset($data['resultCode'])) {
    echo "<h3 style='color:red'>❌ Phản hồi không hợp lệ từ MoMo!</h3>";
    echo "<a href='$previousPage'>Quay về trang thanh toán</a>";
    exit;
}

$resultCode = $data['resultCode'];

/*
 * resultCode:
 * 0      → Thành công
 * 1006   → Người dùng hủy giao dịch
 * khác   → Thất bại
 */

// ==== 1. THANH TOÁN THÀNH CÔNG ====
if ($resultCode == "0") {

    echo "<h3 style='color:green'>✔ Thanh toán thành công!</h3>";

    $orderId = $data['orderId'] ?? "Không có";
    $transId = $data['transId'] ?? "Không có";
    $amount  = isset($data['amount']) ? number_format($data['amount']) : "Không rõ";

    echo "Mã đơn hàng: $orderId<br>";
    echo "Mã giao dịch MoMo: $transId<br>";
    echo "Số tiền: $amount đ<br>";

    // TODO: cập nhật database → đổi trạng thái đơn hàng sang PAID

    echo "<br><a href='$previousPage'
            style='padding:10px 20px;background:#28a745;color:#fff;border-radius:5px;text-decoration:none'>
            Quay về trang mua hàng
          </a>";
}

// ==== 2. NGƯỜI DÙNG HỦY GIAO DỊCH ====
else if ($resultCode == "1006") {

    echo "<h3 style='color:orange'>⚠ Bạn đã hủy giao dịch!</h3>";

    echo "<br><a href='$previousPage'
            style='padding:10px 20px;background:#ff9800;color:#fff;border-radius:5px;text-decoration:none'>
            Quay về trang trước đó
          </a>";

    echo "<script>
            setTimeout(() => { window.location.href = '$previousPage'; }, 2000);
          </script>";
}

// ==== 3. GIAO DỊCH THẤT BẠI ====
else {

    $msg = $data['message'] ?? "Không rõ nguyên nhân";

    echo "<h3 style='color:red'>❌ Thanh toán thất bại!</h3>";
    echo "Mã lỗi: $resultCode<br>";
    echo "Thông báo: $msg<br>";

    echo "<br><a href='$previousPage'
            style='padding:10px 20px;background:#d9534f;color:#fff;border-radius:5px;text-decoration:none'>
            Quay về trang trước đó
          </a>";

    echo "<script>
            setTimeout(() => { window.location.href = '$previousPage'; }, 2500);
          </script>";
}

echo "</pre>";
