<?php
require_once 'config.php';

$cart_items = [];
$subtotal = 0;
$product_ids_in_cart = [];

define('USD_TO_NGN_RATE', 1460);

// Check if the cart session exists and is not empty
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
        $price_in_usd = $product['price'] / $current_exchange_rate;
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $price_in_usd,
            'image' => $product['image'],
            'sku' => $product['sku'],
            'stock' => $product['stock'],
            'quantity' => $quantity,
        ];
        $subtotal += $price_in_usd * $quantity;
        $product_ids_in_cart[] = $product_id;
    }
}

// Calculate totals
// Free shipping threshold: $500 USD
$shipping = $subtotal > 500 ? 0 : 15;
$tax_rate = 0.075;
$tax = $subtotal * $tax_rate;
$total = $subtotal + $shipping + $tax;


// Fetch suggested products
$suggested_products = [];
$suggest_sql = "SELECT * FROM products WHERE stock > 0 ";
$suggest_params = [];

if (!empty($product_ids_in_cart)) {
    $placeholders_suggest = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
    $suggest_sql .= " AND id NOT IN ($placeholders_suggest) ";
    $suggest_params = $product_ids_in_cart;
}

$suggest_sql .= " ORDER BY RAND() LIMIT 4";
$suggest_stmt = $pdo->prepare($suggest_sql);
$suggest_stmt->execute($suggest_params);
$suggested_products = $suggest_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <script src="js/notifications.js"></script>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Shopping Cart</span>
            </nav>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

        <?php if (!empty($cart_items)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="p-4 md:p-6 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-24 h-24 object-cover rounded-lg flex-shrink-0" width="96" height="96">
                            <div class="flex-1 text-center md:text-left">
                                <a href="product.php?id=<?php echo $item['id']; ?>" class="font-semibold text-gray-800 hover:text-teal-600"><?php echo htmlspecialchars($item['name']); ?></a>
                                <p class="text-sm text-gray-500 mt-1">SKU: <?php echo htmlspecialchars($item['sku']); ?></p>
                                <button onclick="removeItem(<?php echo $item['id']; ?>)" class="md:hidden text-red-600 hover:text-red-700 text-sm mt-2">Remove</button>
                            </div>
                            <div class="flex items-center border rounded-lg">
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" class="px-4 py-2 hover:bg-gray-100 transition rounded-l-lg">-</button>
                                <input type="number" value="<?php echo $item['quantity']; ?>" class="w-12 text-center border-0 focus:outline-none font-semibold" readonly>
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" class="px-4 py-2 hover:bg-gray-100 transition rounded-r-lg">+</button>
                            </div>
                            <div class="w-24 text-center">
                                <p class="font-bold text-gray-800">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                            <button onclick="removeItem(<?php echo $item['id']; ?>)" class="hidden md:block text-red-600 hover:text-red-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold">$<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-semibold"><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?></span>
                        </div>
                         <?php if ($subtotal <= 500): ?>
                        <p class="text-xs text-gray-500">Free shipping on orders over $500</p>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-600"><span>Tax (VAT 7.5%)</span><span class="font-semibold">$<?php echo number_format($tax, 2); ?></span></div>
                        <div class="border-t pt-4"><div class="flex justify-between text-lg font-bold text-gray-800"><span>Total</span><span>$<?php echo number_format($total, 2); ?></span></div></div>
                    </div>
                    <a href="checkout.php" class="block w-full bg-teal-600 text-white text-center py-4 rounded-lg font-semibold hover:bg-teal-700 transition">Proceed to Checkout</a>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet.</p>
            <a href="shop.php" class="inline-block bg-teal-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Start Shopping</a>
        </div>
        <?php endif; ?>

        <!-- You May Also Like -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($suggested_products as $product): ?>
                <?php $price_in_usd = $product['price'] / $current_exchange_rate; ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                     <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover" width="500" height="500" loading="lazy">
                    </a>
                    <div class="p-4">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block h-12"><?php echo htmlspecialchars($product['name']); ?></a>
                        <div class="flex items-center justify-between">
                            <p class="text-lg md:text-xl font-bold text-gray-800">$<?php echo number_format($price_in_usd, 2); ?></p>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
        function updateQuantity(productId, change) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&change=${change}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Toast.error(data.message || 'Could not update quantity.');
                }
            });
        }

        function removeItem(productId) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Toast.success('Item removed from cart');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Toast.error(data.message || 'Could not remove item.');
                }
            });
        }
        
        function addToCart(productId, quantity = 1) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.success('Product added to cart!');
            // Update cart count in header
            if (data.cart_count !== undefined) {
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.cart_count;
                }
            }
        } else {
            Toast.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.error('An error occurred. Please try again.');
    });
}
    </script>
</body>
</html>