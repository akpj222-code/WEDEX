<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Refunds Policy - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .policy-section { margin-bottom: 3rem; }
        .policy-section h3 { color: #0d9488; margin-bottom: 1rem; }
        .warning-box { background-color: #fef2f2; border-left: 4px solid #dc2626; }
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
                <span class="text-gray-800 font-medium">Returns & Refunds</span>
            </nav>
        </div>
    </div>

    <!-- Returns & Refunds Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Returns & Refunds Policy</h1>
                <p class="text-gray-600 mb-8">Last updated: <?php echo date('F j, Y'); ?></p>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">30-Day Return Policy</h3>
                    <p class="text-gray-700 mb-4">We want you to be completely satisfied with your purchase. Most items can be returned within 30 days of delivery for a full refund or exchange.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Eligibility for Returns</h3>
                    <p class="text-gray-700 mb-4">To be eligible for a return, your item must be:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4">
                        <li>In its original condition</li>
                        <li>Unused and in original packaging</li>
                        <li>Accompanied by the original receipt or proof of purchase</li>
                        <li>Returned within 30 days of delivery</li>
                    </ul>
                </div>

                <div class="policy-section warning-box p-6 rounded-lg mb-6">
                    <h4 class="font-semibold text-red-800 mb-2">Non-Returnable Items</h4>
                    <p class="text-red-700">For health and safety reasons, the following items cannot be returned:</p>
                    <ul class="list-disc list-inside text-red-700 mt-2">
                        <li>Opened medical supplies and consumables</li>
                        <li>Personal care items (if seal is broken)</li>
                        <li>Custom-made or personalized equipment</li>
                        <li>Items marked as "final sale" or "non-returnable"</li>
                        <li>Prescription medical devices</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Return Process</h3>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-semibold text-lg mb-4">Step-by-Step Return Procedure</h4>
                        <ol class="list-decimal list-inside text-gray-700 space-y-3">
                            <li><strong>Contact Customer Service:</strong> Email returns@wedexhealthcare.com or call +234-XXX-XXXX-XXX to initiate your return</li>
                            <li><strong>Receive Return Authorization:</strong> We'll provide you with a Return Authorization Number and instructions</li>
                            <li><strong>Package Your Item:</strong> Include all original packaging, accessories, and documentation</li>
                            <li><strong>Ship Your Return:</strong> Use the provided shipping label or our recommended courier service</li>
                            <li><strong>Receive Confirmation:</strong> We'll notify you once we receive and inspect your return</li>
                        </ol>
                    </div>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Refund Processing</h3>
                    <p class="text-gray-700 mb-4">Once your return is received and inspected, we will send you an email to notify you of the approval or rejection of your refund.</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4">
                        <li><strong>Approved Refunds:</strong> Processed within 7-10 business days</li>
                        <li><strong>Payment Method:</strong> Refunds are issued to the original payment method</li>
                        <li><strong>Shipping Costs:</strong> Original shipping costs are non-refundable</li>
                        <li><strong>Return Shipping:</strong> Customer is responsible for return shipping costs unless the return is due to our error</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Damaged or Defective Items</h3>
                    <p class="text-gray-700 mb-4">If you receive a damaged or defective item, please contact us immediately. We will arrange for a replacement or refund at no additional cost to you.</p>
                    <p class="text-gray-700">Please provide photos of the damaged item and packaging to assist with your claim.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Exchanges</h3>
                    <p class="text-gray-700 mb-4">We replace items if they are defective or damaged. If you need to exchange an item for the same product, contact us with your order details.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Late or Missing Refunds</h3>
                    <p class="text-gray-700 mb-4">If you haven't received your refund within 10 business days, please:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Check your bank account again</li>
                        <li>Contact your credit card company (processing times may vary)</li>
                        <li>Contact your bank</li>
                        <li>If you've done all of the above and still haven't received your refund, contact us</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Contact Information</h3>
                    <p class="text-gray-700">For returns and refunds inquiries:</p>
                    <ul class="text-gray-700 mt-2">
                        <li><strong>Email:</strong>wedexhealthcareservices@gmail.com</li>
                        <li><strong>Phone:</strong> +234 803 516 1651</li>
                        <li><strong>Address:</strong>21 Enerhen Road, Effurun, Delta State, Warri,Nigeria</li>
                        <li><strong>Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</li>
                    </ul>
                </div>

                <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                    <p class="text-blue-800"><strong>Important:</strong> This policy is designed to ensure the health and safety of all our customers while providing fair return options. We reserve the right to refuse returns that don't meet our policy requirements.</p>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>