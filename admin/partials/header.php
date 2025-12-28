<?php
// This file is included at the top of all admin pages.
// It checks for a valid admin session and includes the common HTML head and navigation.
// The config.php file, which is required before this file, handles starting the session.

// If the user is not logged in (the session variable is not set), redirect to the login page.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard" class="text-2xl font-bold text-teal-600">WEDEX Admin</a>
                
                <!-- Hamburger Menu for Mobile -->
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-gray-800 hover:text-teal-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="dashboard" class="text-gray-600 hover:text-teal-600 font-medium">Dashboard</a>
                    <a href="../" target="_blank" class="text-gray-600 hover:text-teal-600 font-medium">View Site</a>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-900 transition">Logout</a>
                </nav>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <nav class="flex flex-col space-y-4">
                    <a href="dashboard" class="text-gray-600 hover:text-teal-600 font-medium">Dashboard</a>
                    <a href="../" target="_blank" class="text-gray-600 hover:text-teal-600 font-medium">View Site</a>
                    <span class="text-gray-700 border-t pt-4">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-900 transition text-center">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <main>

    <script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
    </script>