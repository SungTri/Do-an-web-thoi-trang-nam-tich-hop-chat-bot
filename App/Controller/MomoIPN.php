<?php
require_once __DIR__ . '/../../Config/db.php';
require_once __DIR__ . '/../Models/Payment.php';

$payment = new Payment($conn);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo "NO DATA";
    exit;
}

$orderId = $data["orderId"];
$resultCode = $data["resultCode"];

if ($resultCode == 0) {
    $payment->updateStatus($orderId, "Đã thanh toán");
}

echo "OK";
