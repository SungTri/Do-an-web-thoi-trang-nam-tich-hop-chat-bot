<?php
    include '../../../Config/db.php';  // Kết nối database
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Lấy 10 sản phẩm mới nhất
$sql_new_products = "SELECT id, name, image_url FROM products ORDER BY created_at DESC LIMIT 10";
$result_new_products = mysqli_query($conn, $sql_new_products);

// Lấy 5 sản phẩm bán chạy nhất
$sql_best_sellers = "SELECT p.id, p.name, p.image_url, SUM(od.quantity) AS total_sold 
                     FROM order_details od
                     JOIN products p ON od.product_id = p.id
                     GROUP BY p.id
                     ORDER BY total_sold DESC 
                     LIMIT 5";
$result_best_sellers = mysqli_query($conn, $sql_best_sellers);
    // Lấy danh sách danh mục từ bảng categories
    $sql_categories = "SELECT * FROM categories";
    $result_categories = mysqli_query($conn, $sql_categories);

    // Kiểm tra nếu người dùng đã đăng nhập
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Truy vấn lấy tổng số lượng sản phẩm trong giỏ hàng
    $sql = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $cart_count = $row['total_items'] ?? 0; // Nếu không có sản phẩm nào thì mặc định là 0

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Store</title>
    <link rel="stylesheet" href="../../../Public/css/style.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../Public/js/alerts.js"></script>
</head>
<body>
<div class="promo-bar">
    <a href="../Pages/sale_products.php" class="promo-text">MIỄN PHÍ VẬN CHUYỂN CHO ĐƠN HÀNG > 1.000.000 VND
    </a>
</div>
<header>
    <div class="container">
        <a href="../Pages/index.php">
            <img src="../../../Public/images/logo.jpg" alt="Fashion Store Logo" class="logo">
        </a>
        <nav>
            <ul>
                <li class="dropdown">
                    <a href="#" id="collection-link">THỂ LOẠI</a>
                    <div class="dropdown-menu">
                        <ul>
                            <li><a href="../Pages/Shop.php">Tất cả</a></li>
                            <?php while ($row = mysqli_fetch_assoc($result_categories)) { ?>
                                <li><a href="../Pages/Shop.php?category_id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></li>
                            <?php } ?>
                        </ul>   
                    </div>
                </li>
                <li><a href="#" id="story-link">SẢN PHẨM</a></li>
                <li><a href="#" id="about-link">CHÚNG TÔI</a></li>
            </ul> 
        </nav>
        <div class="header-right">
            <form action="../Pages/Search.php" method="get" class="search-form">
                <input type="text" name="name" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit">
                    <img src="../../../Public/images/ico-search2x.svg" alt="">
                </button>
            </form>
            <a href="../Pages/cart.php" class="cart-icon">
                         <img src="../../../Public/images/Cart.png" alt=""><?php if ($cart_count > 0) { ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                 <?php } ?>
            </a>
            <div class="account-icon">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <div class="dropdown-content">
                            <a href="../Pages/Account.php"><img src="../../../Public/images/Account.png" alt="Tài khoản"></a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../Pages/login.php"><img src="../../../Public/images/Account.png" alt="Tài khoản"></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<!-- ABOUT SECTION ẨN MẶC ĐỊNH -->
