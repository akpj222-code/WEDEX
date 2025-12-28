<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        // Unset the specific product from the cart session
        unset($_SESSION['cart'][$product_id]);

        echo json_encode([
            'success' => true,
            'message' => 'Product removed from cart.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID or item not in cart.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>

