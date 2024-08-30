<?php
// Kết nối với cơ sở dữ liệu
$host = 'localhost';
$db = 'shopping_cart';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Lớp Product để thao tác với bảng tbl_product
class Product {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM tbl_product");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Lớp Cart để quản lý giỏ hàng
class Cart {
    private $cart = [];

    public function __construct() {
        if (isset($_SESSION['cart'])) {
            $this->cart = $_SESSION['cart'];
        }
    }

    public function insertCart($product_id, $quantity) {
        if (isset($this->cart[$product_id])) {
            $this->cart[$product_id] += $quantity;
        } else {
            $this->cart[$product_id] = $quantity;
        }
        $_SESSION['cart'] = $this->cart;
    }

    public function updateCart($product_id, $quantity) {
        if (isset($this->cart[$product_id])) {
            $this->cart[$product_id] = $quantity;
            $_SESSION['cart'] = $this->cart;
        }
    }

    public function deleteCart($product_id) {
        if (isset($this->cart[$product_id])) {
            unset($this->cart[$product_id]);
            $_SESSION['cart'] = $this->cart;
        }
    }

    public function totalCart() {
        return array_sum($this->cart);
    }

    public function contentCart() {
        return $this->cart;
    }
}

// Khởi tạo phiên làm việc
session_start();

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $cart = new Cart();
    $cart->insertCart($product_id, $quantity);
}

// Xử lý hiển thị sản phẩm
$product = new Product($pdo);
$products = $product->getAllProducts();

// Hiển thị giỏ hàng
$cart = new Cart();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
</head>
<body>
    <header>
        <h1>Giỏ Hàng</h1>
        <div>
            <a href="cart.php">Giỏ hàng (<?= $cart->totalCart(); ?>)</a>
        </div>
    </header>
    
    <h2>Danh sách sản phẩm</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['pro_name']); ?></td>
                    <td><?= number_format($product['pro_price'], 0, ',', '.'); ?> VNĐ</td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['pro_id']; ?>">
                            <input type="number" name="quantity" value="1" min="1">
                            <input type="hidden" name="action" value="add_to_cart">
                            <button type="submit">Mua hàng</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
