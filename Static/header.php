<?php
// This file assumes config.php has been included on the page that requires it.
// So, the $pdo variable for database connection is available here.

// Fetch categories for the navigation dropdown
try {
    $nav_categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC LIMIT 5");
    $nav_categories = $nav_categories_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $nav_categories = []; // Default to empty array on error to prevent site crash
}

// Calculate total number of items in the cart from the session
$cart_item_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}
?>

<header class="bg-white shadow-md sticky top-0 z-50">
    <!-- Top Bar -->
    <div class="bg-teal-600 text-white py-2 text-sm">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>+234 803 516 1651, +234 706 886 6864, +234 815 278 0300</span>
                </span>
                <span class="hidden md:flex items-center space-x-1">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>wedexhealthcareservices@gmail.com</span>
                </span>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <span class="hidden md:flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Mon - Fri: 8am - 5pm</span>
                </span>
                <a href="contact.php" class="hover:text-teal-200">Support</a>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2">
                <img class="w-28 h-auto md:w-32 mr-[-40px] ml-[-40px]" src="images/Blue_Logo.png" alt="WEDEX Logo">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">WEDEX</h1>
                    <p class="text-xs text-gray-500">Healthcare Services</p>
                </div>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-2xl mx-8">
                <form class="w-full flex" action="shop.php" method="GET">
                    <input 
                        type="text" 
                        name="search"
                        placeholder="Search for medical supplies..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    >
                    <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-r-lg hover:bg-teal-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            <!-- Header Actions & Mobile Menu Toggle -->
            <div class="flex items-center space-x-4 md:space-x-6">
                <a href="account.php" class="hidden md:flex items-center space-x-2 text-gray-700 hover:text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-sm font-medium">Account</span>
                </a>
                <a href="cart.php" class="flex items-center space-x-2 text-gray-700 hover:text-teal-600 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <?php if ($cart_item_count > 0): ?>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        <?php echo $cart_item_count; ?>
                    </span>
                    <?php endif; ?>
                    <span class="text-sm font-medium hidden md:inline">Cart</span>
                </a>
                <!-- Hamburger Button -->
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-gray-700 hover:text-teal-600 focus:outline-none">
                        <svg id="menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Collapsible Menu: Search (Mobile/Tablet) + Navigation -->
        <div id="mobile-menu" class="hidden md:block">
            <!-- Search Bar (Mobile/Tablet) -->
            <div class="mt-4 lg:hidden">
                <form class="w-full flex" action="shop.php" method="GET">
                    <input 
                        type="text" 
                        name="search"
                        placeholder="Search for medical supplies..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    >
                    <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-r-lg hover:bg-teal-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-4 border-t pt-4">
                <ul class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-8 text-base md:text-sm font-medium">
                    <li><a href="index.php" class="block py-2 text-teal-600 hover:text-teal-700">Home</a></li>
                    <li><a href="shop.php" class="block py-2 text-gray-700 hover:text-teal-600">Shop All</a></li>
                    <li class="relative group">
                        <a href="#" class="flex items-center justify-between py-2 text-gray-700 hover:text-teal-600">
                            <span>Categories</span>
                            <svg class="w-4 h-4 ml-1 transform transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </a>
                        <div class="md:absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 py-2 w-48">
                            <?php foreach ($nav_categories as $category): ?>
                                <a href="shop.php?category=<?php echo urlencode($category); ?>" class="block px-4 py-2 hover:bg-gray-100"><?php echo htmlspecialchars($category); ?></a>
                            <?php endforeach; ?>
                             <a href="shop.php" class="block px-4 py-2 font-bold hover:bg-gray-100">View All...</a>
                        </div>
                    </li>
                    <li><a href="about.php" class="block py-2 text-gray-700 hover:text-teal-600">About</a></li>
                    <li><a href="contact.php" class="block py-2 text-gray-700 hover:text-teal-600">Contact</a></li>
                     <li class="md:hidden pt-4 border-t"><a href="account.php" class="block py-2 text-gray-700 hover:text-teal-600">My Account</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-icon-open');
        const closeIcon = document.getElementById('menu-icon-close');

        if (menuToggle && mobileMenu && openIcon && closeIcon) {
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                openIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    </script>
</header>
