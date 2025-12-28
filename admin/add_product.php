<?php
require_once '../config.php';
require_once 'partials/header.php';

// Get current exchange rate
$exchange_stmt = $pdo->query("SELECT rate FROM exchange_rates WHERE base_currency = 'USD' AND target_currency = 'NGN' AND is_active = 1 ORDER BY last_updated DESC LIMIT 1");
$current_exchange_rate = $exchange_stmt->fetch(PDO::FETCH_COLUMN) ?: 1460;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['name']) || !isset($_POST['price_ngn']) || $_POST['price_ngn'] === '' || empty($_POST['description'])) {
        $error = 'Please fill in all required fields: Name, Price, and Description.';
    } else {
        $image_names = []; // Array to store multiple image names
        
        // Handle multiple file uploads
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $uploaded_files = $_FILES['images'];
    $file_count = count($uploaded_files['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($uploaded_files['error'][$i] == 0) {
            $image_name = uniqid() . '-' . basename($uploaded_files["name"][$i]);
            $target_file = $target_dir . $image_name;
            
            if (move_uploaded_file($uploaded_files["tmp_name"][$i], $target_file)) {
                // Compress the uploaded image using TinyPNG
                compressImageWithTinyPNG($target_file);
                
                $image_names[] = $image_name;
            } else {
                $error = "Sorry, there was an error uploading one or more files.";
                break;
            }
        }
    }
}
        
        // If no images uploaded, use default
        if (empty($image_names)) {
            $image_names[] = 'default.jpg';
        }
        
        // Convert array to comma-separated string for database storage
        $images_string = implode(',', $image_names);
        
        if (empty($error)) {
            try {
                // Price is now entered in NGN, so we store it directly
                $price_ngn = (float)$_POST['price_ngn'];

                $sql = "INSERT INTO products (name, description, price, category, stock, image, sku, brand, features, specifications, dimensions, colors, material, finished_type, delivery_days, is_featured) 
                        VALUES (:name, :description, :price, :category, :stock, :image, :sku, :brand, :features, :specifications, :dimensions, :colors, :material, :finished_type, :delivery_days, :is_featured)";
                
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':name' => $_POST['name'],
                    ':description' => $_POST['description'],
                    ':price' => $price_ngn, // Save NGN price directly in database
                    ':category' => $_POST['category'],
                    ':stock' => $_POST['stock'],
                    ':image' => $images_string,
                    ':sku' => $_POST['sku'] ?? null,
                    ':brand' => $_POST['brand'] ?? null,
                    ':features' => $_POST['features'] ?? null,
                    ':specifications' => $_POST['specifications'] ?? null,
                    ':dimensions' => $_POST['dimensions'] ?? null,
                    ':colors' => $_POST['colors'] ?? null,
                    ':material' => $_POST['material'] ?? null,
                    ':finished_type' => $_POST['finished_type'] ?? null,
                    ':delivery_days' => $_POST['delivery_days'] ?? 3,
                    ':is_featured' => isset($_POST['is_featured']) ? 1 : 0
                ]);
                
                $_SESSION['success_message'] = 'Product added successfully!';
                header('Location: dashboard');
                exit;

            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Product</h1>

    <!-- Exchange Rate Display -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-blue-800">Current Exchange Rate</h3>
                <p class="text-blue-600">1 USD = ₦<?php echo number_format($current_exchange_rate, 2); ?></p>
                <p class="text-sm text-blue-500">Prices are stored in Naira in the database</p>
            </div>
            <a href="exchange_rate" style="background-color:royalblue;"  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                Manage Rates
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg p-8">
        <form action="add_product" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                <div>
                    <label for="price_ngn" class="block text-sm font-medium text-gray-700">Price in Naira *</label>
                    <div class="grid grid-cols-2 gap-4">
                         <div class="col-span-2">
                            <input type="number" name="price_ngn" id="price_ngn" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" placeholder="e.g., 50000.00" value="<?php echo htmlspecialchars($_POST['price_ngn'] ?? ''); ?>">
                        </div>
                         <div class="col-span-2">
                            <p class="text-sm text-gray-600">Equivalent in USD: <span id="usd-equivalent" class="font-semibold">$0.00</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                <textarea name="description" id="description" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                 <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>">
                </div>
            </div>

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Product Images (Multiple)</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                <p class="text-xs text-gray-500 mt-1">You can select multiple images. The first image will be the main product image.</p>
                <div id="image-preview" class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2 hidden">
                    <!-- Image previews will be shown here -->
                </div>
            </div>
            
            <div class="border-t pt-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Additional Details</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" name="brand" id="brand" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($_POST['brand'] ?? ''); ?>">
                    </div>
                     <div>
                        <label for="material" class="block text-sm font-medium text-gray-700">Material</label>
                        <input type="text" name="material" id="material" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($_POST['material'] ?? ''); ?>">
                    </div>
                </div>
                <div>
                    <label for="features" class="block text-sm font-medium text-gray-700">Features (JSON format)</label>
                    <textarea name="features" id="features" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder='["Feature 1", "Feature 2"]'><?php echo htmlspecialchars($_POST['features'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label for="specifications" class="block text-sm font-medium text-gray-700">Specifications (JSON format)</label>
                    <textarea name="specifications" id="specifications" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder='{"Key 1": "Value 1", "Key 2": "Value 2"}'><?php echo htmlspecialchars($_POST['specifications'] ?? ''); ?></textarea>
                </div>
                 <div>
                    <label for="colors" class="block text-sm font-medium text-gray-700">Colors (Comma-separated)</label>
                    <input type="text" name="colors" id="colors" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Gray,Blue,Yellow" value="<?php echo htmlspecialchars($_POST['colors'] ?? ''); ?>">
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="dimensions" class="block text-sm font-medium text-gray-700">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($_POST['dimensions'] ?? ''); ?>">
                    </div>
                     <div>
                        <label for="finished_type" class="block text-sm font-medium text-gray-700">Finished Type</label>
                        <input type="text" name="finished_type" id="finished_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($_POST['finished_type'] ?? ''); ?>">
                    </div>
                     <div>
                        <label for="delivery_days" class="block text-sm font-medium text-gray-700">Delivery Days</label>
                        <input type="number" name="delivery_days" id="delivery_days" value="<?php echo htmlspecialchars($_POST['delivery_days'] ?? '3'); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                 <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_featured" name="is_featured" type="checkbox" <?php echo (isset($_POST['is_featured']) ? 'checked' : ''); ?> class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                        <p class="text-gray-500">Check this to display the product on the homepage.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="dashboard" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exchangeRate = <?php echo $current_exchange_rate; ?>;
    const priceNgnInput = document.getElementById('price_ngn');
    const usdEquivalent = document.getElementById('usd-equivalent');

    function updateUsdEquivalent() {
        const ngnValue = parseFloat(priceNgnInput.value);
        if (!isNaN(ngnValue) && ngnValue >= 0) {
            const usdValue = (ngnValue / exchangeRate).toFixed(2);
            usdEquivalent.textContent = '$' + usdValue.replace(/\d(?=(\d{3})+\.)/g, '$&,');
        } else {
            usdEquivalent.textContent = '$0.00';
        }
    }

    priceNgnInput.addEventListener('input', updateUsdEquivalent);
    
    // Initialize on page load
    updateUsdEquivalent();

    // Image preview functionality
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        imagePreview.classList.add('hidden');

        if (this.files && this.files.length > 0) {
            imagePreview.classList.remove('hidden');
            
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border">
                            <span class="absolute top-1 right-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">${i + 1}</span>
                        `;
                        imagePreview.appendChild(previewDiv);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        }
    });
});
</script>

<?php require_once 'partials/footer.php'; ?>