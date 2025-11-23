<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Transaksi') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm">
                + Transaksi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('transactions.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">No Invoice</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Invoice..." class="w-full border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Tipe</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>📤 Penjualan (OUT)</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>📥 Pembelian (IN)</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kasir / Admin</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <a href="{{ route('transactions.index') }}" class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg text-sm">Reset</a>
                        </div>
                    </div>
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
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-4">{{ $transactions->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>