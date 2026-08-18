<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BelanjaIn') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">
</head>
<body class="font-sans text-gray-900 antialiased" style="font-family:'Inter',sans-serif;">
    <div class="min-h-screen flex">
        
        <div class="hidden lg:flex lg:w-1/2 bg-[#14213D] relative overflow-hidden items-center justify-center">
            
            <a href="/" class="absolute top-8 left-8 z-50 flex items-center gap-2 text-white/70 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#12A57F] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-[#F2A93B] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10 px-12 text-center">
                <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-20 h-20 rounded-2xl mx-auto mb-8 shadow-lg shadow-[#12A57F]/30 object-cover">
                <h1 class="text-4xl font-bold text-white mb-4" style="font-family:'Poppins',sans-serif;">
                    BelanjaIn
                </h1>
                <p class="text-slate-300 text-lg max-w-md mx-auto leading-relaxed">
                    Platform e-commerce masa depan. Kelola tokomu dengan mudah, cepat, dan aman.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 bg-white relative">
            
            <a href="/" class="lg:hidden absolute top-6 left-6 sm:top-8 sm:left-8 z-50 flex items-center gap-2 text-slate-500 hover:text-[#12A57F] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <img src="{{ asset('img/icon.jpg') }}" alt="BelanjaIn Logo" class="w-10 h-10 rounded-lg shadow-md object-cover">
                <span class="text-2xl font-bold text-[#14213D]" style="font-family:'Poppins',sans-serif;">BelanjaIn</span>
            </div>

            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
            
        </div>
    </div>
</body>
</html>
