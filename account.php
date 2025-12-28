<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: logout.php');
        exit;
    }
} catch (PDOException $e) {
    die("Could not retrieve user data.");
}

try {
    $wishlist_stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.image 
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
    ");
    $wishlist_stmt->execute([$_SESSION['user_id']]);
    $wishlist_items = $wishlist_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $wishlist_items = [];
}

$orders = [];
$active_tab = $_GET['tab'] ?? 'dashboard';
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

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">My Account</span>
            </nav>
        </div>
    </div>

    <!-- Account Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center pb-6 border-b mb-6">
                        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-teal-600"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-2">
                        <a href="?tab=dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'dashboard' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span class="font-medium">Dashboard</span></a>
                        <a href="?tab=orders" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'orders' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg><span class="font-medium">Orders</span></a>
                        <a href="?tab=wishlist" class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo $active_tab === 'wishlist' ? 'bg-teal-50 text-teal-600' : 'text-gray-700 hover:bg-gray-50'; ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg><span class="font-medium">Wishlist</span></a>
                        <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg><span class="font-medium">Logout</span></a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="lg:col-span-3">
                <?php if ($active_tab === 'dashboard'): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
                    <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! From your account dashboard you can view your recent orders and edit your password and account details.</p>
                </div>
                <?php elseif ($active_tab === 'orders'): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Order History</h2>
                    <p class="text-gray-600">You have not placed any orders yet.</p>
                </div>
                <?php elseif ($active_tab === 'wishlist'): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                    <?php if (empty($wishlist_items)): ?>
                        <p class="text-gray-600">Your wishlist is empty. Browse the <a href="shop.php" class="text-teal-600 hover:underline">shop</a> to find products you'll love!</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($wishlist_items as $item): ?>
                            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition group">
                                <div class="relative">
                                    <a href="product.php?id=<?php echo $item['id']; ?>">
                                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-48 object-cover" width="500" height="500" loading="lazy">
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
    function handleWishlistClick(productId, cardElement) {
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'removed') {
                    // Hide the card and show a toast message
                    cardElement.style.display = 'none';
                    Toast.info(data.message);
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
