<?php
session_start();
$config = require __DIR__ . '/../../Config/vnpay_config.php';

if (!isset($_SESSION['user_id']) || empty($_GET['order_id']) || empty($_GET['amount'])) {
    die("Thiếu thông tin đơn hàng");
}

$order_id = intval($_GET['order_id']);
$amount   = intval($_GET['amount']); 

date_default_timezone_set('Asia/Ho_Chi_Minh'); // ✅ Đặt timezone VN

// Config
$vnp_Url        = $config['vnp_Url'];
$vnp_Returnurl  = $config['vnp_Returnurl'];
$vnp_TmnCode    = $config['vnp_TmnCode'];
$vnp_HashSecret = $config['vnp_HashSecret'];

// Thông tin giao dịch
$vnp_TxnRef    = $order_id . "_" . time();
$vnp_OrderInfo = "Thanh toan don hang #" . $order_id;
$vnp_OrderType = "billpayment";
$vnp_Amount    = $amount * 100;
$vnp_Locale    = "vn";
$vnp_BankCode  = "NCB";
$vnp_IpAddr    = $_SERVER['REMOTE_ADDR'];
$vnp_CreateDate = date('YmdHis');
$vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

$inputData = [
    "vnp_Version"    => "2.1.0",
    "vnp_TmnCode"    => $vnp_TmnCode,
    "vnp_Amount"     => $vnp_Amount,
    "vnp_Command"    => "pay",
    "vnp_CreateDate" => $vnp_CreateDate,
    "vnp_ExpireDate" => $vnp_ExpireDate,
    "vnp_CurrCode"   => "VND",
    "vnp_IpAddr"     => $vnp_IpAddr,
    "vnp_Locale"     => $vnp_Locale,
    "vnp_OrderInfo"  => $vnp_OrderInfo,
    "vnp_OrderType"  => $vnp_OrderType,
    "vnp_ReturnUrl"  => $vnp_Returnurl,
    "vnp_TxnRef"     => $vnp_TxnRef,
    "vnp_BankCode"   => $vnp_BankCode
];

// Sắp xếp
ksort($inputData);

// Build query + hash
$query    = http_build_query($inputData, '', '&');   // ✅ không urldecode
$hashdata = http_build_query($inputData, '', '&');   // ✅ giống nhau

$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

// Tạo URL thanh toán
$vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

// Redirect
header('Location: ' . $vnp_Url);
exit;
