<?php
// Flutterwave Configuration

// Set to true for test mode, false for production
define('FLUTTERWAVE_TEST_MODE', true);

// Test Keys (your test account)
define('FLUTTERWAVE_TEST_PUBLIC_KEY', 'FLWPUBK_TEST-0b1efeaf9d9b10790b99b912f95cc8e9-X');
define('FLUTTERWAVE_TEST_SECRET_KEY', 'FLWSECK_TEST-ae7ea92659cf96b9a380e2bbbceb60f1-X');
define('FLUTTERWAVE_TEST_ENCRYPTION_KEY', 'FLWSECK_TEST8300bd669a55');

// Production Keys (client's keys - to be added later)
define('FLUTTERWAVE_LIVE_PUBLIC_KEY', '');
define('FLUTTERWAVE_LIVE_SECRET_KEY', '');
define('FLUTTERWAVE_LIVE_ENCRYPTION_KEY', '');

// Active keys based on mode
define('FLUTTERWAVE_PUBLIC_KEY', FLUTTERWAVE_TEST_MODE ? FLUTTERWAVE_TEST_PUBLIC_KEY : FLUTTERWAVE_LIVE_PUBLIC_KEY);
define('FLUTTERWAVE_SECRET_KEY', FLUTTERWAVE_TEST_MODE ? FLUTTERWAVE_TEST_SECRET_KEY : FLUTTERWAVE_LIVE_SECRET_KEY);
define('FLUTTERWAVE_ENCRYPTION_KEY', FLUTTERWAVE_TEST_MODE ? FLUTTERWAVE_TEST_ENCRYPTION_KEY : FLUTTERWAVE_LIVE_ENCRYPTION_KEY);

// Webhook URL (update with your actual domain)
define('FLUTTERWAVE_WEBHOOK_URL', 'https://wedexhealthcareservices.com/flutterwave_webhook.php');

// Redirect URLs
define('FLUTTERWAVE_REDIRECT_URL', 'https://wedexhealthcareservices.com/payment_callback.php');
define('FLUTTERWAVE_CANCEL_URL', 'https://wedexhealthcareservices.com/checkout.php');
?>