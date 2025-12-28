<?php
require_once '../config.php';
require_once 'partials/header.php';
require_once '../includes/exchange_rate.php';

$message = '';
$messageType = '';

// Handle manual refresh request
if (isset($_POST['refresh_rate'])) {
    $newRate = refreshExchangeRate();
    
    if ($newRate !== false) {
        $message = 'Exchange rate refreshed successfully! New rate: $1 = ₦' . number_format($newRate, 2);
        $messageType = 'success';
    } else {
        $message = 'Failed to refresh exchange rate. Using cached or fallback rate.';
        $messageType = 'error';
    }
}

$currentRate = getExchangeRate();
$lastUpdate = getExchangeRateUpdateTime();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">System Settings</h1>

    <?php if ($message): ?>
        <div class="<?php echo $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded mb-6" role="alert">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg p-8 mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Exchange Rate Settings</h2>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700 mb-2">
                        <span class="font-semibold">Current Exchange Rate:</span>
                    </p>
                    <p class="text-3xl font-bold text-blue-600">
                        $1 = ₦<?php echo number_format($currentRate, 2); ?>
                    </p>
                    <p class="text-xs text-gray-600 mt-2">
                        Last updated: <?php echo $lastUpdate; ?>
                    </p>
                </div>
                
                <form method="POST" action="">
                    <button type="submit" name="refresh_rate" class="bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-teal-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Now
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-2">How it works:</h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>Exchange rates are automatically fetched from a live currency API</li>
                    <li>Rates are cached for 6 hours to optimize performance</li>
                    <li>The system automatically refreshes every 6 hours</li>
                    <li>You can manually refresh anytime using the button above</li>
                    <li>If the API is unavailable, a fallback rate of ₦1,460 is used</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-sm text-yellow-800">
                    <strong>Note:</strong> Product prices are stored in Naira (₦). The USD equivalent is calculated in real-time based on the current exchange rate.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">System Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Cache Duration</p>
                <p class="text-xl font-semibold text-gray-800">6 Hours</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Fallback Rate</p>
                <p class="text-xl font-semibold text-gray-800">₦<?php echo number_format(FALLBACK_USD_TO_NGN_RATE, 2); ?></p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">API Provider</p>
                <p class="text-xl font-semibold text-gray-800">ExchangeRate-API</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Update Status</p>
                <p class="text-xl font-semibold text-green-600">Active</p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="dashboard.php" class="text-teal-600 hover:text-teal-700 font-semibold">
            ← Back to Dashboard
        </a>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>