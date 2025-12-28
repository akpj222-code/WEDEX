<?php
require_once 'config.php';

define('USD_TO_NGN_RATE', 1460);

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: shop.php");
    exit;
}

// Fetch the main product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>The product you are looking for does not exist.</p><a href='shop.php'>Go back to shop</a>";
    exit;
}

// Prepare images for the gallery
$images = !empty($product['image']) ? explode(',', $product['image']) : ['default.jpg'];
$main_image = $images[0];

// Fetch related products from the same category
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->execute([$product['category'], $product_id]);
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- RATING LOGIC ---
$is_in_wishlist = false;
$user_rating = 0; 
if (isset($_SESSION['user_id'])) {
    $wishlist_stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wishlist_stmt->execute([$_SESSION['user_id'], $product_id]);
    $is_in_wishlist = $wishlist_stmt->fetchColumn() > 0;

    $rating_stmt = $pdo->prepare("SELECT rating FROM product_ratings WHERE user_id = ? AND product_id = ?");
    $rating_stmt->execute([$_SESSION['user_id'], $product_id]);
    $result = $rating_stmt->fetchColumn();
    if ($result) {
        $user_rating = (int)$result;
    }
}

// Convert price to USD
$price_in_usd = $product['price'] / $current_exchange_rate;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="js/notifications.js"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 0.25rem; }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label { font-size: 2.5rem; color: #d1d5db; cursor: pointer; transition: color 0.2s ease-in-out; }
        .star-rating input[type="radio"]:not(:disabled) ~ label:hover,
        .star-rating input[type="radio"]:not(:disabled) ~ label:hover ~ label { color: #f59e0b; }
        .star-rating input[type="radio"]:checked ~ label { color: #f59e0b; }
        .star-rating input[type="radio"]:disabled ~ label { cursor: default; }
    </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm flex-wrap">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a><span class="text-gray-400">/</span>
                <a href="shop.php" class="text-gray-500 hover:text-teal-600">Shop</a><span class="text-gray-400">/</span>
                <a href="shop.php?category=<?php echo urlencode($product['category']); ?>" class="text-gray-500 hover:text-teal-600"><?php echo htmlspecialchars($product['category']); ?></a><span class="text-gray-400 hidden sm:inline">/</span>
                <span class="text-gray-800 font-medium truncate block sm:inline mt-1 sm:mt-0"><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-4 md:p-8">
                
                <!-- Product Image Gallery -->
                <div>
                    <div class="bg-gray-100 rounded-lg mb-4 h-80 md:h-96 flex items-center justify-center relative group">
                        <img id="main-product-image" src="uploads/<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full max-w-full object-contain transition-transform duration-300">
                        
                        <!-- Prev/Next Buttons -->
                        <button id="prev-btn" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/50 p-2 rounded-full hover:bg-white focus:outline-none transition-opacity opacity-0 group-hover:opacity-100 hidden">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button id="next-btn" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/50 p-2 rounded-full hover:bg-white focus:outline-none transition-opacity opacity-0 group-hover:opacity-100 hidden">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Thumbnails -->
                    <div id="thumbnail-container" class="flex flex-wrap justify-center gap-2">
                        <?php foreach ($images as $img): ?>
                        <div class="thumbnail-wrapper w-20 h-20 bg-gray-100 rounded-md p-1 cursor-pointer border-2 border-transparent hover:border-teal-500" onclick="changeImage(this)">
                             <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Thumbnail" class="w-full h-full object-contain">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="flex items-center mb-4">
                        <div id="star-display" class="flex text-yellow-400"></div>
                        <span id="reviews-count" class="text-sm text-gray-600 ml-2">(<?php echo (int)$product['reviews']; ?> reviews)</span>
                    </div>
                    <p class="text-3xl md:text-4xl font-bold text-teal-600 mb-4">$<?php echo number_format($price_in_usd, 2); ?></p>
                    <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-800 mb-2 text-lg">
                            <?php if ($user_rating > 0): ?>You rated this product<?php elseif (isset($_SESSION['user_id'])): ?>Rate this product<?php else: ?>Leave a rating<?php endif; ?>
                        </p>
                        <div class="star-rating" data-product-id="<?php echo $product['id']; ?>">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($user_rating == $i) ? 'checked' : ''; ?> <?php echo (!isset($_SESSION['user_id'])) ? 'disabled' : ''; ?> />
                                <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">&#9733;</label>
                            <?php endfor; ?>
                        </div>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <p class="text-sm text-gray-500 mt-2">Please <a href="login.php" class="text-teal-600 hover:underline font-medium">log in</a> to submit a rating.</p>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
                        <div class="flex items-center border rounded-lg justify-between">
                            <button onclick="updateQty(-1)" class="px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-l-lg">-</button>
                            <input id="quantity-input" type="text" value="1" class="w-12 h-full text-center border-0 focus:ring-0 font-semibold">
                            <button onclick="updateQty(1)" class="px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-r-lg">+</button>
                        </div>
                        <button onclick="handleAddToCart()" class="flex-1 bg-teal-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-teal-700 transition flex items-center justify-center space-x-2"><span>Add To Cart</span></button>
                        <button onclick="addToWishlist(<?php echo $product['id']; ?>, this)" class="border p-3 rounded-lg hover:bg-gray-50">
                            <svg class="w-6 h-6 <?php echo $is_in_wishlist ? 'text-red-500 fill-current' : 'text-gray-600'; ?>" fill="<?php echo $is_in_wishlist ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                <?php 
                    $related_images = !empty($related['image']) ? explode(',', $related['image']) : ['default.jpg'];
                    $related_price_in_usd = $related['price'] / USD_TO_NGN_RATE;
                ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <a href="product.php?id=<?php echo $related['id']; ?>" class="block">
                        <img src="uploads/<?php echo htmlspecialchars($related_images[0]); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" class="w-full h-64 object-cover" width="500" height="500" loading="lazy">
                    </a>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-1"><?php echo htmlspecialchars($related['category']); ?></p>
                        <a href="product.php?id=<?php echo $related['id']; ?>" class="font-semibold text-gray-800 mb-2 hover:text-teal-600 block h-12"><?php echo htmlspecialchars($related['name']); ?></a>
                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xl font-bold text-gray-800">$<?php echo number_format($related_price_in_usd, 2); ?></p>
                            <button onclick="addToCart(<?php echo $related['id']; ?>, 1)" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">Add To Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Image Gallery Logic ---
        const images = <?php echo json_encode($images); ?>;
        if (images.length > 0) {
            const mainImage = document.getElementById('main-product-image');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const thumbnails = document.querySelectorAll('.thumbnail-wrapper');
            let currentIndex = 0;

            function updateGallery(index) {
                if(index < 0 || index >= images.length) return;
                mainImage.src = 'uploads/' + images[index];
                thumbnails.forEach((thumb, i) => {
                    thumb.classList.toggle('border-teal-500', i === index);
                });
                currentIndex = index;
            }

            // Make onclick from PHP work
            window.changeImage = function(thumbElement) {
                const thumbIndex = Array.from(thumbnails).indexOf(thumbElement);
                if (thumbIndex !== -1) {
                    updateGallery(thumbIndex);
                }
            };
            
            if (images.length > 1) {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'block';
                
                prevBtn.addEventListener('click', () => {
                    updateGallery((currentIndex - 1 + images.length) % images.length);
                });
                
                nextBtn.addEventListener('click', () => {
                    updateGallery((currentIndex + 1) % images.length);
                });
            } else {
                 document.getElementById('thumbnail-container').style.display = 'none';
            }
            
            // Set the first thumbnail as active initially
            updateGallery(0);
        }

        // --- Rating Logic ---
        let averageRating = <?php echo (float)($product['rating'] ?? 0); ?>;
        let totalReviews = <?php echo (int)($product['reviews'] ?? 0); ?>;
        const hasRated = <?php echo json_encode($user_rating > 0); ?>;
        renderStarDisplay(); // Initial render

        function renderStarDisplay() {
            const starDisplayContainer = document.getElementById('star-display');
            starDisplayContainer.innerHTML = ''; 
            const roundedRating = Math.round(averageRating);
            for (let i = 1; i <= 5; i++) {
                const svgClass = i <= roundedRating ? 'fill-current' : 'text-gray-300';
                starDisplayContainer.innerHTML += `<svg class="w-5 h-5 ${svgClass}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>`;
            }
            document.getElementById('reviews-count').textContent = `(${totalReviews} reviews)`;
        }

        function submitRating(productId, rating) {
            fetch('submit_rating.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&rating=${rating}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Toast.success(data.message);
                    averageRating = data.new_average_rating;
                    totalReviews = data.total_reviews;
                    renderStarDisplay();
                    document.querySelectorAll('.star-rating input[type="radio"]').forEach(input => {
                        input.disabled = true;
                    });
                } else {
                    Toast.error(data.message || 'Could not submit rating.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toast.error('An error occurred. Please try again.');
            });
        }
        
        document.querySelectorAll('.star-rating input[type="radio"]').forEach(radio => {
            if (!hasRated) {
                radio.addEventListener('change', function() {
                    submitRating(this.closest('.star-rating').dataset.productId, this.value);
                });
            }
        });
    });

    // --- Cart and Wishlist Functions ---
    const quantityInput = document.getElementById('quantity-input');
    function updateQty(change) {
        let currentQty = parseInt(quantityInput.value);
        if (currentQty + change > 0) {
            quantityInput.value = currentQty + change;
        }
    }

    function handleAddToCart() {
        addToCart(<?php echo $product['id']; ?>, parseInt(quantityInput.value));
    }

    function addToCart(productId, quantity = 1) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.success('Product added to cart!');
            // Update cart count in header
            if (data.cart_count !== undefined) {
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.cart_count;
                }
            }
        } else {
            Toast.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.error('An error occurred. Please try again.');
    });
}

    function addToWishlist(productId, buttonElement) {
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const svg = buttonElement.querySelector('svg');
                if (data.action === 'added') {
                    svg.classList.add('text-red-500', 'fill-current');
                    Toast.success(data.message);
                } else {
                    svg.classList.remove('text-red-500', 'fill-current');
                    Toast.info(data.message);
                }
            } else {
                Toast.error(data.message);
                if (data.message.toLowerCase().includes('logged in')) {
                    setTimeout(() => window.location.href = 'login.php', 1500);
                }
            }
        });
    }
    </script>
</body>
</html>
