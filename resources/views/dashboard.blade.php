<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- WELCOME CARD --}}
            <div class="relative bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl mb-8 transition-colors duration-300 border border-gray-100 dark:border-gray-700">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-indigo-500/10 dark:bg-indigo-500/20 blur-3xl"></div>
                
                <div class="p-8 text-gray-900 dark:text-gray-100 relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">Selamat Datang, <span class="text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name }}!</span> 👋</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Anda login sebagai <span class="font-bold uppercase bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded text-xs tracking-wider">{{ Auth::user()->role }}</span>.
                                Berikut adalah ringkasan aktivitas toko hari ini.
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <span class="text-4xl">📊</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATISTIK CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Card Omzet --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-green-500 rounded-r-full"></div>
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Omzet Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2 bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300">
                        $ {{ number_format($todayRevenue, 2, '.', ',') }}
                    </p>
                </div>

                {{-- Card Transaksi --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-blue-500 rounded-r-full"></div>
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full group-hover:scale-110 transition-transform"></div>

                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaksi Berhasil</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $todayTransactions }} <span class="text-sm font-normal text-gray-400">Kali</span>
                    </p>
                </div>

                {{-- Card Item Terjual --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-purple-500 rounded-r-full"></div>
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full group-hover:scale-110 transition-transform"></div>

                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Terjual</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $todayItemsSold }} <span class="text-sm font-normal text-gray-400">Pcs</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- STOK MENIPIS --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Stok Menipis (< 5)
                    </h3>

                    @if($lowStockItems->count() > 0)
                        <ul class="space-y-3">
                            @foreach($lowStockItems as $item)
                                <li class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg flex justify-between items-center border border-gray-100 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-900/50 transition">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono">{{ $item->code }}</p>
                                    </div>
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-bold px-3 py-1 rounded-full border border-red-200 dark:border-red-800">
                                        Sisa: {{ $item->stock }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-6 text-center">
                            <a href="{{ route('items.index') }}" class="text-indigo-600 dark:text-indigo-400 text-sm font-bold hover:text-indigo-800 dark:hover:text-indigo-300 transition flex items-center justify-center gap-1">
                                Lihat Semua Barang 
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-gray-800 dark:text-gray-200 font-medium">Stok Aman!</p>
                            <p class="text-gray-500 dark:text-gray-500 text-sm">Tidak ada barang yang stoknya kritis.</p>
                        </div>
                    @endif
                </div>

                {{-- AKSI CEPAT --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('transactions.create') }}" class="group block p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition">
                            <div class="w-10 h-10 bg-indigo-200 dark:bg-indigo-800 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <span class="text-xl">🛒</span>
                            </div>
                            <p class="font-bold text-indigo-700 dark:text-indigo-300">Kasir Baru</p>
                            <p class="text-xs text-indigo-500/70 dark:text-indigo-400/50 mt-1">Buat Transaksi</p>
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('items.create') }}" class="group block p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                                <div class="w-10 h-10 bg-green-200 dark:bg-green-800 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <span class="text-xl">📦</span>
                                </div>
                                <p class="font-bold text-green-700 dark:text-green-300">Tambah Barang</p>
                                <p class="text-xs text-green-500/70 dark:text-green-400/50 mt-1">Input Stok Awal</p>
                            </a>
                            <a href="{{ route('reports.index') }}" class="group block p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 rounded-xl hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition">
                                <div class="w-10 h-10 bg-yellow-200 dark:bg-yellow-800 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <span class="text-xl">📊</span>
                                </div>
                                <p class="font-bold text-yellow-700 dark:text-yellow-300">Laporan</p>
                                <p class="text-xs text-yellow-500/70 dark:text-yellow-400/50 mt-1">Cek Keuangan</p>
                            </a>
                            <a href="{{ route('users.create') }}" class="group block p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                                <div class="w-10 h-10 bg-purple-200 dark:bg-purple-800 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <span class="text-xl">👥</span>
                                </div>
                                <p class="font-bold text-purple-700 dark:text-purple-300">Tambah User</p>
                                <p class="text-xs text-purple-500/70 dark:text-purple-400/50 mt-1">Kelola Akses</p>
                            </a>
                        @else
                            <a href="{{ route('transactions.index') }}" class="group block p-4 bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 transition">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <span class="text-xl">📄</span>
                                </div>
                                <p class="font-bold text-gray-700 dark:text-gray-200">Riwayat</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cek Transaksi</p>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>