<div id="about-section" class="about-content">
    <div class="about-text">
        <!-- Nội dung About -->
        <p><strong>Ambastyle creates a New Aesthetic</strong> by accessing technical fabrics, durable materials, experimental knitting techniques, and chemical dyeing treatments. From our perspective, traditional streetwear is now renewed by deconstruction through raw cuts, hand distressing, combining avant-garde garments and asymmetrical details.</p>
        <p>Design language of brand represents a POV on global youth subcultures. Sometimes an opinion, a statement, even an emotion, a blurry memory, sometimes an imagination of parallel dimension.</p>
        <p>Our seasonal collections push the boundaries in creativity, also balance the timeless aesthetics and honoring futuristic values. Goldie expresses the chaos and contradictions of society standards, provoking more questions than answers.</p>
        <p><strong>We are an unexpected concept proudly created by Vietnamese people.</strong></p>
        <hr>
        <p><strong>Ambastyle  hướng đến việc sáng tạo tính Thẩm Mĩ Mới</strong> bằng việc tiếp cận những chất liệu tiên phong với độ bền cao, các kỹ thuật dệt thử nghiệm, phương pháp xử lí nhuộm hoá chất. Trang phục đường phố quen thuộc dưới góc nhìn của thương hiệu được làm mới bằng việc giải cấu trúc thông qua những đường cắt thô, tạo hình rách thủ công, kết hợp nhiều chất liệu và chi tiết bất đối xứng.</p>
        <p>Ngôn ngữ thiết kế của Ambastyle  thể hiện góc nhìn cá nhân về những văn hoá đặc trưng của người trẻ. Đôi khi là quan điểm, là tuyên ngôn, 1 trạng thái cảm xúc, ký ức mơ hồ, hoặc thậm chí là sự tưởng tượng về 1 hình thái song song.</p>
        <p>Các bộ sưu tập theo mùa ngoài việc phá vỡ những giới hạn về sáng tạo, song vẫn luôn cân bằng giữa tính thẩm mĩ truyền thống và tôn vinh những giá trị đương đại. Cách tiếp cận thời trang của Goldie nói lên sự hỗn loạn và mâu thuẫn về những tiêu chuẩn trong xã hội, khơi gợi nhiều câu hỏi hơn là những câu trả lời.</p>
        <p><strong>Ambastyle là 1 hình thái vô định được tự hào tạo nên từ những người Việt.</strong></p>
        <hr>
        <h3>📍 STORE LOCATOR:</h3>
        <ul>
            <li><strong>Hanoi:</strong>
                <ul>
                    <li>360 Pho Hue</li>
                    <li>15 Ho Dac Di</li>
                </ul>
            </li>
            <li><strong>Saigon:</strong> @thenewplayground 26 Ly Tu Trong, District 1</li>
            <li><strong>Japan:</strong> <a href="https://www.sixty-percent.com/en/collections/goldie" target="_blank">www.sixty-percent.com/en/collections/goldie</a></li>
        </ul>
        <h3>📞 Contact:</h3>
        <p><strong>Hotline:</strong> 0985 032 589</p>
        <p><strong>Email:</strong> <a href="mailto:info@goldievietnam.com">info@goldievietnam.com</a></p>
        <li><a href="#"><img src="../../../Public/images/anhicon1.png" alt="Facebook"></a></li>
        <li><a href="#"><img src="../../../Public/images/anhicon2.png" alt="Instagram"></a></li>
    </div>
</div>
<!-- STORY SECTION ẨN MẶC ĐỊNH -->
<div id="story-section" class="story-content">
    <h2>✨ What's New? ✨</h2>

    <h3>🆕 10 Sản Phẩm Mới Nhất</h3>
    <div class="new-products">
        <?php while ($row = mysqli_fetch_assoc($result_new_products)) { ?>
            <a href="../Pages/productDetail.php?id=<?= $row['id'] ?>" class="new-product-item">
                <img src="../../../Public/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" title="<?= $row['name'] ?>">
            </a>
        <?php } ?>
    </div>

    <h3>🔥 5 Sản Phẩm Bán Chạy Nhất</h3>
    <div class="best-sellers">
        <?php while ($row = mysqli_fetch_assoc($result_best_sellers)) { ?>
            <a href="../Pages/productDetail.php?id=<?= $row['id'] ?>" class="best-seller-item">
                <img src="../../../Public/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" title="<?= $row['name'] ?>">
            </a>
        <?php } ?>
    </div>
</div>
<script src="../../../Public/js/script.js"></script>
<script src="../../../Public/js/script.js"></script>
<script src="../../../Public/js/script.js"></script>

