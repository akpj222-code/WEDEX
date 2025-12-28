<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to add items to your wishlist.'
    ]);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

try {
    // Check if product exists
    $product_check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $product_check->execute([$product_id]);
    if (!$product_check->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found.'
        ]);
        exit;
    }

    // Check if item is already in wishlist
    $check_stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->execute([$user_id, $product_id]);
    $existing = $check_stmt->fetch();

    if ($existing) {
        // Remove from wishlist
        $delete_stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete_stmt->execute([$user_id, $product_id]);
        
        echo json_encode([
            'success' => true,
            'action' => 'removed',
            'message' => 'Product removed from wishlist.'
        ]);
    } else {
        // Add to wishlist
        $insert_stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
        $insert_stmt->execute([$user_id, $product_id]);
        
        echo json_encode([
            'success' => true,
            'action' => 'added',
            'message' => 'Product added to wishlist!'
        ]);
    }
} catch (PDOException $e) {
    error_log("Wishlist error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again.'
    ]);
}
?>