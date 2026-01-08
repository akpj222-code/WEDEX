<?php
require_once '../config.php';
require_once 'partials/header.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    echo "<script>window.location.href='orders';</script>";
    exit;
}

$error_msg = '';
$success_msg = '';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['order_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $order_id])) {
            $success_msg = "Order status updated to " . ucfirst($new_status);
        } else {
            $error_msg = "Failed to update status in database.";
        }
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Fetch Order Details
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) die("Order not found");

    // Fetch Order Items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Order <?php echo htmlspecialchars($order['order_number']); ?></h1>
        <a href="orders" class="text-teal-600 hover:underline">← Back to Orders</a>
    </div>

    <?php if ($success_msg): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
        <?php echo $success_msg; ?>
    </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
        <?php echo $error_msg; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white shadow rounded-lg p-6 border-t-4 border-teal-600">
                <h3 class="text-lg font-bold mb-4">Manage Order Status</h3>
                <form method="POST" class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="w-full sm:w-auto flex-grow">
                        <select name="order_status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 p-2 border">
                            <option value="processing" <?php if($order['order_status']=='processing') echo 'selected'; ?>>Processing</option>
                            <option value="shipped" <?php if($order['order_status']=='shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if($order['order_status']=='delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="cancelled" <?php if($order['order_status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="w-full sm:w-auto bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold shadow-sm transition">
                        Update Status
                    </button>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold text-gray-700">Items Ordered</div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="px-6 py-4">
                                <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                                <?php echo number_format($item['price'], 2); ?>
                            </td>
                            <td class="px-6 py-4"><?php echo $item['quantity']; ?></td>
                            <td class="px-6 py-4 font-medium">
                                <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                                <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-semibold">Subtotal:</td>
                            <td class="px-6 py-3"><?php echo number_format($order['subtotal'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-semibold">Shipping:</td>
                            <td class="px-6 py-3"><?php echo number_format($order['shipping_cost'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-semibold">Tax:</td>
                            <td class="px-6 py-3"><?php echo number_format($order['tax'], 2); ?></td>
                        </tr>
                        <tr class="bg-gray-100 text-lg">
                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                            <td class="px-6 py-4 font-bold text-teal-700">
                                <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                                <?php echo number_format($order['total'], 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Customer Details</h3>
                <p class="mb-2"><span class="font-medium">Name:</span> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p class="mb-2"><span class="font-medium">Email:</span> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p class="mb-2"><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Shipping Address</h3>
                <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?></p>
            </div>

            <?php if(!empty($order['order_notes'])): ?>
            <div class="bg-yellow-50 border border-yellow-200 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2 text-yellow-800">Order Notes</h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($order['order_notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>