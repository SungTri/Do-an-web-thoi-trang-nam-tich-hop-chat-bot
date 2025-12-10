<?php
$config = require __DIR__ . "/momo.php";

$amount = $_GET['amount'];
$orderId = time() . rand(100, 999);
$requestId = time() . rand(1000, 9999);

// ⬅️ Với payWithMethod trên môi trường TEST
$data = [
    "partnerCode" => $config['partnerCode'],
    "accessKey"   => $config['accessKey'],
    "requestId"   => $requestId,
    "amount"      => (string)$amount,
    "orderId"     => $orderId,
    "orderInfo"   => "Thanh toan don hang #" . $orderId,
    "redirectUrl" => $config['redirectUrl'],
    "ipnUrl"      => $config['ipnUrl'],
    "extraData"   => "",
    "requestType" => "payWithMethod",
    "lang"        => "vi"
];

// Tạo chữ ký
$rawHash = "accessKey=" . $config['accessKey'] .
           "&amount=" . $data['amount'] .
           "&extraData=" . $data['extraData'] .
           "&ipnUrl=" . $data['ipnUrl'] .
           "&orderId=" . $data['orderId'] .
           "&orderInfo=" . $data['orderInfo'] .
           "&partnerCode=" . $config['partnerCode'] .
           "&redirectUrl=" . $data['redirectUrl'] .
           "&requestId=" . $data['requestId'] .
           "&requestType=" . $data['requestType'];

$signature = hash_hmac("sha256", $rawHash, $config['secretKey']);
$data['signature'] = $signature;

$ch = curl_init($config['endpoint']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Debug
if (!isset($result['payUrl'])) {
    echo "Lỗi tạo thanh toán:<br><pre>";
    print_r($result);
    exit;
}

// ⬅️ Redirect đến trang thanh toán MoMo
header("Location: " . $result['payUrl']);
exit;