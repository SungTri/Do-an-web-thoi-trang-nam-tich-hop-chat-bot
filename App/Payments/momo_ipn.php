<?php
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if ($data['resultCode'] == 0) {
    // TODO: Update đơn hàng → Đã thanh toán
}

echo json_encode(['message' => 'IPN received']);
