<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BelanjaIn') }}</title>

        <!-- Google Fonts: Outfit for headings, Inter for body -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('img/icon.jpg') }}">

        <style>
            :root {
                --brand: #06b6d4;       /* cyan-500  — warna utama BelanjaIn */
                --brand-dark: #0891b2;  /* cyan-600 */
                --brand-light: #ecfeff; /* cyan-50 */
                --surface: #ffffff;
                --bg: #f0fdfa;          /* teal-50  */
                --text: #0f172a;        /* slate-900 */
                --muted: #64748b;       /* slate-500 */
            }
            body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
            h1, h2, h3, h4, h5 { font-family: 'Outfit', sans-serif; }
            [x-cloak] { display: none !important; }

            /* Custom scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
            ::-webkit-scrollbar-thumb:hover { background: var(--brand); }

            /* Smooth transitions */
            a, button { transition: all 0.15s ease; }

            .btn-primary {
                background: var(--brand);
                color: white;
                font-family: 'Outfit', sans-serif;
                font-weight: 600;
                border-radius: 10px;
                padding: 10px 24px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-primary:hover { background: var(--brand-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,165,233,0.3); }

            .btn-outline {
                border: 1.5px solid var(--brand);
                color: var(--brand);
                font-family: 'Outfit', sans-serif;
                font-weight: 600;
                border-radius: 10px;
                padding: 10px 24px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-outline:hover { background: var(--brand-light); }

            .card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.06);
            }
            .card-hover:hover {
                box-shadow: 0 4px 16px rgba(14,165,233,0.12), 0 1px 4px rgba(0,0,0,0.06);
                transform: translateY(-1px);
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Chat Widget Component -->
        <x-chat-widget />
    </body>
</html>
