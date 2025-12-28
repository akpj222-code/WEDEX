<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - WEDEX Healthcare Services</title>
    <link href="css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
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
                <span class="text-gray-800 font-medium">FAQ</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12 md:py-20">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Frequently Asked Questions</h1>
                <p class="text-lg text-gray-600">Find answers to common questions about our products, shipping, and services.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
                <div class="space-y-6">
                    <!-- FAQ Item 1 -->
                    <div class="faq-item border-b pb-4">
                        <button class="faq-question w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800 focus:outline-none">
                            <span>What are your shipping options?</span>
                            <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="faq-answer mt-2 text-gray-600 leading-relaxed">
                            <p>We offer standard and express shipping options within Nigeria. Standard shipping typically takes 3-5 business days, while express shipping takes 1-2 business days. Shipping costs are calculated at checkout based on your location and the weight of your order.</p>
                        </div>
                    </div>
                    <!-- FAQ Item 2 -->
                    <div class="faq-item border-b pb-4">
                        <button class="faq-question w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800 focus:outline-none">
                            <span>How can I track my order?</span>
                             <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="faq-answer mt-2 text-gray-600 leading-relaxed">
                            <p>Once your order has been shipped, you will receive an email with a tracking number and a link to the courier's website. You can use this number to monitor the progress of your delivery.</p>
                        </div>
                    </div>
                    <!-- FAQ Item 3 -->
                    <div class="faq-item border-b pb-4">
                        <button class="faq-question w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800 focus:outline-none">
                            <span>What is your return policy?</span>
                             <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="faq-answer mt-2 text-gray-600 leading-relaxed">
                            <p>We offer a 14-day return policy for most items, provided they are unused, in their original packaging, and in the same condition that you received them. Please visit our returns page or contact our support team to initiate a return.</p>
                        </div>
                    </div>
                    <!-- FAQ Item 4 -->
                    <div class="faq-item border-b pb-4">
                        <button class="faq-question w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800 focus:outline-none">
                            <span>Are your products certified?</span>
                             <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="faq-answer mt-2 text-gray-600 leading-relaxed">
                            <p>Yes, absolutely. All our medical equipment and supplies are sourced from reputable, certified manufacturers and meet rigorous quality and safety standards. We are committed to providing only authentic and reliable products to our customers.</p>
                        </div>
                    </div>
                    <!-- FAQ Item 5 -->
                     <div class="faq-item">
                        <button class="faq-question w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800 focus:outline-none">
                            <span>What payment methods do you accept?</span>
                             <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="faq-answer mt-2 text-gray-600 leading-relaxed">
                            <p>We accept a variety of payment methods for your convenience, including all major credit and debit cards (Visa, Mastercard), bank transfers, and payments via Flutterwave. All transactions are secure and encrypted.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'Static/footer.php'; ?>

    <script>
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                const icon = button.querySelector('svg');

                if (answer.style.maxHeight) {
                    answer.style.maxHeight = null;
                    icon.classList.remove('rotate-180');
                } else {
                    // Close all other answers first
                    document.querySelectorAll('.faq-answer').forEach(ans => {
                        ans.style.maxHeight = null;
                        ans.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
                    });
                    // Open the clicked answer
                    answer.style.maxHeight = answer.scrollHeight + "px";
                    icon.classList.add('rotate-180');
                } 
            });
        });
    </script>
</body>
</html>

