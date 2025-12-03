<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Immanuel Store - Internal System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Cek preferensi user saat load
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        /* Animasi Background Bergerak Halus */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4b5563; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-[#0B0F19] dark:text-gray-100 transition-colors duration-500 overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    {{-- TOMBOL TOGGLE THEME (Floating) --}}
    <div class="fixed top-6 right-6 z-50">
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
            class="group p-2.5 rounded-full bg-white/80 dark:bg-gray-800/50 backdrop-blur-md shadow-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-all duration-300 hover:scale-110 hover:shadow-indigo-500/20"
            title="Ubah Tema">
            <!-- <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 24.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg> -->

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

    {{-- BACKGROUND ELEMENTS --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full mix-blend-multiply filter blur-3xl opacity-0 dark:opacity-100 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full mix-blend-multiply filter blur-3xl opacity-0 dark:opacity-100 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-600/20 rounded-full mix-blend-multiply filter blur-3xl opacity-0 dark:opacity-100 animate-blob animation-delay-4000"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative z-10">
        
        <div class="w-full max-w-5xl bg-white dark:bg-[#111827]/80 dark:backdrop-blur-xl shadow-2xl rounded-3xl overflow-hidden flex flex-col md:flex-row transition-all duration-500 border border-gray-100 dark:border-gray-700/50">
            
            {{-- BAGIAN KIRI: BRANDING & INFO --}}
            <div class="md:w-5/12 bg-gradient-to-br from-indigo-600 to-violet-700 dark:from-indigo-900 dark:to-[#0f0c29] p-10 text-white flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-2xl group-hover:scale-110 transition duration-700"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-black/20 blur-2xl group-hover:scale-110 transition duration-700"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center shadow-inner border border-white/10">
                            <span class="text-2xl font-bold">IS</span>
                        </div>
                        <span class="text-sm font-semibold tracking-widest uppercase opacity-70">Internal System v1.0</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                        Immanuel <br> <span class="text-indigo-200 dark:text-indigo-400">Store.</span>
                    </h1>
                    <p class="text-indigo-100 dark:text-gray-400 text-sm leading-relaxed mb-8 opacity-90">
                        Platform manajemen terintegrasi untuk pengelolaan stok, transaksi, dan pelaporan keuangan yang efisien dan akurat.
                    </p>
                </div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition duration-300">
                        <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Real-time Analytics</h4>
                            <p class="text-xs text-indigo-200 opacity-70">Pantau performa toko detik ini juga.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition duration-300">
                        <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Secure Access</h4>
                            <p class="text-xs text-indigo-200 opacity-70">Keamanan data prioritas utama.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-xs text-indigo-200 dark:text-gray-500 opacity-60">
                    &copy; {{ date('Y') }} Immanuel Store. Protected.
                </div>
            </div>

            {{-- BAGIAN KANAN: LOGIN AREA --}}
            <div class="md:w-7/12 p-10 md:p-14 flex flex-col justify-center bg-white dark:bg-gray-800 transition-colors duration-300">
                <div class="max-w-md mx-auto w-full">
                    <div class="mb-10">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Selamat Datang!</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Masukkan kredensial Anda untuk mengakses dashboard.</p>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <div class="text-center">
                                <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-700/50 rounded-2xl p-6 mb-6">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6366f1&color=fff" alt="Avatar" class="w-16 h-16 rounded-full mx-auto mb-3 border-4 border-white dark:border-gray-700 shadow-md">
                                    <p class="text-gray-600 dark:text-gray-300 text-sm">Anda sedang login sebagai</p>
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ Auth::user()->name }}</h4>
                                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 uppercase tracking-wide">
                                        {{ Auth::user()->role }}
                                    </span>
                                </div>
                                
                                <a href="{{ url('/dashboard') }}" class="w-full inline-flex justify-center items-center px-6 py-4 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 shadow-lg shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1">
                                    Lanjutkan ke Dashboard &rarr;
                                </a>
                            </div>
                        @else
                            {{-- TOMBOL LOGIN BESAR --}}
                            <a href="{{ route('login') }}" class="group w-full flex items-center justify-between px-6 py-5 border border-gray-200 dark:border-gray-700 rounded-2xl hover:border-indigo-500 dark:hover:border-indigo-500 bg-white dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-gray-700/50 transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    </div>
                                    <div class="text-left">
                                        <h4 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Login Akun</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Masuk menggunakan Username</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>

                            <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700/50 text-center">
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    Mengalami kendala saat login? <br>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">Hubungi Super Admin</span>
                                </p>
                            </div>
                        @endauth
                    @endif {{-- <--- PENUTUP YANG HILANG SUDAH DITAMBAHKAN --}}
                </div>
            </div>

        </div>
    </div>

</body>
</html>