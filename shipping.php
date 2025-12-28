<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Policy - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .policy-section { margin-bottom: 3rem; }
        .policy-section h3 { color: #0d9488; margin-bottom: 1rem; }
        .policy-section ul { list-style-type: disc; margin-left: 1.5rem; }
        .policy-section li { margin-bottom: 0.5rem; }
    </style>
</head>
<body class="bg-gray-50">
    
    <?php require 'Static/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-teal-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-medium">Shipping Policy</span>
            </nav>
        </div>
    </div>

    <!-- Shipping Policy Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Shipping Policy</h1>
                <p class="text-gray-600 mb-8">Last updated: <?php echo date('F j, Y'); ?></p>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Delivery Areas & Coverage</h3>
                    <p class="text-gray-700 mb-4">WEDEX Healthcare provides shipping services across all 36 states in Nigeria, including the Federal Capital Territory (FCT). We deliver to both urban and rural areas, though delivery times may vary based on location accessibility.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Shipping Methods & Timeframes</h3>
                    <div class="bg-gray-50 p-6 rounded-lg mb-4">
                        <h4 class="font-semibold text-lg mb-3">Standard Shipping</h4>
                        <ul class="text-gray-700">
                            <li><strong>Delivery Time:</strong> 3-7 business days</li>
                            <li><strong>Coverage:</strong> Major cities and state capitals</li>
                            <li><strong>Cost:</strong> ₦1,500 - ₦3,500 depending on location</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg mb-4">
                        <h4 class="font-semibold text-lg mb-3">Express Shipping</h4>
                        <ul class="text-gray-700">
                            <li><strong>Delivery Time:</strong> 1-3 business days</li>
                            <li><strong>Coverage:</strong> Limited to major metropolitan areas</li>
                            <li><strong>Cost:</strong> ₦3,000 - ₦6,000 depending on location</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-semibold text-lg mb-3">Free Shipping</h4>
                        <ul class="text-gray-700">
                            <li><strong>Condition:</strong> Orders over $500 (₦730,000)</li>
                            <li><strong>Delivery Time:</strong> 5-10 business days</li>
                            <li><strong>Coverage:</strong> All serviced locations</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Order Processing</h3>
                    <p class="text-gray-700 mb-4">Orders are processed within 24-48 hours after payment confirmation. You will receive an order confirmation email with tracking information once your order has been shipped.</p>
                    <ul class="text-gray-700">
                        <li>Orders placed before 12 PM on business days are processed the same day</li>
                        <li>Weekend orders are processed on the next business day</li>
                        <li>During peak seasons, processing may take up to 72 hours</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Tracking Your Order</h3>
                    <p class="text-gray-700 mb-4">Once your order is shipped, you will receive a tracking number via email and SMS. You can track your package using our tracking portal or through our logistics partners' websites.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Delivery Exceptions</h3>
                    <p class="text-gray-700 mb-4">In some cases, delivery may be delayed due to:</p>
                    <ul class="text-gray-700">
                        <li>Incorrect or incomplete address information</li>
                        <li>Weather conditions or natural disasters</li>
                        <li>Security situations in certain areas</li>
                        <li>Customs clearance for international shipments</li>
                        <li>Public holidays or strikes</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">International Shipping</h3>
                    <p class="text-gray-700 mb-4">We currently focus on serving customers within Nigeria. For international orders, please contact our customer service team for custom shipping arrangements and pricing.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Contact Information</h3>
                    <p class="text-gray-700">For any shipping-related inquiries, please contact our customer service team:</p>
                    <ul class="text-gray-700 mt-2">
                        <li><strong>Email:</strong>wedexhealthcareservices@gmail.com</li>
                        <li><strong>Phone:</strong> +234 803 516 1651</li>
                        <li><strong>Address:</strong>21 Enerhen Road, Effurun, Delta State, Warri,Nigeria</li>
                        <li><strong>Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM</li>
                    </ul>
                </div>

                <div class="mt-8 p-6 bg-teal-50 rounded-lg">
                    <p class="text-teal-800"><strong>Note:</strong> This shipping policy is subject to change without prior notice. Please check this page regularly for updates.</p>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>