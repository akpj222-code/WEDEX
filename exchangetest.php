<?php
// test_rate.php
require_once 'config.php';

echo "<h2>Exchange Rate Test</h2>";
echo "<p>Current Rate: 1 USD = ₦" . number_format($current_exchange_rate, 2) . "</p>";
echo "<p>API Key: " . EXCHANGE_RATE_API_KEY . "</p>";

// Check error logs for debugging
echo "<p>Check your server error logs for exchange rate debugging information.</p>";
?>