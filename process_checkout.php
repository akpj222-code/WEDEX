<?php
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Collect form data
$order_data = [
    'email' => htmlspecialchars($_POST['email'] ?? ''),
    'phone' => htmlspecialchars($_POST['phone'] ?? ''),
    'first_name' => htmlspecialchars($_POST['first_name'] ?? ''),
    'last_name' => htmlspecialchars($_POST['last_name'] ?? ''),
    'address' => htmlspecialchars($_POST['address'] ?? ''),
    'address2' => htmlspecialchars($_POST['address2'] ?? ''),
    'city' => htmlspecialchars($_POST['city'] ?? ''),
    'state' => htmlspecialchars($_POST['state'] ?? ''),
    'postal_code' => htmlspecialchars($_POST['postal_code'] ?? ''),
    'shipping_method' => htmlspecialchars($_POST['shipping_method'] ?? 'standard'),
    'payment_method' => htmlspecialchars($_POST['payment_method'] ?? 'card'),
    'order_notes' => htmlspecialchars($_POST['order_notes'] ?? ''),
];

// Validate required fields
$required_fields = ['email', 'phone', 'first_name', 'last_name', 'address', 'city', 'state'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($order_data[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['form_data'] = $order_data;
    header('Location: checkout.php');
    exit;
}

// Generate order number
$order_number = 'ORD-' . strtoupper(uniqid());

// In production, you would:
// 1. Save order to database
// 2. Process payment (integrate with Paystack, Flutterwave, etc.)
// 3. Send confirmation email
// 4. Update inventory

// For now, just save to session
$_SESSION['last_order'] = [
    'order_number' => $order_number,
    'order_data' => $order_data,
    'cart_items' => $_SESSION['cart'] ?? [],
    'order_date' => date('Y-m-d H:i:s'),
    'status' => 'pending'
];

// Clear cart
$_SESSION['cart'] = [];

// Redirect to order confirmation page
header('Location: order_confirmation.php?order=' . $order_number);
exit;
?>