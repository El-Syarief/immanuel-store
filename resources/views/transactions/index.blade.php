<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $isSearchItemMode ? __('Hasil Pencarian Barang') : __('Transaksi') }}
            </h2>
            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'admin')
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm flex items-center gap-2 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Export
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100" style="display: none;">
                            <a href="{{ route('transactions.export.excel', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600 flex items-center gap-2">📄 Download Excel</a>
                            <a href="{{ route('transactions.export.pdf', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 flex items-center gap-2">📕 Download PDF</a>
                        </div>
                    </div>
                @endif
                <a href="{{ route('transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm transition">+ Transaksi Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('transactions.index') }}">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div class="w-full relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari Nomor Invoice atau Nama Barang..." 
                                class="w-full pl-11 bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-3 transition shadow-sm hover:bg-white">
                        </div>

                        @if($isSearchItemMode)
                            <div class="whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    🔍 Mode Pencarian Barang
                                </span>
                            </div>
                        @endif
                    </div>

                    <hr class="border-dashed border-gray-200 mb-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tipe</label>
                            <select name="type" class="w-full bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>📤 Penjualan</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>📥 Pembelian</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Market</label>
                            <select name="market" class="w-full bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach($markets as $m)
                                    <option value="{{ $m }}" {{ request('market') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Kasir / Admin</label>
                            <select name="user_id" class="w-full bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dari Tgl</label>
                            <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sampai Tgl</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full bg-gray-50 border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5" onchange="this.form.submit()">
                        </div>

                        <div>
                            <a href="{{ route('transactions.index') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-600 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>

                </form>
            </div>

            @if($isSearchItemMode)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-indigo-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase">Nama Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase">No Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-800 uppercase">Market</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-indigo-800 uppercase">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-indigo-800 uppercase">Harga</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-indigo-800 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactionDetails as $detail)
                                    <tr class="hover:bg-indigo-50 transition cursor-pointer" onclick="window.location='{{ route('transactions.show', $detail->transaction_id) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ $detail->item->name ?? 'Item Terhapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">{{ $detail->transaction->invoice_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->transaction->transaction_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->transaction->market ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">{{ $detail->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">$ {{ number_format($detail->price, 2, '.', ',') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">$ {{ number_format($detail->subtotal, 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Tidak ditemukan barang dengan nama tersebut.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-50 px-6 py-4">{{ $transactionDetails->links() }}</div>
                </div>

            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Market</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi (Admin)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $trx)
                                    <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="window.location='{{ route('transactions.show', $trx->id) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm font-bold text-indigo-600 group-hover:underline">
                                            {{ $trx->invoice_code }}
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
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $trx->market ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($trx->description, 30) ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$ {{ number_format($trx->grand_total, 2, '.', ',') }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium" onclick="event.stopPropagation()">
                                            @if(auth()->user()->role === 'admin')
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('transactions.edit', $trx->id) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 p-2 rounded hover:bg-yellow-100">✏️</a>
                                                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded hover:bg-red-100">🗑️</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Tidak ada transaksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-50 px-6 py-4">{{ $transactions->links() }}</div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>