<?php
/**
 * Password Hash Generator
 *
 * Use this script to generate a secure password hash for your admin users.
 * 1. Change the value of $plainPassword to your desired secure password.
 * 2. Upload this file to your server's root directory.
 * 3. Navigate to this file in your browser (e.g., http://localhost/yourproject/hash_password.php).
 * 4. Copy the generated hash string.
 * 5. Paste it into the `password` column for your admin user in the `admins` table via phpMyAdmin.
 * 6. DELETE THIS FILE from your server once you are done.
 */

// ****************************************************
// ENTER YOUR DESIRED PASSWORD HERE
$plainPassword = 'admin123';
// ****************************************************

// Generate the hash using PHP's recommended BCRYPT algorithm.
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Display the hash
echo '<h1>Password Hash Generator</h1>';
echo '<p><strong>Plain Password:</strong> ' . htmlspecialchars($plainPassword) . '</p>';
echo '<p><strong>Generated Hash (Copy this value):</strong></p>';
echo '<textarea readonly rows="3" cols="80" style="font-family: monospace; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%; box-sizing: border-box;" onclick="this.select();">' . htmlspecialchars($hashedPassword) . '</textarea>';
echo '<p style="color: red; font-weight: bold; margin-top: 20px;">IMPORTANT: Delete this file from your server after you have copied the hash!</p>';

?>
