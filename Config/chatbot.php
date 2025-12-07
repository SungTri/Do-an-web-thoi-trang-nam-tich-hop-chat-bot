<?php
// Config/chatbot.php - API xử lý tin nhắn chatbot

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Kết nối database
include 'db.php';

// Nhận tin nhắn từ người dùng
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['response' => 'Vui lòng nhập tin nhắn!']);
    exit;
}

// Lưu lịch sử chat
session_start();
$sessionId = session_id();
$stmt = mysqli_prepare($conn, "INSERT INTO chatbot_conversations (session_id, message, message_type) VALUES (?, ?, 'user')");
mysqli_stmt_bind_param($stmt, "ss", $sessionId, $userMessage);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Xử lý tin nhắn
$response = processMessage($userMessage, $conn);

// Lưu phản hồi bot
$responseText = is_array($response) ? ($response['text'] ?? '') : $response;
$stmt = mysqli_prepare($conn, "INSERT INTO chatbot_conversations (session_id, bot_response, message_type) VALUES (?, ?, 'bot')");
mysqli_stmt_bind_param($stmt, "ss", $sessionId, $responseText);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode($response);

// Hàm xử lý tin nhắn chính
function processMessage($message, $conn) {
    $message = mb_strtolower($message, 'UTF-8');
    
    // 1. Chào hỏi
    if (preg_match('/(xin chào|chào|hello|hi|hey)/i', $message)) {
        return [
            'type' => 'text',
            'text' => 'Xin chào! Tôi là trợ lý ảo của AMBASTYLE. Tôi có thể giúp gì cho bạn? 😊

Bạn có thể hỏi:
• "list" - Xem danh sách sản phẩm
• "áo nam" - Xem áo
• "quần nam" - Xem quần
• "giày dép" - Xem giày
• "đặt hàng" - Đặt mua sản phẩm
• "xem shop" - Vào cửa hàng'
        ];
    }
    
    // 2. Xem danh sách sản phẩm / list
    if (preg_match('/(list|danh sách|sản phẩm|món|đồ|xem sản phẩm|có gì)/i', $message)) {
        return getProductList($conn);
    }
    
    // 3. Hỏi về giá
    if (preg_match('/(giá|bao nhiêu|giá cả|giá tiền)/i', $message)) {
        return [
            'type' => 'text',
            'text' => 'Bạn muốn xem giá sản phẩm nào? Vui lòng cho tôi biết tên sản phẩm hoặc gõ "list" để xem tất cả sản phẩm.'
        ];
    }
    
    // 4. Đặt hàng
    if (preg_match('/(đặt hàng|mua|order|thanh toán)/i', $message)) {
        return [
            'type' => 'html',
            'text' => 'Để đặt hàng, vui lòng truy cập: <a href="http://localhost/WebThoiTrangNam/App/Views/Pages/Shop.php" target="_blank" style="color: #0f0; font-weight: bold;">Trang đặt hàng</a>'
        ];
    }
    
    // 5. Xem shop/sản phẩm (yêu cầu đăng nhập)
    if (preg_match('/(xem shop|shop|cửa hàng|vào shop)/i', $message)) {
        return [
            'type' => 'html',
            'text' => 'Vui lòng <a href="http://localhost/WebThoiTrangNam/App/Views/Pages/login.php" target="_blank" style="color: #0f0; font-weight: bold;">đăng nhập</a> để xem sản phẩm và mua sắm!'
        ];
    }
    
    // 6. Lọc theo danh mục
    if (preg_match('/(áo|quần|giày|dép|phụ kiện)/i', $message)) {
        return getProductsByCategory($message, $conn);
    }
    
    // 7. Tìm sản phẩm cụ thể
    $productInfo = findProduct($message, $conn);
    if ($productInfo) {
        return $productInfo;
    }
    
    // 8. Cảm ơn
    if (preg_match('/(cảm ơn|thank|thanks|cám ơn)/i', $message)) {
        return [
            'type' => 'text',
            'text' => 'Rất vui được hỗ trợ bạn! Chúc bạn mua sắm vui vẻ! 😊'
        ];
    }
    
    // 9. Tạm biệt
    if (preg_match('/(tạm biệt|bye|goodbye)/i', $message)) {
        return [
            'type' => 'text',
            'text' => 'Hẹn gặp lại bạn! Chúc bạn một ngày tốt lành! 👋'
        ];
    }
    
    // Không hiểu
    return [
        'type' => 'text',
        'text' => 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn. 

Bạn có thể hỏi:
• "list" - xem danh sách sản phẩm
• "áo nam" - xem áo
• "quần nam" - xem quần
• "giày dép" - xem giày
• "đặt hàng" - đặt mua sản phẩm
• "xem shop" - vào cửa hàng'
    ];
}

// Hàm lấy danh sách tất cả sản phẩm
function getProductList($conn) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.stock > 0
            ORDER BY p.id DESC 
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        return [
            'type' => 'text',
            'text' => 'Hiện tại chưa có sản phẩm nào.'
        ];
    }
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return [
        'type' => 'products',
        'text' => 'Đây là danh sách sản phẩm của chúng tôi (Top 10):',
        'products' => $products
    ];
}

// Hàm lọc sản phẩm theo danh mục
function getProductsByCategory($message, $conn) {
    $categoryMap = [
        'áo' => 1,
        'quần' => 2,
        'giày' => 3,
        'dép' => 3,
        'phụ kiện' => 4
    ];
    
    $categoryId = null;
    $categoryName = '';
    
    foreach ($categoryMap as $key => $id) {
        if (stripos($message, $key) !== false) {
            $categoryId = $id;
            $categoryName = $key;
            break;
        }
    }
    
    if (!$categoryId) {
        return getProductList($conn);
    }
    
    $stmt = mysqli_prepare($conn, "
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.category_id = ? AND p.stock > 0
        ORDER BY p.id DESC 
        LIMIT 10
    ");
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return [
            'type' => 'text',
            'text' => "Hiện tại không có sản phẩm $categoryName nào."
        ];
    }
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    return [
        'type' => 'products',
        'text' => "Danh sách sản phẩm $categoryName:",
        'products' => $products
    ];
}

// Hàm tìm kiếm sản phẩm cụ thể
function findProduct($message, $conn) {
    $searchTerm = "%$message%";
    $stmt = mysqli_prepare($conn, "
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE LOWER(p.name) LIKE ? AND p.stock > 0
        LIMIT 5
    ");
    mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return null;
    }
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
    
    if (count($products) == 1) {
        return [
            'type' => 'product',
            'text' => 'Thông tin sản phẩm:',
            'product' => $products[0]
        ];
    } else {
        return [
            'type' => 'products',
            'text' => 'Tôi tìm thấy những sản phẩm này:',
            'products' => $products
        ];
    }
}
?>