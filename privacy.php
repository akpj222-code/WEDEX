<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - WEDEX Healthcare</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .policy-section { margin-bottom: 3rem; }
        .policy-section h3 { color: #0d9488; margin-bottom: 1rem; }
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
                <span class="text-gray-800 font-medium">Privacy Policy</span>
            </nav>
        </div>
    </div>

    <!-- Privacy Policy Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Privacy Policy</h1>
                <p class="text-gray-600 mb-8">Last updated: <?php echo date('F j, Y'); ?></p>

                <div class="policy-section">
                    <p class="text-gray-700 mb-6">At WEDEX Healthcare, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a purchase from us.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Information We Collect</h3>
                    <div class="bg-gray-50 p-6 rounded-lg mb-4">
                        <h4 class="font-semibold text-lg mb-3">Personal Information</h4>
                        <ul class="list-disc list-inside text-gray-700">
                            <li>Name and contact details (email address, phone number, shipping address)</li>
                            <li>Payment information (processed securely through our payment partners)</li>
                            <li>Order history and preferences</li>
                            <li>Account credentials (for registered users)</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-semibold text-lg mb-3">Automatically Collected Information</h4>
                        <ul class="list-disc list-inside text-gray-700">
                            <li>IP address and browser type</li>
                            <li>Device information and operating system</li>
                            <li>Website usage data and browsing patterns</li>
                            <li>Cookies and similar tracking technologies</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">How We Use Your Information</h3>
                    <p class="text-gray-700 mb-4">We use the information we collect for various purposes, including:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Processing and fulfilling your orders</li>
                        <li>Providing customer support and service</li>
                        <li>Sending order confirmations and shipping updates</li>
                        <li>Personalizing your shopping experience</li>
                        <li>Improving our website and services</li>
                        <li>Sending marketing communications (with your consent)</li>
                        <li>Preventing fraud and ensuring security</li>
                        <li>Complying with legal obligations</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Information Sharing and Disclosure</h3>
                    <p class="text-gray-700 mb-4">We do not sell or rent your personal information to third parties. We may share your information with:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li><strong>Service Providers:</strong> Payment processors, shipping carriers, and IT service providers</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                        <li><strong>Business Transfers:</strong> In connection with a merger or acquisition</li>
                        <li><strong>With Your Consent:</strong> When you explicitly agree to share your information</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Data Security</h3>
                    <p class="text-gray-700 mb-4">We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    <p class="text-gray-700">All payment transactions are encrypted using SSL technology and processed through secure payment gateways.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Cookies and Tracking Technologies</h3>
                    <p class="text-gray-700 mb-4">We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and understand where our visitors come from.</p>
                    <p class="text-gray-700">You can control cookies through your browser settings. However, disabling cookies may affect your ability to use certain features of our website.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Your Rights</h3>
                    <p class="text-gray-700 mb-4">Depending on your location, you may have the following rights regarding your personal information:</p>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Right to access and receive a copy of your personal data</li>
                        <li>Right to correct inaccurate or incomplete information</li>
                        <li>Right to delete your personal data</li>
                        <li>Right to restrict or object to processing</li>
                        <li>Right to data portability</li>
                        <li>Right to withdraw consent</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Data Retention</h3>
                    <p class="text-gray-700 mb-4">We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Third-Party Links</h3>
                    <p class="text-gray-700 mb-4">Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these external sites. We encourage you to review their privacy policies.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Children's Privacy</h3>
                    <p class="text-gray-700 mb-4">Our services are not directed to individuals under the age of 18. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Changes to This Policy</h3>
                    <p class="text-gray-700 mb-4">We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>
                </div>

                <div class="policy-section">
                    <h3 class="text-2xl font-semibold">Contact Us</h3>
                    <p class="text-gray-700">If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                    <ul class="text-gray-700 mt-2">
                        <li><strong>Email:</strong> wedexhealthcareservices@gmail.com</li>
                        <li><strong>Phone:</strong> +234 803 516 1651</li>
                        <li><strong>Address:</strong> 21 Enerhen Road, Effurun, Delta State, Warri,Nigeria</li>
                    </ul>
                </div>

                <div class="mt-8 p-6 bg-teal-50 rounded-lg">
                    <p class="text-teal-800"><strong>Note:</strong> By using our website and services, you acknowledge that you have read and understood this Privacy Policy.</p>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>
</body>
</html>