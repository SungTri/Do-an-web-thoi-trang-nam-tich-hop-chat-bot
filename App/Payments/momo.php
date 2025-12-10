<?php
return [
    "partnerCode" => "MOMO",
    "accessKey"   => "F8BBA842ECF85",
    "secretKey"   => "K951B6PE1waDMi640xX08PD3vg6EkVlz",

    // ⬅️ QUAN TRỌNG: Dùng endpoint này cho payWithMethod
    "endpoint" => "https://test-payment.momo.vn/v2/gateway/api/create",

    "redirectUrl" => "http://localhost/WebThoiTrangNam/App/Payments/momo_return.php",
    "ipnUrl"      => "http://localhost/WebThoiTrangNam/App/Payments/momo_ipn.php",
];