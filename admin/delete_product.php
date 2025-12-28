<?php
require_once '../config.php';

// Check for admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard');
    exit;
}

try {
    // Optional: Delete the image file from the server
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $product['image'] !== 'default.jpg') {
        $images = explode(',', $product['image']);
        foreach ($images as $image) {
            if (file_exists('../uploads/' . $image) && $image !== 'default.jpg') {
                unlink('../uploads/' . $image);
            }
        }
    }

    // Delete the product from the database
    $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $deleteStmt->execute([$id]);
    
    $_SESSION['success_message'] = 'Product deleted successfully!';
} catch (PDOException $e) {
    // You might want to log this error instead of showing it to the user
    $_SESSION['error_message'] = 'Error deleting product: ' . $e->getMessage();
}

header('Location: dashboard');
exit;
?>