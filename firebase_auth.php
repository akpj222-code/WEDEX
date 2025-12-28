<?php
// 1. Include the configuration file to connect to the database.
require_once 'config.php';
// 2. Set the content type to JSON, as this script will only communicate with JavaScript.
header('Content-Type: application/json');

// 3. Log the request for debugging
error_log("Firebase auth request received. Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request data: " . file_get_contents("php://input"));

// 4. Get the user data sent from the Firebase JavaScript in the login page.
$input = file_get_contents("php://input");
$data = json_decode($input);

// 5. Basic validation to ensure we received the necessary data from the frontend.
if (!$data || !isset($data->email) || !isset($data->uid)) {
    error_log("Invalid data received from Firebase. Data: " . print_r($data, true));
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid data received from Firebase. Required: email and uid.',
        'received_data' => $data
    ]);
    exit;
}

// 6. Sanitize the data received.
$email = filter_var($data->email, FILTER_VALIDATE_EMAIL);
$firebase_uid = htmlspecialchars(trim($data->uid));
$displayName = htmlspecialchars(trim($data->displayName ?? 'New User'));

// Split the user's full name into first name and last name.
$name_parts = explode(' ', $displayName, 2);
$first_name = $name_parts[0] ?? 'User';
$last_name = $name_parts[1] ?? ''; // The last name might not exist.

if (!$email) {
    error_log("Invalid email format: " . ($data->email ?? 'empty'));
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email format: ' . ($data->email ?? 'empty')
    ]);
    exit;
}

if (empty($firebase_uid)) {
    error_log("Empty Firebase UID received");
    echo json_encode([
        'success' => false, 
        'message' => 'Empty Firebase UID received'
    ]);
    exit;
}

try {
    // 7. Check if a user with this email already exists in your database.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // USER EXISTS: Log them in by creating a PHP session.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        
        error_log("User logged in: " . $user['email'] . " (ID: " . $user['id'] . ")");

        // If their firebase_uid is not set (maybe they signed up with email first), update it now.
        if (empty($user['firebase_uid'])) {
            $update_stmt = $pdo->prepare("UPDATE users SET firebase_uid = ? WHERE id = ?");
            $update_stmt->execute([$firebase_uid, $user['id']]);
            error_log("Updated Firebase UID for existing user: " . $user['email']);
        }
        
        echo json_encode([
            'success' => true, 
            'action' => 'logged_in',
            'user_id' => $user['id'],
            'user_name' => $user['first_name']
        ]);
    } else {
        // USER DOES NOT EXIST: Create a new account for them in your database.
        $insert_stmt = $pdo->prepare(
            "INSERT INTO users (first_name, last_name, email, firebase_uid, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        
        if ($insert_stmt->execute([$first_name, $last_name, $email, $firebase_uid])) {
            // Get the ID of the new user we just created.
            $new_user_id = $pdo->lastInsertId();
            
            // Log the new user in immediately by creating a PHP session.
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_name'] = $first_name;
            
            error_log("New user registered: " . $email . " (ID: " . $new_user_id . ")");
            
            echo json_encode([
                'success' => true, 
                'action' => 'registered',
                'user_id' => $new_user_id,
                'user_name' => $first_name
            ]);
        } else {
            error_log("Failed to create new user in database: " . $email);
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to create a new user account in database.'
            ]);
        }
    }
} catch (PDOException $e) {
    // Log the detailed error for debugging
    error_log("Firebase Auth Database Error: " . $e->getMessage());
    error_log("SQL Error Code: " . $e->getCode());
    
    echo json_encode([
        'success' => false, 
        'message' => 'A database error occurred. Please try again later.',
        'error_code' => $e->getCode()
    ]);
}
?>