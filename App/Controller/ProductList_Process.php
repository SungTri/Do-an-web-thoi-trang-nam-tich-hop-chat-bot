<?php
require_once __DIR__ . '/../Models/Products.php';
require_once __DIR__ . '/../Models/ProductImages.php';

$productModel = new Product();
$imageModel   = new ProductImages();

$action = $_POST['action'] ?? $_GET['action'] ?? '';


// ================= CREATE =================
if ($action === 'create') {
    $mainImage = null;
    $targetDir = "../../Public/uploads/";

    // Upload ảnh chính
    if (!empty($_FILES['image_url']['name'])) {
        $fileName  = time() . "_" . basename($_FILES["image_url"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile);
        $mainImage = "uploads/" . $fileName;
    }

    // Thêm sản phẩm
    $product_id = $productModel->create([
        'category_id'   => $_POST['category_id'],
        'promotion_id'  => $_POST['promotion_id'] ?? null,
        'name'          => $_POST['name'],
        'description'   => $_POST['description'],
        'price'         => $_POST['price'],
        'stock'         => $_POST['stock'],
        'image_url'     => $mainImage
    ]);

    // Upload ảnh phụ
    if (!empty($_FILES['sub_images']['name'][0])) {
        foreach ($_FILES['sub_images']['tmp_name'] as $key => $tmp_name) {
            $fileName = time() . "_" . basename($_FILES["sub_images"]["name"][$key]);
            $targetFile = $targetDir . $fileName;
            move_uploaded_file($tmp_name, $targetFile);
            $imageModel->addImage($product_id, "uploads/" . $fileName);
        }
    }

    header("Location: ../Views/Admin/Admin_Products.php");
    exit;
}


// ================= UPDATE =================
if ($action === 'update') {
    $id = intval($_POST['id']);
    $mainImage = $_POST['old_image'] ?? null;
    $targetDir = "../../Public/uploads/";

    if (!empty($_FILES['image_url']['name'])) {
        $fileName  = time() . "_" . basename($_FILES["image_url"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile);
        $mainImage = "uploads/" . $fileName;
    }

    $productModel->update($id, [
        'category_id'   => $_POST['category_id'],
        'promotion_id'  => $_POST['promotion_id'] ?? null,
        'name'          => $_POST['name'],
        'description'   => $_POST['description'],
        'price'         => $_POST['price'],
        'stock'         => $_POST['stock'],
        'image_url'     => $mainImage
    ]);

    // Ảnh phụ mới
    if (!empty($_FILES['sub_images']['name'][0])) {
        $imageModel->deleteImagesByProductId($id);
        foreach ($_FILES['sub_images']['tmp_name'] as $key => $tmp_name) {
            $fileName = time() . "_" . basename($_FILES["sub_images"]["name"][$key]);
            $targetFile = $targetDir . $fileName;
            move_uploaded_file($tmp_name, $targetFile);
            $imageModel->addImage($id, "uploads/" . $fileName);
        }
    }

    header("Location: ../Views/Admin/Admin_Products.php");
    exit;
}


// ================= DELETE =================
if ($action === 'delete') {
    $id = intval($_GET['id']);
    $imageModel->deleteImagesByProductId($id);
    $productModel->delete($id);
    header("Location: ../Views/Admin/Admin_Products.php");
    exit;
}


// ================= GET DATA =================
if ($action === 'get_by_id' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $product = $productModel->findById($id);
}

if ($action === 'get_by_category' && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $products = $productModel->findByCategory($category_id);
}

if ($action === 'search' && !empty($_GET['name'])) {
    $name = trim($_GET['name']);
    $products = $productModel->searchByName($name);
}

if ($action === 'category' && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $products = $productModel->findByCategory($category_id);
}

if ($action === 'category_price_asc' && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $products = $productModel->filterByCategoryPriceAsc($category_id);
}

if ($action === 'category_price_desc' && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $products = $productModel->filterByCategoryPriceDesc($category_id);
}

if ($action === 'price_asc') {
    $products = $productModel->getAllByPriceAsc();
}

if ($action === 'price_desc') {
    $products = $productModel->getAllByPriceDesc();
}


// ⭐⭐⭐ ================= TÌM KIẾM NEW ================= ⭐⭐⭐
if (isset($_GET['keyword']) && $_GET['keyword'] !== '') {
    $keyword = trim($_GET['keyword']);
    $products = $productModel->searchByName($keyword); // ✔ SỬA search() thành searchByName()
    return;
}


// ================= DEFAULT LOAD =================
if ($action === '') {
    $products = $productModel->getAll();
}

?>
