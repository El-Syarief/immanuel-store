<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Transaksi') }}
            </h2>
            
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'admin')
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm flex items-center gap-2 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Data
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100" style="display: none;">
                            <a href="{{ route('transactions.export.excel', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600 flex items-center gap-2">
                                📄 Download Excel
                            </a>
                            <a href="{{ route('transactions.export.pdf', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 flex items-center gap-2">
                                📕 Download PDF
                            </a>
                        </div>
                    </div>
                @endif

                <a href="{{ route('transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm transition">
                    + Transaksi Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('transactions.index') }}">
                    
                    <div class="flex flex-col md:flex-row gap-4 mb-4 justify-between">
                        <div class="w-full md:w-1/3">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Cari Invoice</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: INV-OUT..." class="w-full pl-10 border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="w-full md:w-1/4">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Urutkan</label>
                            <select name="sort" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>📅 Terbaru - Terlama</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>📅 Terlama - Terbaru</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-4">

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                        
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Tipe</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>📤 Penjualan</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>📥 Pembelian</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Market</label>
                            <select name="market" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua Market</option>
                                @foreach($markets as $m)
                                    <option value="{{ $m }}" {{ request('market') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Bulan</label>
                            <select name="month" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Tahun</label>
                            <select name="year" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Dari Tgl</label>
                            <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full border-gray-300 rounded-lg text-sm px-2">
                        </div>

                        <div class="relative">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Sampai Tgl</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full border-gray-300 rounded-lg text-sm px-2" onchange="this.form.submit()">
                        </div>
                    </div>

                    @if(request()->hasAny(['search', 'type', 'market', 'user_id', 'month', 'year', 'date_start', 'date_end']))
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('transactions.index') }}" class="text-red-600 hover:text-red-800 text-sm font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                Hapus Semua Filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Market</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-indigo-600">
                                        <a href="{{ route('transactions.show', $trx->id) }}" class="hover:underline">{{ $trx->invoice_code }}</a>
                                        @if($trx->description)
                                            <span class="ml-2 text-gray-400" title="{{ $trx->description }}">📝</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($trx->type == 'out')
                                            <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-0.5 rounded">OUT</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded">IN</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $trx->transaction_date->format('d M Y') }}<br>
                                        <span class="text-xs text-gray-400">{{ $trx->user->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($trx->market)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $trx->market }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('transactions.show', $trx->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded hover:bg-blue-100" title="Detail">
                                                📄
                                            </a>
                                            
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('transactions.edit', $trx->id) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 p-2 rounded hover:bg-yellow-100" title="Edit Data">
                                                    ✏️
                                                </a>
                                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin hapus? Stok barang akan dikembalikan/dibatalkan.');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded hover:bg-red-100" title="Hapus">
                                                        🗑️
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada transaksi yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-4">{{ $transactions->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>