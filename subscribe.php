<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // In production, save to database
        // For now, just return success
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for subscribing!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>