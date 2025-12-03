<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Immanuel Store') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        {{-- CSS GLOBAL FIXES FOR DARK MODE --}}
        <style>
            /* 1. FIX ICON KALENDER (DATE PICKER) 
               Membalik warna icon dari hitam -> putih */
            .dark ::-webkit-calendar-picker-indicator {
                filter: invert(1) !important;
                opacity: 0.8;
                cursor: pointer;
            }

            /* 2. FIX ICON MATA PASSWORD (EDGE / CHROMIUM) 
               Khusus untuk browser yang menampilkan icon mata bawaan (seperti Edge) */
            .dark input[type="password"]::-ms-reveal,
            .dark input[type="password"]::-ms-clear {
                filter: invert(1) !important;
            }

            /* 3. FIX SCROLLBAR (Opsional - Agar scrollbar tidak putih mencolok) */
            .dark ::-webkit-scrollbar {
                width: 10px;
            }
            .dark ::-webkit-scrollbar-track {
                background: #1f2937; 
            }
            .dark ::-webkit-scrollbar-thumb {
                background: #4b5563; 
                border-radius: 5px;
            }
            .dark ::-webkit-scrollbar-thumb:hover {
                background: #6b7280; 
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>

            {{-- 
                    LOGIKA ALERT STOK GLOBAL (REAL-TIME CHECK) 
                    Cek database langsung: Apakah ada barang sisa <= 1?
                    Hanya tampil untuk ADMIN.
                --}}
                @php
                    $globalLowStockItems = collect([]);
                    if(auth()->check() && auth()->user()->role === 'admin') {
                        $globalLowStockItems = \App\Models\Item::where('stock',  0)->get();
                    }
                @endphp

                {{-- JIKA ADA STOK MENIPIS -> TAMPILKAN SPANDUK MERAH --}}
                @if($globalLowStockItems->count() > 0)
                    <div class="bg-red-600 dark:bg-red-900/80 text-white px-4 py-4 shadow-lg relative z-40 animate-pulse">
                        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex items-start gap-3">
                                <span class="text-3xl">⚠️</span>
                                <div>
                                    <p class="font-bold text-lg uppercase tracking-wide">PERHATIAN: Stok barang berikut telah HABIS (0)! Segera restock.</p>
                                    
                                    {{-- DAFTAR BARANG (List) --}}
                                    <div class="mt-2 bg-white/10 p-3 rounded-lg border border-white/20">
                                        <ul class="list-disc list-inside text-sm space-y-1 font-mono">
                                            @foreach($globalLowStockItems as $item)
                                                <li>
                                                    <span class="font-bold">{{ $item->name }}</span> 
                                                    ({{ $item->code }}) 
                                                    - Sisa: <span class="font-bold bg-white text-red-600 px-1 rounded">{{ $item->stock }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Tombol Aksi Cepat --}}
                            <div class="flex-shrink-0">
                                {{-- Filter link ke stok 0 --}}
                                <a href="{{ route('items.index', ['sort_by' => 'stock-asc']) }}" class="block w-full bg-white text-red-600 dark:bg-gray-800 dark:text-red-400 font-bold px-6 py-3 rounded-xl shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-center transform hover:scale-105">
                                    Lihat Detail Barang &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                @if(auth()->user()->role === 'admin' && empty(auth()->user()->email))
                    <div class="bg-red-600 text-white px-4 py-3 shadow-md relative z-50">
                        <div class="max-w-7xl mx-auto flex justify-between items-center sm:px-6 lg:px-8">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="font-bold">PERHATIAN:</span>
                                <span class="text-sm">Anda belum mengatur Email Pengingat Stok. Notifikasi stok habis tidak dapat dikirim.</span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="bg-white text-red-600 text-sm font-bold px-3 py-1.5 rounded hover:bg-red-50 transition">
                                Atur Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        {{-- Gunakan asset() untuk memanggil file dari folder public --}}
        <audio id="stockAlertSound" src="{{ asset('sounds/Alert_Stock.mp3') }}" preload="auto"></audio>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Ambil status stok kritis dari PHP ke JS (True/False)
                // Kita cek apakah user adalah admin DAN apakah ada barang stok 0
                const hasCriticalStock = @json(auth()->check() && auth()->user()->role === 'admin' && isset($globalLowStockItems) && $globalLowStockItems->count() > 0);

                if (hasCriticalStock) {
                    // 2. Cek di Memory Browser (Session Storage)
                    // Apakah kunci 'stock_alert_played' sudah ada?
                    if (!sessionStorage.getItem('stock_alert_played')) {
                        
                        // Jika belum ada, mainkan suara
                        const audio = document.getElementById("stockAlertSound");
                        if(audio) {
                            var playPromise = audio.play();
                            if (playPromise !== undefined) {
                                playPromise.then(_ => {
                                    console.log("Audio played (First time)");
                                    
                                    // 3. SET FLAG: Tandai bahwa audio sudah diputar
                                    sessionStorage.setItem('stock_alert_played', 'true');
                                })
                                .catch(error => { 
                                    console.log("Audio autoplay blocked:", error); 
                                });
                            }
                        }
                    } else {
                        console.log("Audio muted (Already played in this session)");
                    }
                } else {
                    // 4. RESET: Jika stok sudah aman (tidak ada yang 0), 
                    // Hapus tanda agar nanti kalau ada barang habis lagi, alarm bisa bunyi ulang.
                    sessionStorage.removeItem('stock_alert_played');
                }
            });
        </script>
    </body>
</html>
