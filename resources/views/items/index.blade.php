<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Barang') }}
            </h2>
            @if(auth()->user()->role === 'admin')
                <div class="flex items-center gap-3">
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
                            <a href="{{ route('items.export.excel', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-green-600 flex items-center gap-2">
                                📄 Download Excel
                            </a>
                            <a href="{{ route('items.export.pdf', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 flex items-center gap-2">
                                📕 Download PDF
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('items.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out text-sm shadow-md">
                        + Tambah Barang
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('items.index') }}">
                    <div class="flex flex-col md:flex-row gap-4 md:items-center justify-between">
                        
                        <div class="w-full md:w-1/4 relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition" 
                                placeholder="Cari Kode atau Nama...">
                        </div>

                        <div class="flex flex-wrap gap-3 w-full md:w-auto items-center justify-end">
                            
                            <select name="market" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:z-10 p-2.5 pr-10 cursor-pointer hover:bg-gray-100 transition outline-none relative">
                                <option value="">Semua Market</option>
                                @foreach($markets as $market)
                                    <option value="{{ $market }}" {{ request('market') == $market ? 'selected' : '' }}>
                                        {{ $market }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="criteria" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:z-10 p-2.5 pr-10 cursor-pointer hover:bg-gray-100 transition outline-none relative">
                                <option value="">Semua Kriteria</option>
                                @foreach($criterias as $criteria)
                                    <option value="{{ $criteria }}" {{ request('criteria') == $criteria ? 'selected' : '' }}>
                                        {{ $criteria }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="creator_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:z-10 p-2.5 pr-10 cursor-pointer hover:bg-gray-100 transition outline-none relative">
                                <option value="">Semua Admin</option>
                                @foreach($creators as $creator)
                                    <option value="{{ $creator->id }}" {{ request('creator_id') == $creator->id ? 'selected' : '' }}>
                                        {{ $creator->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="sort_by" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:z-10 p-2.5 pr-10 font-medium shadow-sm cursor-pointer hover:bg-gray-50 transition outline-none relative">
                                <option value="created_at-desc" {{ request('sort_by') == 'created_at-desc' ? 'selected' : '' }}>📅 Dibuat (Terbaru)</option>
                                <option value="created_at-asc" {{ request('sort_by') == 'created_at-asc' ? 'selected' : '' }}>📅 Dibuat (Terlama)</option>
                                <option value="updated_at-desc" {{ request('sort_by') == 'updated_at-desc' ? 'selected' : '' }}>✏️ Diupdate (Terbaru)</option>
                                <option value="updated_at-asc" {{ request('sort_by') == 'updated_at-asc' ? 'selected' : '' }}>✏️ Diupdate (Terlama)</option>
                                <option value="name-asc" {{ request('sort_by') == 'name-asc' ? 'selected' : '' }}>🔤 Nama (A - Z)</option>
                                <option value="name-desc" {{ request('sort_by') == 'name-desc' ? 'selected' : '' }}>🔤 Nama (Z - A)</option>
                            </select>

                            @if(request()->hasAny(['search', 'criteria', 'creator_id', 'sort_by', 'market']))
                                <a href="{{ route('items.index') }}" class="text-red-500 hover:text-red-700 hover:bg-red-100 p-2 rounded-full transition" title="Hapus Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Market</th> <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pembuat</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                @if(auth()->user()->role === 'admin')
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($items as $item)
                                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $item->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                        @if($item->criteria)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                {{ $item->criteria }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->market)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                {{ $item->market }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->stock }} Unit
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-bold">Rp {{ number_format($item->sell_price, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-400">Modal: Rp {{ number_format($item->buy_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs mr-2">
                                                {{ substr($item->creator->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $item->creator->name ?? 'Sistem' }}</div>
                                                <div class="text-xs text-gray-400">{{ $item->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $item->description }}">
                                        {{ Str::limit($item->description ?? '-', 40) }}
                                    </td>
                                    @if(auth()->user()->role === 'admin')
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('items.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition">Edit</a>
                                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus barang ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="text-lg font-medium">Tidak ada barang ditemukan.</p>
                                            <p class="text-sm text-gray-400">Coba ubah filter atau kata kunci pencarian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>