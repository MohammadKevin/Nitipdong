<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BelanjaIn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased" style="font-family:'Inter',sans-serif;">
    <div class="min-h-screen flex">
        
        <!-- Left Side: Branding / Illustration (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#14213D] relative overflow-hidden items-center justify-center">
            <!-- Decorative circles -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#12A57F] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-[#F2A93B] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10 px-12 text-center">
                <div class="w-20 h-20 bg-[#12A57F] rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-8 shadow-lg shadow-[#12A57F]/30" style="font-family:'Poppins',sans-serif;">
                    BI
                </div>
                <h1 class="text-4xl font-bold text-white mb-4" style="font-family:'Poppins',sans-serif;">
                    BelanjaIn
                </h1>
                <p class="text-slate-300 text-lg max-w-md mx-auto leading-relaxed">
                    Platform e-commerce masa depan. Kelola tokomu dengan mudah, cepat, dan aman.
                </p>
            </div>
        </div>

        <!-- Right Side: Auth Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 bg-white relative">
            
            <!-- Mobile Logo (only visible on mobile) -->
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-[#12A57F] rounded-lg flex items-center justify-center text-white text-lg font-bold" style="font-family:'Poppins',sans-serif;">BI</div>
                <span class="text-2xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">BelanjaIn</span>
            </div>

            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
            
        </div>
    </div>
</body>
</html>
