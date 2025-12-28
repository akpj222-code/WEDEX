<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .policy-section { margin-bottom: 3rem; }
        .policy-section h3 { color: #0d9488; margin-bottom: 1rem; }
        .highlight-box { background-color: #fffbeb; border-left: 4px solid #d97706; }
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
                <span class="text-gray-800 font-medium">Terms & Conditions</span>
            </nav>
        </div>
    </div>

    <!-- Terms & Conditions Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Terms & Conditions</h1>
                <p class="text-gray-600 mb-8">Last updated: <?php echo date('F j, Y'); ?></p>

                <div class="policy-section">
                    <p class="text-gray-700 mb-6">Welcome to WEDEX Healthcare. These Terms and Conditions govern your use of our website and the purchase of products from us. By accessing our website and making a purchase, you agree to be bound by these terms.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">1. Agreement to Terms</h3>
                    <p class="text-gray-700 mb-4">By accessing and using the WEDEX Healthcare website, you accept and agree to be bound by these Terms and Conditions. If you disagree with any part of these terms, you may not access our website or make purchases.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">2. Account Registration</h3>
                    <p class="text-gray-700 mb-4">To access certain features, you may be required to register for an account. You agree to:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Provide accurate, current, and complete information</li>
                        <li>Maintain and update your information to keep it accurate</li>
                        <li>Maintain the security of your password and accept all risks of unauthorized access</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">3. Products and Services</h3>
                    <p class="text-gray-700 mb-4">All products are subject to availability. We reserve the right to discontinue any product at any time. Prices for products are subject to change without notice.</p>
                    <div class="highlight-box p-6 rounded-lg">
                        <p class="text-amber-800"><strong>Medical Disclaimer:</strong> Our products are intended for use by healthcare professionals and trained individuals. Customers are responsible for proper usage and should consult with healthcare professionals when necessary.</p>
                    </div>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">4. Orders and Payment</h3>
                    <p class="text-gray-700 mb-4">By placing an order, you offer to purchase a product subject to these Terms. We reserve the right to refuse or cancel any order for any reason, including:</p>
                    <ul class="list-disc list-inside text-gray-700 mb-4">
                        <li>Product availability</li>
                        <li>Errors in product or pricing information</li>
                        <li>Suspected fraud or unauthorized activity</li>
                        <li>Inaccuracies in your provided information</li>
                    </ul>
                    <p class="text-gray-700">We accept various payment methods as displayed during checkout. All payments are processed securely through our payment partners.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">5. Shipping and Delivery</h3>
                    <p class="text-gray-700 mb-4">Shipping times and costs are outlined in our Shipping Policy. While we strive to meet estimated delivery dates, we are not responsible for delays caused by carriers or unforeseen circumstances.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">6. Returns and Refunds</h3>
                    <p class="text-gray-700 mb-4">Our Returns and Refunds Policy governs all returns and refund requests. Please review that policy for detailed information about eligibility and procedures.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">7. Intellectual Property</h3>
                    <p class="text-gray-700 mb-4">All content on this website, including text, graphics, logos, images, and software, is the property of WEDEX Healthcare or its content suppliers and is protected by intellectual property laws.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">8. User Conduct</h3>
                    <p class="text-gray-700 mb-4">You agree not to:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Use the website for any unlawful purpose</li>
                        <li>Attempt to gain unauthorized access to any part of the website</li>
                        <li>Interfere with the proper working of the website</li>
                        <li>Use any automated means to access the website</li>
                        <li>Submit false or misleading information</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">9. Disclaimer of Warranties</h3>
                    <p class="text-gray-700 mb-4">The website and products are provided "as is" without warranties of any kind, either express or implied. We do not warrant that the website will be uninterrupted or error-free.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">10. Limitation of Liability</h3>
                    <p class="text-gray-700 mb-4">To the fullest extent permitted by law, WEDEX Healthcare shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the website or products.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">11. Indemnification</h3>
                    <p class="text-gray-700 mb-4">You agree to indemnify and hold harmless WEDEX Healthcare and its affiliates from any claims, damages, or expenses arising from your use of the website or violation of these Terms.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">12. Governing Law</h3>
                    <p class="text-gray-700 mb-4">These Terms shall be governed by and construed in accordance with the laws of the Federal Republic of Nigeria, without regard to its conflict of law provisions.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">13. Changes to Terms</h3>
                    <p class="text-gray-700 mb-4">We reserve the right to modify these Terms at any time. We will notify users of significant changes by posting the updated Terms on our website. Continued use after changes constitutes acceptance.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">14. Contact Information</h3>
                    <p class="text-gray-700">For questions about these Terms and Conditions, please contact us:</p>
                    <ul class="text-gray-700 mt-2">
                        <li><strong>Email:</strong>wedexhealthcareservices@gmail.com</li>
                        <li><strong>Phone:</strong> +234 803 516 1651</li>
                        <li><strong>Address:</strong>21 Enerhen Road, Effurun, Delta State, Warri,Nigeria</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">15. Severability</h3>
                    <p class="text-gray-700 mb-4">If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary, and the remaining provisions shall remain in full force and effect.</p>
                </div>

                <div class="mt-8 p-6 bg-teal-50 rounded-lg">
                    <p class="text-teal-800"><strong>Important:</strong> These Terms and Conditions constitute the entire agreement between you and WEDEX Healthcare regarding your use of the website and purchase of products.</p>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>