<?php
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'u586966339_wedex');
define('DB_PASS', 'Wedexhealthcareservicesltd1');
define('DB_NAME', 'u586966339_wedex_db');

// TinyPNG API Key
define('TINIFY_API_KEY', 'tj6tZ4y9BVL0SfM4LSNmQM2Q42CZS83k');

// Create PDO instance
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Exchange rate configuration
define('EXCHANGE_RATE_API_KEY', '9157449413b3b0119e22504f');
define('DEFAULT_EXCHANGE_RATE', 1460); // Fallback rate

// Function to compress image using TinyPNG API
function compressImageWithTinyPNG($sourcePath) {
    try {
        // Check if file exists
        if (!file_exists($sourcePath)) {
            throw new Exception("Source file does not exist: " . $sourcePath);
        }

        // Skip compression for default.jpg
        if (basename($sourcePath) === 'default.jpg') {
            return true;
        }

        // TinyPNG API endpoint
        $url = "https://api.tinify.com/shrink";
        
        // Prepare the image data
        $imageData = file_get_contents($sourcePath);
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $imageData);
        curl_setopt($ch, CURLOPT_USERPWD, "api:" . TINIFY_API_KEY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/octet-stream"
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
        
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            throw new Exception("cURL Error: " . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($httpCode === 201) {
            // Success - get the compressed image URL from response
            $responseData = json_decode($response, true);
            $compressedUrl = $responseData['output']['url'];
            
            // Download the compressed image
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $compressedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "api:" . TINIFY_API_KEY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $compressedImage = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $compressedImage) {
                // Save the compressed image back to the same path
                if (file_put_contents($sourcePath, $compressedImage)) {
                    $originalSize = strlen($imageData);
                    $compressedSize = strlen($compressedImage);
                    $savings = round((($originalSize - $compressedSize) / $originalSize) * 100, 2);
                    
                    error_log("Image compressed successfully: " . basename($sourcePath) . " - Saved " . $savings . "%");
                    return true;
                } else {
                    throw new Exception("Failed to save compressed image");
                }
            } else {
                throw new Exception("Failed to download compressed image. HTTP Code: " . $httpCode);
            }
        } else {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['message']) ? $errorData['message'] : 'Unknown error';
            throw new Exception("TinyPNG API Error (" . $httpCode . "): " . $errorMessage);
        }
    } catch (Exception $e) {
        // Log the error but don't stop the process
        error_log("TinyPNG Compression Error for " . basename($sourcePath) . ": " . $e->getMessage());
        return false; // Return false but continue processing
    }
}

// Function to get current exchange rate
function getCurrentExchangeRate() {
    global $pdo;
    
    try {
        // Check if we have a recent rate (less than 24 hours old)
        $stmt = $pdo->prepare("SELECT rate FROM exchange_rates WHERE is_active = 1 AND last_updated >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY last_updated DESC LIMIT 1");
        $stmt->execute();
        $rate = $stmt->fetchColumn();
        
        if ($rate) {
            return (float)$rate;
        }
    } catch (PDOException $e) {
        error_log("Exchange rate database error: " . $e->getMessage());
    }
    
    // If no recent rate in database, fetch from API
    return fetchLiveExchangeRate();
}

// Function to fetch live exchange rate from API
function fetchLiveExchangeRate() {
    global $pdo;
    
    // Use the correct API endpoint with your API key
    $api_url = "https://v6.exchangerate-api.com/v6/" . EXCHANGE_RATE_API_KEY . "/latest/USD";
    
    try {
        // Use curl for better error handling
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            
            // Check if API response is successful
            if ($data['result'] === 'success' && isset($data['conversion_rates']['NGN'])) {
                $rate = (float)$data['conversion_rates']['NGN'];
                
                // Store the new rate in database
                try {
                    $stmt = $pdo->prepare("INSERT INTO exchange_rates (rate) VALUES (?)");
                    $stmt->execute([$rate]);
                    
                    error_log("Exchange rate updated successfully: 1 USD = " . $rate . " NGN");
                } catch (PDOException $e) {
                    error_log("Failed to store exchange rate: " . $e->getMessage());
                }
                
                return $rate;
            } else {
                error_log("Exchange rate API error: " . ($data['error-type'] ?? 'Unknown error'));
            }
        } else {
            error_log("Exchange rate API HTTP error: " . $http_code);
        }
    } catch (Exception $e) {
        error_log("Exchange rate API exception: " . $e->getMessage());
    }
    
    // Fallback to default rate
    error_log("Using fallback exchange rate: " . DEFAULT_EXCHANGE_RATE);
    return DEFAULT_EXCHANGE_RATE;
}

// Function to update all product prices based on new exchange rate
function updateAllProductPrices($newRate, $oldRate) {
    global $pdo;
    
    if ($oldRate == 0) return false;
    
    try {
        // Calculate conversion factor
        $conversionFactor = $newRate / $oldRate;
        
        // Update all product prices
        $stmt = $pdo->prepare("UPDATE products SET price = ROUND(price * ?, 2)");
        return $stmt->execute([$conversionFactor]);
    } catch (PDOException $e) {
        error_log("Price update error: " . $e->getMessage());
        return false;
    }
}

// Get current exchange rate
$current_exchange_rate = getCurrentExchangeRate();
?>