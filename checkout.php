<?php
require_once 'config.php';
require_once 'config/flutterwave.php';

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
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
        ];
        $subtotal += $product['price'] * $quantity;
    }
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Free shipping threshold: $500 USD
$shipping = $subtotal > 500 ? 0 : 15;
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
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="js/notifications.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <a href="cart.php" class="text-gray-500 hover:text-teal-600">Cart</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Checkout</span>
            </nav>
        </div>
    </div>

    <?php if (FLUTTERWAVE_TEST_MODE): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
        <div class="container mx-auto px-4">
            <p class="font-bold">⚠️ Test Mode Active</p>
            <p class="text-sm">Use test card: 5531 8866 5214 2950 | CVV: 564 | Expiry: 09/32 | PIN: 3310 | OTP: 12345</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Checkout Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form id="checkout-form" class="space-y-6">
                    
                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Contact Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                                <input 
                                    type="email" 
                                    id="customer_email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($user_email); ?>"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="your.email@example.com"
                                >
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                <input 
                                    type="tel" 
                                    id="customer_phone"
                                    name="phone"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="+1 234 567 8900"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Shipping Address</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_firstname" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                                <input 
                                    type="text" 
                                    id="customer_firstname"
                                    name="first_name"
                                    value="<?php echo htmlspecialchars($user_first_name); ?>"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label for="customer_lastname" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                                <input 
                                    type="text" 
                                    id="customer_lastname"
                                    name="last_name"
                                    value="<?php echo htmlspecialchars($user_last_name); ?>"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                                <input 
                                    type="text" 
                                    id="address"
                                    name="address"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="House number and street name"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label for="address2" class="block text-sm font-semibold text-gray-700 mb-2">Apartment, suite, etc. (Optional)</label>
                                <input 
                                    type="text" 
                                    id="address2"
                                    name="address2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="Apartment, suite, unit, building, floor, etc."
                                >
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                                <input 
                                    type="text" 
                                    id="city"
                                    name="city"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                >
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State / Province *</label>
                                <input 
                                    type="text" 
                                    id="state"
                                    name="state"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="State or Province"
                                >
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Postal / ZIP Code *</label>
                                <input 
                                    type="text" 
                                    id="postal_code"
                                    name="postal_code"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    placeholder="12345"
                                >
                            </div>
                            <div>
                                <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                                <select 
                                    id="country"
                                    name="country"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white"
                                >
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="NG">Nigeria</option>
                                    <option value="GH">Ghana</option>
                                    <option value="KE">Kenya</option>
                                    <option value="ZA">South Africa</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="ES">Spain</option>
                                    <option value="IT">Italy</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="BE">Belgium</option>
                                    <option value="SE">Sweden</option>
                                    <option value="NO">Norway</option>
                                    <option value="DK">Denmark</option>
                                    <option value="FI">Finland</option>
                                    <option value="IE">Ireland</option>
                                    <option value="CH">Switzerland</option>
                                    <option value="AT">Austria</option>
                                    <option value="PT">Portugal</option>
                                    <option value="PL">Poland</option>
                                    <option value="CZ">Czech Republic</option>
                                    <option value="HU">Hungary</option>
                                    <option value="RO">Romania</option>
                                    <option value="GR">Greece</option>
                                    <option value="JP">Japan</option>
                                    <option value="CN">China</option>
                                    <option value="IN">India</option>
                                    <option value="SG">Singapore</option>
                                    <option value="MY">Malaysia</option>
                                    <option value="TH">Thailand</option>
                                    <option value="PH">Philippines</option>
                                    <option value="ID">Indonesia</option>
                                    <option value="VN">Vietnam</option>
                                    <option value="KR">South Korea</option>
                                    <option value="NZ">New Zealand</option>
                                    <option value="BR">Brazil</option>
                                    <option value="MX">Mexico</option>
                                    <option value="AR">Argentina</option>
                                    <option value="CL">Chile</option>
                                    <option value="CO">Colombia</option>
                                    <option value="PE">Peru</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="IL">Israel</option>
                                    <option value="EG">Egypt</option>
                                    <option value="MA">Morocco</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Order Notes (Optional)</h2>
                        <textarea 
                            name="order_notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            placeholder="Notes about your order, e.g. special delivery instructions"
                        ></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6 pb-6 border-b max-h-64 overflow-y-auto">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center space-x-3">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" width="64" height="64">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <p class="font-semibold text-gray-800 text-sm">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold">$<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-semibold"><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?></span>
                        </div>
                        <?php if ($subtotal <= 500): ?>
                        <p class="text-xs text-gray-500">Free shipping on orders over $500</p>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-600"><span>Tax (7.5%)</span><span class="font-semibold">$<?php echo number_format($tax, 2); ?></span></div>
                        <div class="border-t pt-3"><div class="flex justify-between text-lg font-bold text-gray-800"><span>Total</span><span>$<?php echo number_format($total, 2); ?></span></div></div>
                    </div>

                    <button type="button" id="pay-button" class="w-full bg-teal-600 text-white py-4 rounded-lg font-semibold hover:bg-teal-700 transition mb-4">
                        Pay $<?php echo number_format($total, 2); ?>
                    </button>

                    <div class="text-center text-sm text-gray-600">
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                            <span>Secured Payment</span>
                        </div>
                        <p class="text-xs">Your payment information is protected</p>
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
        const customerPhone = document.getElementById('customer_phone').value;
        const customerName = document.getElementById('customer_firstname').value + ' ' + document.getElementById('customer_lastname').value;

        // You would integrate with your payment processor here
        // For Flutterwave with USD, you would need to configure it for USD
        FlutterwaveCheckout({
            public_key: "<?php echo FLUTTERWAVE_PUBLIC_KEY; ?>",
            tx_ref: "<?php echo $tx_ref; ?>",
            amount: <?php echo $total; ?>,
            currency: "USD",
            payment_options: "card, banktransfer",
            redirect_url: "<?php echo FLUTTERWAVE_REDIRECT_URL; ?>",
            customer: {
                email: customerEmail,
                phone_number: customerPhone,
                name: customerName,
            },
            customizations: {
                title: "WEDEX Healthcare",
                description: "Payment for order",
                logo: "<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; ?>/images/Blue_Logo.png",
            },
            meta: {
                consumer_id: "<?php echo $user_id ?? 'guest'; ?>",
                order_data: JSON.stringify(Object.fromEntries(formData))
            },
            callback: function(data) {
                console.log("Payment callback:", data);
                if (data.status === "successful") {
                    fetch('verify_payment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            transaction_id: data.transaction_id,
                            tx_ref: data.tx_ref,
                            order_data: Object.fromEntries(formData)
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            window.location.href = 'order_confirmation.php?order=' + result.order_number;
                        } else {
                            Toast.error('Payment verification failed. Please contact support.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toast.error('An error occurred. Please contact support.');
                    });
                } else {
                    Toast.error('Payment was not successful. Please try again.');
                }
            },
            onclose: function() {
                Toast.info('Payment window closed');
            }
        });
    });
    </script>
</body>
</html>
