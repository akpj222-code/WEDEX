<?php
require_once 'config.php';
require_once 'config/paystack.php';

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
$user_email = '';
$user_name = '';
$user_first_name = '';
$user_last_name = '';

if ($user_id) {
    $stmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user_email = $user['email'];
        $user_first_name = $user['first_name'];
        $user_last_name = $user['last_name'];
        $user_name = $user_first_name . ' ' . $user_last_name;
    }
}

$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    $products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products_in_cart as $product) {
        $product_id = $product['id'];
        $quantity = $_SESSION['cart'][$product_id]['quantity'];
        
        // FOR TESTING: We use the database price directly as NGN
        // Your DB has prices like 160000.00, which works perfectly for Naira
        $price = $product['price'];
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $price,
            'image' => $product['image'],
            'quantity' => $quantity,
        ];
        $subtotal += $price * $quantity;
    }
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// SHIPPING LOGIC (NGN)
// Example: Free shipping if over ₦500,000, else ₦5,000
$shipping = $subtotal > 500000 ? 0 : 5000;
$tax_rate = 0.075;
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;

$tx_ref = 'WEDEX-' . time() . '-' . mt_rand(100000, 999999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://js.paystack.co/v1/inline.js"></script>
    
    <script src="js/notifications.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Checkout (Test Mode: NGN)</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <form id="checkout-form" class="space-y-6">
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Contact Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="customer_email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" id="customer_phone" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Shipping Address</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_firstname" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                                <input type="text" id="customer_firstname" name="first_name" value="<?php echo htmlspecialchars($user_first_name); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="customer_lastname" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                                <input type="text" id="customer_lastname" name="last_name" value="<?php echo htmlspecialchars($user_last_name); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                                <input type="text" id="address" name="address" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                                <input type="text" id="city" name="city" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State / Province *</label>
                                <input type="text" id="state" name="state" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Postal / ZIP Code *</label>
                                <input type="text" id="postal_code" name="postal_code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                                <select id="country" name="country" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                                    <option value="NG">Nigeria</option>
                                    <option value="US">United States</option>
                                    <option value="GB">United Kingdom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary (NGN)</h2>
                    
                    <div class="space-y-4 mb-6 pb-6 border-b max-h-64 overflow-y-auto">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-3">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <p class="font-semibold text-gray-800 text-sm">₦<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold">₦<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="flex justify-between text-gray-600"><span>Shipping</span><span class="font-semibold">₦<?php echo number_format($shipping, 2); ?></span></div>
                        <div class="flex justify-between text-gray-600"><span>Tax (7.5%)</span><span class="font-semibold">₦<?php echo number_format($tax, 2); ?></span></div>
                        <div class="border-t pt-3"><div class="flex justify-between text-lg font-bold text-gray-800"><span>Total</span><span>₦<?php echo number_format($total, 2); ?></span></div></div>
                    </div>

                    <button type="button" id="pay-button" class="w-full bg-teal-600 text-white py-4 rounded-lg font-semibold hover:bg-teal-700 transition mb-4">
                        Pay ₦<?php echo number_format($total, 2); ?>
                    </button>
                    
                    <div class="text-center text-sm text-gray-600">
                        <span>Secured Payment by Paystack</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
    document.getElementById('pay-button').addEventListener('click', function() {
        const form = document.getElementById('checkout-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const customerEmail = document.getElementById('customer_email').value;
        const customerName = document.getElementById('customer_firstname').value + ' ' + document.getElementById('customer_lastname').value;
        const customerPhone = document.getElementById('customer_phone').value;

        // Paystack Pop - TEST MODE NGN
        const handler = PaystackPop.setup({
            key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
            email: customerEmail,
            amount: <?php echo ceil($total * 100); ?>, // Amount in Kobo
            currency: 'NGN', // Using Naira for test
            ref: '<?php echo $tx_ref; ?>',
            metadata: {
                custom_fields: [
                    { display_name: "Customer Name", variable_name: "customer_name", value: customerName },
                    { display_name: "Mobile Number", variable_name: "mobile_number", value: customerPhone }
                ]
            },
            callback: function(response) {
                // Verify on backend
                fetch('verify_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        reference: response.reference,
                        order_data: Object.fromEntries(formData)
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        window.location.href = 'order_confirmation.php?order=' + result.order_number;
                    } else {
                        Toast.error('Verification failed: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Toast.error('An error occurred during verification.');
                });
            },
            onClose: function() {
                Toast.info('Payment window closed');
            }
        });
        
        handler.openIframe();
    });
    </script>
</body>
</html>