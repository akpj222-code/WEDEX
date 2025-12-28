<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - WEDEX Healthcare Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">

    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">About Us</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12 md:py-20">
        
        <!-- Our Story Section -->
        <section class="mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Our Story</h1>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        WEDEX Healthcare Services was founded with a simple yet powerful mission: to make quality medical equipment and supplies accessible and affordable for everyone. We saw a need for a reliable source where healthcare professionals and individuals alike could find certified, top-tier products without compromise.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        From our humble beginnings, we have grown into a trusted name in the healthcare industry, known for our commitment to quality, customer satisfaction, and ethical practices. We believe that access to proper medical tools is fundamental to health and well-being, and we are dedicated to supporting the healthcare community with the best products available.
                    </p>
                </div>
                <div class="order-1 lg:order-2">
                    <img src="https://placehold.co/600x400/E0F2F1/374151?text=Our+Facility" alt="WEDEX Healthcare Facility" class="rounded-lg shadow-lg w-full h-auto">
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="py-16 bg-white rounded-lg shadow-md">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Why Choose Us?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-6">
                        <div class="bg-teal-100 text-teal-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Quality Assurance</h3>
                        <p class="text-gray-600">We source only from certified manufacturers, ensuring every product meets the highest standards of safety and efficacy.</p>
                    </div>
                    <div class="p-6">
                        <div class="bg-teal-100 text-teal-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Expert Team</h3>
                        <p class="text-gray-600">Our team consists of experienced professionals who are passionate about healthcare and ready to assist you.</p>
                    </div>
                    <div class="p-6">
                        <div class="bg-teal-100 text-teal-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Customer Focus</h3>
                        <p class="text-gray-600">Your needs are our priority. We provide dedicated support to ensure a seamless and satisfactory experience.</p>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>
