<?php
require_once '../config.php';
require_once 'partials/header.php';

$message = '';
$message_type = '';

// Handle manual rate update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rate'])) {
    $new_rate = (float)$_POST['new_rate'];
    $old_rate = $current_exchange_rate;
    
    if ($new_rate > 0) {
        try {
            // Store new rate
            $stmt = $pdo->prepare("INSERT INTO exchange_rates (rate) VALUES (?)");
            $stmt->execute([$new_rate]);
            
            // Update all product prices
            if (updateAllProductPrices($new_rate, $old_rate)) {
                $message = "Exchange rate updated to ₦" . number_format($new_rate, 2) . "! All product prices have been adjusted.";
                $message_type = "success";
                
                // Refresh current rate
                $current_exchange_rate = $new_rate;
            } else {
                $message = "Exchange rate updated but there was an error updating product prices.";
                $message_type = "warning";
            }
        } catch (PDOException $e) {
            $message = "Error updating exchange rate: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Please enter a valid exchange rate.";
        $message_type = "error";
    }
}

// Handle manual API refresh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refresh_api'])) {
    $new_rate = fetchLiveExchangeRate();
    $old_rate = $current_exchange_rate;
    
    if (updateAllProductPrices($new_rate, $old_rate)) {
        $message = "Exchange rate refreshed from API! All product prices have been adjusted.";
        $message_type = "success";
        $current_exchange_rate = $new_rate;
    } else {
        $message = "Failed to refresh exchange rate from API.";
        $message_type = "error";
    }
}

// Get rate history
try {
    $history_stmt = $pdo->prepare("SELECT rate, last_updated FROM exchange_rates ORDER BY last_updated DESC LIMIT 10");
    $history_stmt->execute();
    $rate_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rate_history = [];
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Exchange Rate Management</h1>

    <?php if ($message): ?>
        <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : ($message_type === 'warning' ? 'bg-yellow-100 border-yellow-400 text-yellow-700' : 'bg-red-100 border-red-400 text-red-700'); ?> border px-4 py-3 rounded mb-6" role="alert">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Current Rate Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Current Exchange Rate</h2>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600">1 USD = ₦<?php echo number_format($current_exchange_rate, 2); ?></p>
                        <p class="text-blue-500 mt-2">Last updated: <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                </div>

                <!-- Manual Rate Update -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Update Exchange Rate Manually</h3>
                    <form method="POST" class="flex gap-4">
                        <div class="flex-1">
                            <label for="new_rate" class="block text-sm font-medium text-gray-700 mb-1">New Rate (1 USD to NGN)</label>
                            <input type="number" name="new_rate" id="new_rate" step="0.01" min="0" value="<?php echo $current_exchange_rate; ?>" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" name="update_rate" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                Update Rate
                            </button>
                        </div>
                    </form>
                    <p class="text-sm text-gray-500 mt-2">Updating the rate will automatically adjust all product prices.</p>
                </div>

                <!-- API Refresh -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Refresh from Live API</h3>
                    <form method="POST">
                        <button type="submit" name="refresh_api" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                            Refresh from Live API
                        </button>
                    </form>
                    <p class="text-sm text-gray-500 mt-2">Fetches the latest exchange rate from the live API and updates all prices.</p>
                </div>
            </div>
        </div>

        <!-- Rate History -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Rate History</h2>
                
                <div class="space-y-3">
                    <?php if (empty($rate_history)): ?>
                        <p class="text-gray-500 text-center py-4">No rate history available.</p>
                    <?php else: ?>
                        <?php foreach ($rate_history as $history): ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <div>
                                    <p class="font-semibold text-gray-800">₦<?php echo number_format($history['rate'], 2); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo date('M j, H:i', strtotime($history['last_updated'])); ?></p>
                                </div>
                                <?php if ($history['rate'] == $current_exchange_rate): ?>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Current</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Section -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">How It Works</h3>
        <ul class="text-yellow-700 space-y-2">
            <li>• Products are stored in the database with Naira prices</li>
            <li>• Frontend displays prices in USD using current exchange rate</li>
            <li>• When exchange rate changes, all product prices are automatically adjusted</li>
            <li>• New products are priced in USD and converted to NGN for storage</li>
            <li>• The system automatically fetches rates every 24 hours</li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on rate input
    document.getElementById('new_rate').focus();
});
</script>

<?php require_once 'partials/footer.php'; ?>