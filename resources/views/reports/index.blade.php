<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Keuangan') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Laporan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100" style="display: none;">
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600 flex items-center gap-2">📄 Download Excel</a>
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 flex items-center gap-2">📕 Download PDF</a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Dari Tanggal</label>
                        <input type="date" name="date_start" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Sampai Tanggal</label>
                        <input type="date" name="date_end" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                            Terapkan Filter
                        </button>
                        @if(request()->has('date_start'))
                            <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg shadow transition text-sm">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Omzet</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$ {{ number_format($omzet, 2, '.', ',') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Pemasukan Kotor</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 {{ $profit >= 0 ? 'border-green-500' : 'border-red-500' }}">
                    <p class="text-xs font-bold text-gray-500 uppercase">Laba Bersih (Profit)</p>
                    <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                        $ {{ number_format($profit, 2, '.', ',') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Omzet - Modal (HPP)</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Valuasi Aset Gudang</p>
                    <p class="text-2xl font-bold text-purple-700 mt-2">$ {{ number_format($assetValue, 2, '.', ',') }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Posisi per: {{ \Carbon\Carbon::parse($valuationDate)->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">📦 Performa Barang</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Barang Terjual (Out)</span>
                            <span class="font-bold text-indigo-600">{{ number_format($totalSold) }} Pcs</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Barang Dibeli (In)</span>
                            <span class="font-bold text-green-600">{{ number_format($totalPurchased) }} Pcs</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">📊 Statistik Transaksi</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Transaksi</span>
                            <span class="font-bold text-gray-900">{{ $trxCount }} Kali</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rata-rata Omzet</span>
                            <span class="font-bold text-gray-900">
                                $ {{ $trxCount > 0 ? number_format($omzet / $trxCount, 2, '.', ',') : 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>