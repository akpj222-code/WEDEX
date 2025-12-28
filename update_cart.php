<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $change = isset($_POST['change']) ? (int)$_POST['change'] : 0;

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $change;

        // If quantity drops to 0 or less, remove the item
        if ($new_quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed']);
            exit;
        }

        // Check stock before increasing quantity
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $new_quantity) {
            $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
            echo json_encode(['success' => true, 'message' => 'Quantity updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product or request.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

