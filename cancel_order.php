<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // 1. Verify the order belongs to this user AND is in 'processing' state
        $stmt = $pdo->prepare("SELECT id, order_status FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            if ($order['order_status'] === 'processing') {
                // 2. Update status to cancelled
                $update_stmt = $pdo->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
                if ($update_stmt->execute([$order_id])) {
                    // Optional: You could add logic here to restore stock quantities if needed
                    $_SESSION['flash_message'] = "Order #WEDEX-{$order_id} has been cancelled successfully.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    $_SESSION['flash_message'] = "Failed to cancel order. Please try again.";
                    $_SESSION['flash_type'] = "error";
                }
            } else {
                $_SESSION['flash_message'] = "This order cannot be cancelled because it has already been processed or shipped.";
                $_SESSION['flash_type'] = "error";
            }
        } else {
            $_SESSION['flash_message'] = "Order not found.";
            $_SESSION['flash_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database error occurred.";
        $_SESSION['flash_type'] = "error";
    }
}

// Redirect back to orders tab
header('Location: account.php?tab=orders');
exit;
?>