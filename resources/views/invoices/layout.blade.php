<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoicify — Professional Invoice Maker')</title>
    
    <!-- Fonts (Google Roboto for Material UI Design) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Roboto"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'mui-1': '0px 2px 1px -1px rgba(0,0,0,0.2), 0px 1px 1px 0px rgba(0,0,0,0.14), 0px 1px 3px 0px rgba(0,0,0,0.12)',
                        'mui-2': '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)',
                        'mui-8': '0px 5px 5px -3px rgba(0,0,0,0.2), 0px 8px 10px 1px rgba(0,0,0,0.14), 0px 3px 14px 2px rgba(0,0,0,0.12)',
                        'mui-24': '0px 11px 15px -7px rgba(0,0,0,0.2), 0px 24px 38px 3px rgba(0,0,0,0.14), 0px 9px 46px 8px rgba(0,0,0,0.12)'
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Custom print stylesheet */
        @media print {
            html, body {
                background: white !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            /* Reset body viewport layout wrapper spacing and backgrounds */
            main, .min-h-\[calc\(100vh-4rem\)\] {
                min-height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                display: block !important;
            }
            .print-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                background: white !important;
                overflow: visible !important;
                height: auto !important;
                aspect-ratio: auto !important;
            }
            @page {
                size: A4;
                margin: 1.2cm;
            }
            /* Lock print layout for guest users */
            body.guest-no-print main,
            body.guest-no-print .print-card {
                display: none !important;
            }
            body.guest-no-print .guest-print-warning {
                display: block !important;
                background: white !important;
                color: #000000 !important;
            }
        }

        /* Premium shine hover effects */
        .shine-button {
            position: relative;
            overflow: hidden;
        }
        .shine-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.35) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: skewX(-25deg);
            transition: left 0.65s ease-in-out;
        }
        .shine-button:hover::after {
            left: 150%;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-zinc-950 text-slate-900 dark:text-zinc-100 font-sans min-h-screen flex flex-col antialiased selection:bg-indigo-500/10 selection:text-indigo-500 @guest guest-no-print @endguest">
    
    <!-- Ambient background glows -->
    <div class="absolute inset-0 -z-10 overflow-hidden no-print">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 blur-3xl rounded-full"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 blur-3xl rounded-full"></div>
    </div>

    <!-- Top Navigation Bar (Material Design Inspired) -->
    <header class="no-print border-b border-slate-200/60 dark:border-zinc-800/60 bg-white/95 dark:bg-zinc-900/95 sticky top-0 z-40 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" class="flex items-center gap-2 group">
                    <div class="h-9 w-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-mui-2 group-hover:scale-105 transition-transform duration-200 shrink-0">
                        <i class="fa-solid fa-file-invoice text-base"></i>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 dark:text-zinc-50 tracking-tight text-base leading-none block">Invoicify</span>
                        <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 tracking-wider uppercase block mt-0.5">Invoice Maker</span>
                    </div>
                </a>
            </div>
            
            <nav class="flex items-center gap-1 sm:gap-2">
                @auth
                    <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-slate-650 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3 py-2 rounded-md hover:bg-slate-100/50 dark:hover:bg-zinc-850/50 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-list text-[11px]"></i> Dashboard
                    </a>
                    <a href="{{ route('till.index') }}" class="text-xs font-bold text-slate-655 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3 py-2 rounded-md hover:bg-slate-100/50 dark:hover:bg-zinc-850/50 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-cash-register text-[11px]"></i> POS Till
                    </a>
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-655 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3 py-2 rounded-md hover:bg-slate-100/50 dark:hover:bg-zinc-850/50 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-box text-[11px]"></i> Products
                    </a>
                    <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-slate-655 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3 py-2 rounded-md hover:bg-slate-100/50 dark:hover:bg-zinc-850/50 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-gear text-[11px]"></i> Settings
                    </a>
                    
                    <span class="h-4 w-px bg-slate-200 dark:bg-zinc-855 mx-1"></span>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-slate-500 hover:text-rose-650 dark:text-zinc-400 dark:hover:text-rose-400 px-3 py-2 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-all cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i> Log out
                        </button>
                    </form>
                    
                    <!-- User avatar bubble -->
                    <div class="h-8 w-8 rounded-full bg-indigo-600 border border-indigo-700 text-white flex items-center justify-center font-bold text-xs shrink-0 select-none shadow-sm uppercase ml-1" title="{{ auth()->user()->name }}">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                @else
                    <a href="{{ route('till.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3 py-2 rounded-md transition-all flex items-center gap-1.5 mr-2">
                        <i class="fa-solid fa-cash-register text-[11px]"></i> POS Till
                    </a>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400 px-3.5 py-2 rounded-md transition-all">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="shine-button text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-all shadow-sm hover:shadow-md">
                        Create Account
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @if (session('success'))
            <div class="no-print max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250/30 dark:border-emerald-900/30 rounded-lg p-3 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-check text-base"></i>
                        </div>
                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="no-print border-t border-slate-200/60 dark:border-zinc-800/60 bg-white dark:bg-zinc-900/40 py-5 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500 dark:text-zinc-500">
                &copy; {{ date('Y') }} Invoicify. All rights reserved. Powered by Roboto & Three.js.
            </p>
            <div class="flex items-center gap-4 text-xs text-slate-400 dark:text-zinc-500">
                <a href="{{ route('privacy') }}" class="hover:text-slate-650 dark:hover:text-zinc-400 transition-colors">Privacy Policy</a>
                <span>&bull;</span>
                <a href="{{ route('terms') }}" class="hover:text-slate-650 dark:hover:text-zinc-400 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
