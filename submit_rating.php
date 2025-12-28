<?php
require_once 'config.php';

// Set header to return JSON responses
header('Content-Type: application/json');

// --- Security and Validation ---

// Rule 1: User must be logged in to rate a product.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to rate products.']);
    exit;
}

// Rule 2: The request must be a POST request to prevent direct URL access.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// --- Data Processing ---

$user_id = $_SESSION['user_id'];
// Sanitize and validate the incoming data from the user.
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

// Rule 3: Ensure the product ID and rating values are valid.
if ($product_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided. Please try again.']);
    exit;
}

try {
    // Use a database transaction to ensure all queries succeed or none do.
    $pdo->beginTransaction();

    // Check if the user has already rated this product.
    $stmt = $pdo->prepare("SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing_rating_id = $stmt->fetchColumn();

    if ($existing_rating_id) {
        // If a rating exists, the user is changing their mind. Update the existing rating.
        $update_stmt = $pdo->prepare("UPDATE product_ratings SET rating = ?, rated_at = NOW() WHERE id = ?");
        $update_stmt->execute([$rating, $existing_rating_id]);
    } else {
        // If no rating exists, this is a new rating. Insert it into the table.
        $insert_stmt = $pdo->prepare("INSERT INTO product_ratings (user_id, product_id, rating) VALUES (?, ?, ?)");
        $insert_stmt->execute([$user_id, $product_id, $rating]);
    }

    // After updating the rating, recalculate the product's overall average rating and review count.
    $recalc_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_reviews, 
            AVG(rating) as average_rating
        FROM product_ratings 
        WHERE product_id = ?
    ");
    $recalc_stmt->execute([$product_id]);
    $new_stats = $recalc_stmt->fetch(PDO::FETCH_ASSOC);

    $total_reviews = $new_stats['total_reviews'];
    $new_average_rating = $new_stats['average_rating'];

    // Update the main `products` table with the new, accurate statistics.
    $product_update_stmt = $pdo->prepare("UPDATE products SET rating = ?, reviews = ? WHERE id = ?");
    $product_update_stmt->execute([$new_average_rating, $total_reviews, $product_id]);

    // If all queries were successful, commit the changes to the database.
    $pdo->commit();

    // Return a success message along with the new data to update the page instantly.
    echo json_encode([
        'success' => true,
        'message' => 'Your rating has been submitted successfully!',
        'new_average_rating' => (float)$new_average_rating,
        'total_reviews' => (int)$total_reviews
    ]);

} catch (PDOException $e) {
    // If any error occurred, roll back all database changes.
    $pdo->rollBack();
    // Log the error for debugging purposes instead of showing it to the user.
    error_log("Rating submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}
