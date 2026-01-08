<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch User Data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) { header('Location: logout.php'); exit; }
} catch (PDOException $e) { die("Could not retrieve user data."); }

// Fetch Wishlist
try {
    $wishlist_stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.image FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
    $wishlist_stmt->execute([$_SESSION['user_id']]);
    $wishlist_items = $wishlist_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $wishlist_items = []; }

// Fetch Orders
try {
    $orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $orders_stmt->execute([$_SESSION['user_id']]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $orders = []; }

$active_tab = $_GET['tab'] ?? 'dashboard';
// Handle Flash Messages from cancel_order.php
$flash_msg = $_SESSION['flash_message'] ?? null;
$flash_type = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    <script src="js/notifications.js"></script>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">My Account</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if ($flash_msg): ?>
            <div class="<?php echo $flash_type === 'success' ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?> border-l-4 p-4 mb-6 rounded shadow-sm">
                <?php echo htmlspecialchars($flash_msg); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center pb-6 border-b mb-6">
                        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-teal-600"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <nav class="space-y-2">
                        <a href="?tab=dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'dashboard' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span class="font-medium">Dashboard</span></a>
                        <a href="?tab=orders" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'orders' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg><span class="font-medium">Orders</span></a>
                        <a href="?tab=wishlist" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'wishlist' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg><span class="font-medium">Wishlist</span></a>
                        <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg><span class="font-medium">Logout</span></a>
                    </nav>
                </div>
            </aside>

            <main class="lg:col-span-3">
                <?php if ($active_tab === 'dashboard'): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
                    <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</p>
                </div>
                
                <?php elseif ($active_tab === 'orders'): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Order History</h2>
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">You have not placed any orders yet.</p>
                            <a href="shop.php" class="inline-block bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Order #</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-teal-600">
                                            <?php echo htmlspecialchars($order['order_number']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php 
                                            $status = $order['order_status'];
                                            $colorClass = 'bg-gray-100 text-gray-800'; 
                                            if ($status === 'processing') $colorClass = 'bg-blue-100 text-blue-800';
                                            elseif ($status === 'shipped') $colorClass = 'bg-purple-100 text-purple-800';
                                            elseif ($status === 'delivered') $colorClass = 'bg-green-100 text-green-800';
                                            elseif ($status === 'cancelled') $colorClass = 'bg-red-100 text-red-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $colorClass; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                            <?php echo ($order['payment_method'] == 'paystack_usd') ? '$' : '₦'; ?>
                                            <?php echo number_format($order['total'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($status === 'processing'): ?>
                                                <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                                        Cancel Order
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">Cannot Cancel</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php elseif ($active_tab === 'wishlist'): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                    <?php if (empty($wishlist_items)): ?>
                        <p class="text-gray-600">Your wishlist is empty.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($wishlist_items as $item): ?>
                            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition group">
                                <div class="relative">
                                    <a href="product.php?id=<?php echo $item['id']; ?>">
                                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="w-full h-48 object-cover">
                                    </a>
                                    <button onclick="handleWishlistClick(<?php echo $item['id']; ?>, this.closest('.group'))" class="absolute top-2 right-2 bg-white p-2 rounded-full shadow-md">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <a href="product.php?id=<?php echo $item['id']; ?>" class="font-semibold text-gray-800 mb-2 block h-12 hover:text-teal-600"><?php echo htmlspecialchars($item['name']); ?></a>
                                    <p class="text-xl font-bold text-gray-800 mb-3">₦<?php echo number_format($item['price'], 2); ?></p>
                                    <button onclick="addToCart(<?php echo $item['id']; ?>)" class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition font-medium text-sm">Add to Cart</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
    <script>
    function addToCart(productId, quantity = 1) {
    fetch('add_to_cart.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `product_id=${productId}&quantity=${quantity}` })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.success('Product added to cart!');
            if (data.cart_count !== undefined) {
                const cartCount = document.querySelector('.cart-count');
                if(cartCount) cartCount.textContent = data.cart_count;
            }
        } else { Toast.error(data.message); }
    });
}
    function handleWishlistClick(productId, card) {
        fetch('add_to_wishlist.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'product_id=' + productId })
        .then(response => response.json())
        .then(data => { if (data.success && data.action === 'removed') { card.style.display = 'none'; Toast.info(data.message); } });
    }
    </script>
</body>
</html>