<!-- ============================================ -->
<!-- CHATBOT WIDGET - AMBASTYLE -->
<!-- ============================================ -->
<style>
    #chatbot-toggle {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: none;
        background: #111;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        z-index: 9999;
        transition: transform 0.2s;
    }
    #chatbot-toggle:hover {
        transform: scale(1.1);
    }
    #chatbot-window {
        position: fixed;
        bottom: 90px;
        right: 24px;
        width: 350px;
        max-height: 500px;
        background: #111;
        color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.6);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 9999;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    #chatbot-window.show {
        display: flex;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #chatbot-header {
        padding: 12px 16px;
        background: #000;
        border-bottom: 1px solid #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #chatbot-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    #chatbot-header .status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #0f0;
    }
    #chatbot-header .status::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #0f0;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    #chatbot-messages {
        padding: 16px;
        flex: 1;
        overflow-y: auto;
        background: #151515;
        font-size: 13px;
        line-height: 1.5;
    }
    #chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 3px;
    }
    .chat-msg {
        margin-bottom: 12px;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .chat-msg.user {
        text-align: right;
    }
    .chat-msg.user span {
        display: inline-block;
        background: #0f0;
        color: #000;
        padding: 8px 12px;
        border-radius: 12px 12px 2px 12px;
        max-width: 80%;
        word-wrap: break-word;
        font-weight: 500;
    }
    .chat-msg.bot span {
        display: inline-block;
        background: #222;
        color: #fff;
        padding: 8px 12px;
        border-radius: 12px 12px 12px 2px;
        max-width: 85%;
        word-wrap: break-word;
        white-space: pre-line;
    }
    .chat-msg.bot a {
        color: rgba(0, 0, 0, 1);
        text-decoration: underline;
    }
    .chat-product {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 10px;
        margin: 8px 0;
    }
    .chat-product img {
        width: 100%;
        height: 120px;
        object-fit: contain;
        border-radius: 6px;
        background: #0a0a0a;
        margin-bottom: 8px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .chat-product img:hover {
        opacity: 0.8;
    }
    .chat-product h4 {
        margin: 0 0 6px 0;
        font-size: 13px;
        color: #fff;
    }
    .chat-product .price {
        color: #0f0;
        font-weight: 700;
        font-size: 14px;
    }
    .chat-product .stock {
        font-size: 11px;
        color: #888;
        margin-top: 4px;
    }
    .chat-product .view-detail-btn {
        display: inline-block;
        margin-top: 8px;
        padding: 6px 12px;
        background: rgba(255, 255, 255, 1);
        color: #000;
        text-decoration: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: background 0.2s;
    }
    .chat-product .view-detail-btn:hover {
        background: rgba(121, 121, 121, 1);
    }
    .chat-msg.bot.loading span {
        background: #1a1a1a;
        color: #888;
        font-style: italic;
    }
    .chat-msg.bot.error span {
        background: #3a0000;
        color: #ff6b6b;
        border: 1px solid #ff0000;
    }
    #chatbot-input-area {
        display: flex;
        border-top: 1px solid #333;
        background: #0a0a0a;
    }
    #chatbot-input {
        flex: 1;
        border: none;
        padding: 12px 16px;
        font-size: 13px;
        background: transparent;
        color: #fff;
        outline: none;
    }
    #chatbot-input::placeholder {
        color: #666;
    }
    #chatbot-send {
        width: 70px;
        border: none;
        background: #0f0;
        color: #000;
        font-weight: 700;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    #chatbot-send:hover {
        background: #0c0;
    }
    #chatbot-send:disabled {
        background: #333;
        color: #666;
        cursor: not-allowed;
    }
</style>

<div id="chatbot-window">
    <div id="chatbot-header">
        <h4>AMBASTYLE BOT</h4>
        <span class="status">online</span>
    </div>
    <div id="chatbot-messages"></div>
    <div id="chatbot-input-area">
        <input type="text" id="chatbot-input" placeholder="Nhập tin nhắn..." />
        <button id="chatbot-send">Gửi</button>
    </div>
