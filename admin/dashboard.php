<?php
require_once '../config.php';

// Start session explicitly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login');
    exit;
}

require_once 'partials/header.php';

define('USD_TO_NGN_RATE', 1460); // Exchange Rate: 1 USD = 1460 NGN

// Fetch all products from the database
try {
    $stmt = $pdo->query("SELECT id, name, category, price, stock, image FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not retrieve products: " . $e->getMessage());
}

// Add this at the top of your admin files after require_once '../config.php';
function getAdminExchangeRate() {
    // Simple API call without database dependency
    try {
        $response = file_get_contents("https://api.exchangerate-api.com/v4/latest/USD");
        $data = json_decode($response, true);
        
        if (isset($data['rates']['NGN'])) {
            return (float)$data['rates']['NGN'];
        }
    } catch (Exception $e) {
        // Fallback rate
    }
    
    return 1460;
}

$admin_rate = getAdminExchangeRate();
?>

<!-- Admin Dashboard Header Display -->
<div style="background-color:royalblue; border-radius:2px;padding:5px;" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="font-semibold">Welcome, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></span>
            </div>
            <div class="flex items-center space-x-6 text-sm">
                <div class="flex items-center space-x-2 bg-blue-400 px-3 py-1 rounded-full">
                    <span>💵</span>
                    <span>1 USD = ₦<?php echo number_format($admin_rate, 2); ?></span>
                </div>
                <span><?php echo date('l, F j, Y'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 text-center sm:text-left">Product Management</h1>
        <a href="quotation.php" class="bg-teal-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-teal-700 transition w-full sm:w-auto text-center">
            Create Quotation
        </a>
        <a href="add_product" class="bg-teal-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-teal-700 transition w-full sm:w-auto text-center">
            + Add New Product
        </a>
    </div>

    <?php 
    // Display success message
    if (isset($_SESSION['success_message'])): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg" role="alert">
        <p><?php echo $_SESSION['success_message']; ?></p>
    </div>
    <?php unset($_SESSION['success_message']); endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No products found. <a href="add_product" class="text-teal-600 hover:underline">Add one now</a>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-16 h-16 object-cover rounded-lg">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?php echo htmlspecialchars($product['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    <?php
                                        // Price from DB is in NGN. Convert to USD for display.
                                        $price_in_ngn = $product['price'];
                                        $price_in_usd = $price_in_ngn / USD_TO_NGN_RATE;
                                    ?>
                                    <div class="font-semibold">₦<?php echo number_format($price_in_ngn, 2); ?></div>
                                    <div class="text-xs text-gray-500">$<?php echo number_format($price_in_usd, 2); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo htmlspecialchars($product['stock']); ?> units
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="edit_product?id=<?php echo $product['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <a href="delete_product?id=<?php echo $product['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>