<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Keuangan') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-green-500/20 text-sm flex items-center gap-2 transition transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Laporan
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 z-50 border border-gray-100 dark:border-gray-700" style="display: none;">
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 flex items-center gap-2 transition">
                        <span class="text-lg">📊</span> Download Excel
                    </a>
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 flex items-center gap-2 transition">
                        <span class="text-lg">📕</span> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FILTER CARD --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-8 border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Dari Tanggal</label>
                        <input type="date" name="date_start" value="{{ $startDate }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 transition shadow-sm">
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Sampai Tanggal</label>
                        <input type="date" name="date_end" value="{{ $endDate }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 transition shadow-sm">
                    </div>
                    <div class="flex gap-3 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-500/20 transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                        </button>
                        @if(request()->has('date_start'))
                            <a href="{{ route('reports.index') }}" class="flex-1 md:flex-none bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2.5 px-4 rounded-xl shadow-sm transition text-sm flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- KARTU STATISTIK UTAMA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                {{-- Card Omzet --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Omzet</p>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mb-1">$ {{ number_format($omzet, 2, '.', ',') }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded inline-block border border-blue-100 dark:border-blue-800">
                        Pemasukan Kotor
                    </p>
                </div>

                {{-- Card Laba Bersih --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-16 h-16 {{ $profit >= 0 ? 'text-green-500' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Laba Bersih (Profit)</p>
                    <p class="text-3xl font-extrabold {{ $profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mb-1">
                        $ {{ number_format($profit, 2, '.', ',') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/50 px-2 py-1 rounded inline-block border border-gray-200 dark:border-gray-600">
                        Omzet - Modal (HPP)
                    </p>
                </div>

                {{-- Card Valuasi Aset --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Valuasi Aset Gudang</p>
                    <p class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mb-1">$ {{ number_format($assetValue, 2, '.', ',') }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-300 font-medium bg-purple-50 dark:bg-purple-900/20 px-2 py-1 rounded inline-block border border-purple-100 dark:border-purple-800">
                        Posisi per: {{ \Carbon\Carbon::parse($valuationDate)->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- PERFORMA BARANG --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="bg-indigo-100 dark:bg-indigo-900/50 p-1.5 rounded-lg text-indigo-600 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </span>
                        Performa Barang
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Barang Terjual (Out)</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">{{ number_format($totalSold) }} Unit</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Barang Dibeli (In)</span>
                            <span class="font-bold text-green-600 dark:text-green-400 bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">{{ number_format($totalPurchased) }} Unit</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-100 dark:border-purple-900/30 mt-2">
                            <span class="text-purple-800 dark:text-purple-300 text-sm font-bold">Stok Tersisa (Gudang/Akun)</span>
                            <span class="font-bold text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-purple-200 dark:border-purple-800 shadow-sm">{{ number_format($totalStockRemaining) }} Unit</span>
                        </div>
                    </div>
                </div>
                
                {{-- STATISTIK TRANSAKSI --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="bg-yellow-100 dark:bg-yellow-900/50 p-1.5 rounded-lg text-yellow-600 dark:text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </span>
                        Statistik Transaksi
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Jumlah Transaksi</span>
                            <span class="font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">{{ $trxCount }} Kali</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Rata-rata Omzet</span>
                            <span class="font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                $ {{ $trxCount > 0 ? number_format($omzet / $trxCount, 2, '.', ',') : 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>