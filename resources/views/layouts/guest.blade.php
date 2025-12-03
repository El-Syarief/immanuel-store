<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Immanuel Store') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        {{-- CSS KHUSUS LOGIN & DARK MODE FIX --}}
        <style>
            /* Background Pattern */
            .bg-dots-darker {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
            }
            
            /* Input Text/Password Dark Mode */
            .dark input[type="text"], 
            .dark input[type="email"], 
            .dark input[type="password"] {
                background-color: #1f2937 !important; /* gray-800 */
                border-color: #4b5563 !important;     /* gray-600 */
                color: #f3f4f6 !important;            /* gray-100 */
            }
            .dark input:focus {
                border-color: #6366f1 !important;
                box-shadow: 0 0 0 1px #6366f1 !important;
            }
            
            /* --- PERBAIKAN CHECKBOX --- */
            .dark input[type="checkbox"] {
                background-color: #1f2937 !important; /* Background gelap */
                border-color: #6b7280 !important;     /* Border abu terang */
                color: #6366f1 !important;            /* Centang ungu */
            }
            .dark input[type="checkbox"]:checked {
                background-color: #6366f1 !important; /* Saat dicentang jadi ungu */
                border-color: #6366f1 !important;
            }

            /* --- PERBAIKAN LABEL --- */
            .dark label, .dark .text-sm {
                color: #d1d5db !important; /* gray-300 (Putih redup) */
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        
        <div class="absolute top-4 right-4 z-50">
            <button 
                x-data="{ 
                    darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggle() {
                        this.darkMode = !this.darkMode;
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    }
                }"
                @click="toggle()"
                class="p-2 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition transform hover:scale-105">
                <!-- <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 24.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg> -->

                <!-- Icon Matahari -->
                <svg x-show="darkMode" class="w-6 h-6" viewBox="0 0 24 24" fill="url(#gradSun)">
                <defs>
                    <linearGradient id="gradSun" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#FFD93D;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#FF8C00;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <circle cx="12" cy="12" r="5"/>
                <g stroke="#FFB100" stroke-width="2">
                    <line x1="12" y1="2" x2="12" y2="5"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                    <line x1="2" y1="12" x2="5" y2="12"/>
                    <line x1="19" y1="12" x2="22" y2="12"/>
                    <line x1="4.5" y1="4.5" x2="6.5" y2="6.5"/>
                    <line x1="17.5" y1="17.5" x2="19.5" y2="19.5"/>
                    <line x1="17.5" y1="6.5" x2="19.5" y2="4.5"/>
                    <line x1="4.5" y1="19.5" x2="6.5" y2="17.5"/>
                </g>
                </svg>

                <!-- Icon Bulan -->
                <svg x-show="!darkMode" class="w-6 h-6" viewBox="0 0 24 24" fill="url(#gradMoon)">
                <defs>
                    <linearGradient id="gradMoon" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#A0C4FF;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#3A0CA3;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 0010 9.79z"/>
                </svg>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full z-0 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-500/20 blur-[100px]"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-500/20 blur-[100px]"></div>
                <div class="absolute inset-0 bg-dots-darker opacity-0 dark:opacity-100 transition-opacity duration-500"></div>
            </div>

            <div class="z-10 w-full sm:max-w-md flex flex-col items-center">
                <div class="mb-8 text-center animate-fade-in-down">
                    <a href="/" class="flex flex-col items-center gap-3 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl flex items-center justify-center text-2xl font-bold shadow-lg shadow-indigo-500/30 transform group-hover:scale-110 transition duration-300">
                            IS
                        </div>
                        <span class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight transition-colors duration-300">IMMANUEL STORE</span>
                    </a>
                </div>

                <div class="w-full px-8 py-10 bg-white dark:bg-gray-800/90 dark:backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100 dark:border-gray-700/50 transition-all duration-300 relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                    {{ $slot }}
                </div>
                
                <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500">
                    &copy; {{ date('Y') }} Internal System. All rights reserved.
                </div>
            </div>
        </div>
    </body>
</html>