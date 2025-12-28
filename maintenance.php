<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Construction</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            overflow: hidden;
        }
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        @keyframes float-reverse {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(20px) rotate(-5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .float-1 { animation: float 6s ease-in-out infinite; }
        .float-2 { animation: float-reverse 8s ease-in-out infinite; }
        .float-3 { animation: float 7s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 relative">
    <div class="absolute inset-0 z-0 opacity-10">
        <i data-lucide="stethoscope" class="absolute top-[10%] left-[15%] w-24 h-24 text-blue-500 float-1"></i>
        <i data-lucide="microscope" class="absolute top-[20%] right-[10%] w-32 h-32 text-blue-500 float-2"></i>
        <i data-lucide="heart-pulse" class="absolute bottom-[15%] left-[20%] w-28 h-28 text-blue-500 float-3"></i>
        <i data-lucide="syringe" class="absolute bottom-[25%] right-[25%] w-20 h-20 text-blue-500 float-1"></i>
        <i data-lucide="pill" class="absolute top-[60%] left-[5%] w-16 h-16 text-blue-500 float-2"></i>
        <i data-lucide="activity" class="absolute top-[50%] right-[30%] w-20 h-20 text-blue-500 float-3"></i>
    </div>

    <div class="container mx-auto px-4 flex flex-col items-center justify-center min-h-screen text-center relative z-10">
        <!-- The image path now starts with a '/' to reference it from the root directory. -->
        <!--<img src="images/Blue_Logo.png" alt="WEDEX Logo" class="mb-4 h-32 w-auto">-->
        <img src="https://i.ibb.co/Kc3KDxCN/Blue-Logo.png" alt="Blue-Logo" border="0"  class="mb-4 h-32 w-auto">
        
        <h1 class="text-4xl md:text-5xl font-extrabold text-blue-800 mt-6">Welcome to WEDEX Healthcare Services</h1>
        <h2 class="text-2xl md:text-3xl font-bold mt-4">Our Website is Under Construction</h2>
        <p class="text-gray-600 mt-2 max-w-lg">
            We're working hard to bring you an amazing new experience. We'll be back online soon. Thank you for your patience!
        </p>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <?php
// // This script will display the IP address that the server sees.
// echo "The server sees your IP address as: ";
// echo $_SERVER['REMOTE_ADDR'];
// ?>
</body>
</html>
