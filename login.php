<?php
require_once 'config.php';

// If already logged in, redirect to account page
if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit;
}

$error = '';
$success = '';

// Handle traditional email/password login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Check for a valid user and that they have a password set (i.e., not a Google-only user)
            if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                header('Location: account.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
            error_log($e->getMessage());
        }
    }
}

// Handle traditional email/password registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'An account with this email already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($insert_stmt->execute([$first_name, $last_name, $email, $hashed_password])) {
                    $success = 'Account created successfully! You can now log in.';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'A database error occurred.';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register - WEDEX Healthcare</title>
        <link href="css/output.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <div class="container mx-auto px-4 py-16">
        <div class="max-w-5xl mx-auto">
            
            <div id="firebase-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 hidden" role="alert"></div>
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Login Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Login to Your Account</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="your.email@example.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Enter your password">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center"><input type="checkbox" class="w-4 h-4 text-teal-600 border-gray-300 rounded"><span class="ml-2 text-sm text-gray-600">Remember me</span></label>
                            <a href="#" class="text-sm text-teal-600 hover:text-teal-700">Forgot Password?</a>
                        </div>
                        <button type="submit" name="login" class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Login</button>
                    </form>
                    <div class="my-6 flex items-center">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="px-4 text-sm text-gray-500">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>
                    <button id="google-signin-btn" class="w-full flex items-center justify-center bg-white border border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C42.012,36.494,44,30.861,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                        </svg>
                        Sign in with Google
                    </button>
                </div>

                <!-- Registration Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Account</h2>
                    <form method="POST" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                <input type="text" id="first_name" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div>
                            <label for="reg_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="reg_email" name="reg_email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="your.email@example.com">
                        </div>
                        <div>
                            <label for="reg_password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" id="reg_password" name="reg_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Re-enter password">
                        </div>
                        <button type="submit" name="register" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <!-- Firebase SDKs (Traditional Script Include) -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>
    <script>
       console.log("Script started. Initializing Firebase...");

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCkUx4Zz4xKu1HyISfnw_pEBMGBDVbdCZw",
    authDomain: "wedex-health-990e3.firebaseapp.com",
    projectId: "wedex-health-990e3",
    storageBucket: "wedex-health-990e3.firebasestorage.app",
    messagingSenderId: "244841465651",
    appId: "1:244841465651:web:10460394f5ed05dd812046",
    measurementId: "G-H7609D1WRF"
};

// Initialize Firebase
try {
    firebase.initializeApp(firebaseConfig);
    console.log("Firebase initialized successfully.");
} catch (e) {
    console.error("Firebase initialization failed:", e);
}

const auth = firebase.auth();
const provider = new firebase.auth.GoogleAuthProvider();

const googleSignInBtn = document.getElementById('google-signin-btn');
const errorDiv = document.getElementById('firebase-error');

// Function to handle the authentication result and send it to the backend
// Function to handle the authentication result and send it to the backend
function handleAuthResult(user) {
    console.log("handleAuthResult called for user:", user);
    if (!user) {
        console.log("No user data found in handleAuthResult. Exiting.");
        return;
    }

    console.log("Sending user data to backend (firebase_auth.php)...");
    
    // Prepare the data to send
    const userData = {
        email: user.email,
        displayName: user.displayName || user.email.split('@')[0],
        uid: user.uid
    };
    
    console.log("Sending data:", userData);

    fetch('firebase_auth.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
    })
    .then(response => {
        console.log("Received response from backend. Status:", response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("Backend response data:", data);
        if (data.success) {
            console.log("Backend reports success. Redirecting to account.php...");
            window.location.href = 'account.php';
        } else {
            console.error("Backend reports an error:", data.message);
            errorDiv.textContent = data.message || 'An error occurred during authentication.';
            errorDiv.classList.remove('hidden');
        }
    })
    .catch((error) => {
        console.error('Server Communication Error (fetch failed):', error);
        errorDiv.textContent = 'Failed to communicate with the server: ' + error.message;
        errorDiv.classList.remove('hidden');
    });
}

// Event listener for the Google sign-in button
googleSignInBtn.addEventListener('click', () => {
    console.log("Google Sign-In button clicked. Starting signInWithPopup...");
    
    // Disable button to prevent multiple clicks
    googleSignInBtn.disabled = true;
    googleSignInBtn.textContent = 'Signing in...';
    
    auth.signInWithPopup(provider)
        .then((result) => {
            console.log("Popup sign-in successful:", result.user);
            handleAuthResult(result.user);
        })
        .catch((error) => {
            console.error("Popup sign-in error:", error);
            const errorMessage = error.message;
            const errorCode = error.code;
            console.error(`Firebase Error Code: ${errorCode}, Message: ${errorMessage}`);
            
            errorDiv.textContent = "Google Sign-In failed: " + errorMessage;
            errorDiv.classList.remove('hidden');
            
            // Re-enable button
            googleSignInBtn.disabled = false;
            googleSignInBtn.innerHTML = `
                <svg class="w-5 h-5 mr-3" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C42.012,36.494,44,30.861,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                </svg>
                Sign in with Google
            `;
        });
});
    </script>
</body>
</html>