</div>

<button id="chatbot-toggle">💬</button>

<script>
(function() {
    'use strict';
    
    const API_URL = 'http://localhost/WebThoiTrangNam/Config/chatbot.php';
    
    const chatWindow = document.getElementById('chatbot-window');
    const chatToggle = document.getElementById('chatbot-toggle');
    const chatMessages = document.getElementById('chatbot-messages');
    const chatInput = document.getElementById('chatbot-input');
    const chatSend = document.getElementById('chatbot-send');

    let isOpen = false;

    // Toggle chatbot
    chatToggle.addEventListener('click', () => {
        isOpen = !isOpen;
        if (isOpen) {
            chatWindow.classList.add('show');
            chatInput.focus();
            
            if (chatMessages.children.length === 0) {
                addBotMessage('Xin chào! Tôi là trợ lý ảo của AMBASTYLE. Tôi có thể giúp gì cho bạn? 😊\n\nBạn có thể hỏi về sản phẩm, giá cả, hoặc đặt hàng!');
            }
        } else {
            chatWindow.classList.remove('show');
        }
    });

    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        addUserMessage(message);
        chatInput.value = '';

        const loadingId = addBotMessage('Đang xử lý...', true);

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            removeMessage(loadingId);
            handleBotResponse(data);
        })
        .catch(error => {
            removeMessage(loadingId);
            addBotMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại sau.', false, true);
            console.error('Chatbot error:', error);
        });
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'chat-msg user';
        div.innerHTML = `<span>${escapeHtml(text)}</span>`;
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function addBotMessage(text, isLoading = false, isError = false) {
        const div = document.createElement('div');
        const id = 'msg-' + Date.now();
        div.id = id;
        div.className = 'chat-msg bot' + (isLoading ? ' loading' : '') + (isError ? ' error' : '');
        div.innerHTML = `<span>${text}</span>`;
        chatMessages.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeMessage(id) {
        const msg = document.getElementById(id);
        if (msg) msg.remove();
    }

    function handleBotResponse(data) {
        if (data.type === 'products') {
            addBotMessage(data.text);
            data.products.forEach(product => addProductCard(product));
        } else if (data.type === 'product') {
            addBotMessage(data.text);
            addProductCard(data.product);
        } else if (data.type === 'html') {
            const div = document.createElement('div');
            div.className = 'chat-msg bot';
            div.innerHTML = `<span>${data.text}</span>`;
            chatMessages.appendChild(div);
            scrollToBottom();
        } else {
            addBotMessage(data.text || data.response);
        }
    }

    function addProductCard(product) {
        let imagePath = product.image_url;
        if (imagePath && !imagePath.startsWith('http')) {
            imagePath = `http://localhost/WebThoiTrangNam/Public/${imagePath}`;
        }

        // Tạo link đến trang chi tiết sản phẩm
        const productDetailUrl = `http://localhost/WebThoiTrangNam/App/Views/Pages/ProductDetail.php?id=${product.id}`;

        const div = document.createElement('div');
        div.className = 'chat-msg bot';
        div.innerHTML = `
            <div class="chat-product">
                <a href="${productDetailUrl}" target="_blank">
                    <img src="${imagePath}" alt="${escapeHtml(product.name)}" onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                </a>
                <h4>${escapeHtml(product.name)}</h4>
                <div class="price">${formatPrice(product.price)} VNĐ</div>
                <div class="stock">Kho: ${product.stock} sản phẩm</div>
                ${product.category_name ? `<div style="font-size: 11px; color: #888; margin-top: 4px;">${escapeHtml(product.category_name)}</div>` : ''}
                <a href="${productDetailUrl}" target="_blank" class="view-detail-btn">
                    Xem chi tiết →
                </a>
            </div>
        `;
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }
})();
</script>

<!-- END CHATBOT WIDGET -->

</body>
</html>