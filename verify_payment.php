<?php
require_once 'config.php';
require_once 'config/paystack.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$reference = $input['reference'] ?? '';
$order_data = $input['order_data'] ?? [];

if (empty($reference)) {
    echo json_encode(['success' => false, 'message' => 'Invalid reference']);
    exit;
}

// Verify with Paystack
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . PAYSTACK_SECRET_KEY]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Check success
if (isset($result['status']) && $result['status'] === true && $result['data']['status'] === 'success') {
    
    // Recalculate Totals in NGN (Backend Verification)
    $subtotal = 0;
    $cart_items = $_SESSION['cart'] ?? [];
    
    try {
        foreach ($cart_items as $product_id => $item) {
            $stmt = $pdo->prepare("SELECT price, name FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                // Use DB price directly (NGN)
                $subtotal += $product['price'] * $item['quantity'];
                
                // Store name/price for item insertion
                $cart_items[$product_id]['db_price'] = $product['price'];
                $cart_items[$product_id]['name'] = $product['name'];
            }
        }
        
        $shipping = $subtotal > 500000 ? 0 : 5000;
        $tax = $subtotal * 0.075;
        $total = $subtotal + $shipping + $tax;
        
        // Generate Order
        $order_number = 'WEDEX-' . strtoupper(uniqid());
        
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
            'paystack', 
            'paid',
            $result['data']['id'],
            $reference,
            'processing'
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Save items
        foreach ($cart_items as $product_id => $item) {
             $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
             $stmt = $pdo->prepare($item_sql);
             $stmt->execute([
                 $order_id, $product_id, $item['name'], $item['quantity'], $item['db_price']
             ]);
             
             // Update Stock
             $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
             $stmt->execute([$item['quantity'], $product_id]);
        }
        
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'order_number' => $order_number]);
        
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed at gateway']);
}
?>