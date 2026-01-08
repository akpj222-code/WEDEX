<?php
require_once 'config.php';

// Get order number from URL
$order_number = $_GET['order'] ?? null;

if (!$order_number) {
    header('Location: index.php');
    exit;
}

try {
    // Fetch Order Details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->execute([$order_number]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        // Order not found, redirect to home
        header('Location: index.php');
        exit;
    }

    // Fetch Order Items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error retrieving order details.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="container mx-auto px-4 py-16">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            
            <div class="bg-teal-600 p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Thank You!</h1>
                <p class="text-teal-100 text-lg">Your order has been placed successfully.</p>
                <p class="text-teal-200 mt-2 font-mono">Order #<?php echo htmlspecialchars($order_number); ?></p>
            </div>

            <div class="p-8">
                <div class="border-b pb-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Order Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="font-medium text-gray-900">Shipping Address:</p>
                            <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?></p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Contact Info:</p>
                            <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
                            <p><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                            <p class="mt-2"><span class="font-medium text-gray-900">Payment Status:</span> 
                                <span class="text-green-600 font-semibold uppercase"><?php echo htmlspecialchars($order['payment_status']); ?></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="font-semibold text-gray-800 mb-4">Items Ordered</h3>
                    <div class="space-y-4">
                        <?php foreach ($items as $item): ?>
                        <div class="flex justify-between items-center border-b pb-2 last:border-0">
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <p class="font-medium text-gray-800">
                                <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                                <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-8">
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Subtotal</span>
                        <span><?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Shipping</span>
                        <span><?php echo number_format($order['shipping_cost'], 2); ?></span>
                    </div>
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Tax</span>
                        <span><?php echo number_format($order['tax'], 2); ?></span>
                    </div>
                    <div class="border-t pt-2 mt-2 flex justify-between font-bold text-lg text-gray-800">
                        <span>Total</span>
                        <span>
                            <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                            <?php echo number_format($order['total'], 2); ?>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="shop.php" class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-900 transition text-center">
                        Continue Shopping
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="account.php?tab=orders" class="bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-teal-700 transition text-center">
                        View My Orders
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>