<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-gray-600">
                        Anda login sebagai <span class="font-bold uppercase text-indigo-600">{{ Auth::user()->role }}</span>.
                        Berikut adalah ringkasan aktivitas toko hari ini.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Omzet Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$ {{ number_format($todayRevenue, 2, '.', ',') }}</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Transaksi Berhasil</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $todayTransactions }}</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Item Terjual</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $todayItemsSold }} Pcs</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        ⚠️ Stok Menipis (< 5)
                    </h3>
                    @if($lowStockItems->count() >= 0)
                        <ul class="divide-y divide-gray-100">
                            @foreach($lowStockItems as $item)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->code }}</p>
                                    </div>
                                    <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">
                                        Sisa: {{ $item->stock }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 text-center">
                            <a href="{{ route('items.index') }}" class="text-indigo-600 text-sm font-bold hover:underline">Lihat Semua Barang &rarr;</a>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Aman! Tidak ada barang yang stoknya kritis.</p>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('transactions.create') }}" class="block p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition text-center">
                            <span class="text-2xl">🛒</span>
                            <p class="font-bold text-indigo-700 mt-2">Kasir Baru</p>
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('items.create') }}" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition text-center">
                                <span class="text-2xl">📦</span>
                                <p class="font-bold text-green-700 mt-2">Tambah Barang</p>
                            </a>
                            <a href="{{ route('reports.index') }}" class="block p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition text-center">
                                <span class="text-2xl">📊</span>
                                <p class="font-bold text-yellow-700 mt-2">Laporan</p>
                            </a>
                            <a href="{{ route('users.create') }}" class="block p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition text-center">
                                <span class="text-2xl">👥</span>
                                <p class="font-bold text-purple-700 mt-2">Tambah User</p>
                            </a>
                        @else
                            <a href="{{ route('transactions.index') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                                <span class="text-2xl">📄</span>
                                <p class="font-bold text-gray-700 mt-2">Riwayat Transaksi</p>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>