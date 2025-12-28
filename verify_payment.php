<?php
require_once 'config.php';
require_once 'config/flutterwave.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$transaction_id = $input['transaction_id'] ?? '';
$tx_ref = $input['tx_ref'] ?? '';
$order_data = $input['order_data'] ?? [];

if (empty($transaction_id) || empty($tx_ref)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
    exit;
}

// Verify payment with Flutterwave
$url = "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . FLUTTERWAVE_SECRET_KEY
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    exit;
}

$result = json_decode($response, true);

// Check if payment was successful
if ($result['status'] === 'success' && $result['data']['status'] === 'successful' && $result['data']['tx_ref'] === $tx_ref) {
    
    // Payment verified! Now save the order
    try {
        // Generate order number
        $order_number = 'WEDEX-' . strtoupper(uniqid());
        
        // Calculate cart totals
        $cart_items = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        
        foreach ($cart_items as $product_id => $item) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                $subtotal += $product['price'] * $item['quantity'];
            }
        }
        
        $shipping = $subtotal > 200000 ? 0 : 5000;
        $tax = $subtotal * 0.075;
        $total = $subtotal + $shipping + $tax;
        
        // Save order to database
        $order_sql = "INSERT INTO orders (
            order_number, user_id, customer_email, customer_phone, customer_name,
            shipping_address, city, state, order_notes,
            subtotal, shipping_cost, tax, total,
            payment_method, payment_status, transaction_id, tx_ref,
            order_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($order_sql);
        $stmt->execute([
            $order_number,
            $_SESSION['user_id'] ?? null,
            $order_data['email'],
            $order_data['phone'],
            $order_data['first_name'] . ' ' . $order_data['last_name'],
            $order_data['address'],
            $order_data['city'],
            $order_data['state'],
            $order_data['order_notes'] ?? '',
            $subtotal,
            $shipping,
            $tax,
            $total,
            'flutterwave',
            'paid',
            $transaction_id,
            $tx_ref,
            'processing'
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Save order items
        foreach ($cart_items as $product_id => $item) {
            $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($item_sql);
                $stmt->execute([
                    $order_id,
                    $product_id,
                    $product['name'],
                    $item['quantity'],
                    $product['price']
                ]);
                
                // Update product stock
                $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $pdo->prepare($update_stock);
                $stmt->execute([$item['quantity'], $product_id]);
            }
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        echo json_encode([
            'success' => true,
            'order_number' => $order_number,
            'message' => 'Payment verified and order created successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Order creation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to create order']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
}
?>