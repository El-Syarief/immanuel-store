<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        /* TOMSELECT DARK MODE CUSTOMIZATION */
        .dark .ts-control {
            background-color: #1f2937 !important; /* bg-gray-800 */
            border-color: #374151 !important;     /* border-gray-700 */
            color: #e5e7eb !important;            /* text-gray-200 */
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }
        .dark .ts-dropdown .option:hover, 
        .dark .ts-dropdown .active {
            background-color: #374151 !important; /* hover gray-700 */
            color: #fff !important;
        }
        .dark .ts-control input {
            color: #e5e7eb !important;
        }
        
        /* TomSelect Light Mode (Default) */
        .ts-control {
            border-radius: 0.5rem !important;
            padding-top: 0.6rem; padding-bottom: 0.6rem;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kelola Barang') }}
            </h2>
            @if(auth()->user()->role === 'admin')
                <div class="flex flex-wrap items-center gap-3">
                    {{-- TOMBOL EXPORT --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-green-500/20 text-sm flex items-center gap-2 transition transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Export Data
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        
                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 z-50 border border-gray-100 dark:border-gray-700" style="display: none;">
                            <a href="{{ route('items.export.excel', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 flex items-center gap-2 transition">📄 Download Excel</a>
                            <a href="{{ route('items.export.pdf', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 flex items-center gap-2 transition">📕 Download PDF</a>
                        </div>
                    </div>

                    {{-- TOMBOL TAMBAH --}}
                    <a href="{{ route('items.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-indigo-500/20 text-sm flex items-center gap-2 transition transform hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Barang
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 rounded-lg shadow-sm flex items-center gap-3" role="alert">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <div>
                        <p class="font-bold">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- FILTER CARD --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-6 border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Pencarian</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    class="bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 transition shadow-sm" 
                                    placeholder="Cari Kode atau Nama...">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Akun</label>
                            <select id="warehouse_filter" name="warehouse_id" class="w-full" placeholder="Pilih Akun..." autocomplete="off" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Akun</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Kriteria</label>
                            <select id="criteria_filter" name="criteria" class="w-full" placeholder="Pilih Kriteria..." autocomplete="off" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Kriteria</option>
                                @foreach($criterias as $criteria)
                                    <option value="{{ $criteria }}" {{ request('criteria') == $criteria ? 'selected' : '' }}>{{ $criteria }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Admin Pembuat</label>
                            <select id="creator_filter" name="creator_id" class="w-full" placeholder="Pilih Admin..." autocomplete="off" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Admin</option>
                                @foreach($creators as $creator)
                                    <option value="{{ $creator->id }}" {{ request('creator_id') == $creator->id ? 'selected' : '' }}>{{ $creator->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex items-center gap-4 mt-2">
                            <div class="w-1/3">
                                <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 block tracking-wider">Urutkan</label>
                                <select name="sort_by" onchange="this.form.submit()" class="bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition shadow-sm cursor-pointer">
                                    <option value="" disabled selected class="text-gray-400">Pilih Urutan...</option>
                                    
                                    <option value="stock-desc" {{ request('sort_by') == 'stock-desc' ? 'selected' : '' }}>📦 Stok (Terbanyak)</option>
                                    <option value="stock-asc" {{ request('sort_by') == 'stock-asc' ? 'selected' : '' }}>📦 Stok (Sedikit)</option>

                                    <option value="code-asc" {{ request('sort_by') == 'code-asc' ? 'selected' : '' }}>🔢 Kode (A - Z / 0 - 9)</option>
                                    <option value="code-desc" {{ request('sort_by') == 'code-desc' ? 'selected' : '' }}>🔢 Kode (Z - A / 9 - 0)</option>

                                    <option disabled>──────────</option>

                                    <option value="created_at-desc" {{ request('sort_by') == 'created_at-desc' ? 'selected' : '' }}>📅 Dibuat (Terbaru)</option>
                                    <option value="created_at-asc" {{ request('sort_by') == 'created_at-asc' ? 'selected' : '' }}>📅 Dibuat (Terlama)</option>
                                    <option value="updated_at-desc" {{ request('sort_by') == 'updated_at-desc' ? 'selected' : '' }}>✏️ Diupdate (Terbaru)</option>
                                    <option value="name-asc" {{ request('sort_by') == 'name-asc' ? 'selected' : '' }}>🔤 Nama (A - Z)</option>
                                    <option value="name-desc" {{ request('sort_by') == 'name-desc' ? 'selected' : '' }}>🔤 Nama (Z - A)</option>
                                </select>
                            </div>
                            
                            @if(request()->hasAny(['search', 'criteria', 'creator_id', 'sort_by', 'warehouse_id']))
                                <div class="mt-7">
                                    <a href="{{ route('items.index') }}" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2.5 rounded-xl transition text-sm font-bold flex items-center gap-2 border border-transparent hover:border-red-100 dark:hover:border-red-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" /></svg>
                                        Reset Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="md:col-span-1 text-right mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-500/20 text-sm transition w-full transform active:scale-95">
                                Terapkan
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            {{-- TABEL DATA --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akun</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pembuat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($items as $item)
                                <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition duration-150 ease-in-out cursor-pointer group" onclick="window.location='{{ route('items.show', $item->id) }}'">
                                    
                                    {{-- Kode --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400 font-bold group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $item->code }}</td>
                                    
                                    {{-- Nama --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->name }}</div>
                                        @if($item->criteria)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 mt-1 border border-blue-200 dark:border-blue-800">
                                                {{ $item->criteria }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Akun (Warehouse) --}}
                                    <td class="px-6 py-4 whitespace-normal w-56">
                                        @if($item->warehouses->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item->warehouses as $wh)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800">
                                                        {{ $wh->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600 text-xs italic">Belum ada lokasi</span>
                                        @endif
                                    </td>

                                    {{-- Stok --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->stock > 0)
                                            <div class="flex items-center">
                                                <span class="h-2.5 w-2.5 rounded-full bg-green-500 mr-2"></span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->stock }} Unit</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="h-2.5 w-2.5 rounded-full bg-red-500 mr-2 animate-pulse"></span>
                                                <span class="text-sm font-bold text-red-600 dark:text-red-400">Habis</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Harga --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-bold">$ {{ number_format($item->sell_price, 2, '.', ',') }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">Modal: $ {{ number_format($item->buy_price, 2, '.', ',') }}</div>
                                    </td>

                                    {{-- Pembuat --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold text-xs mr-2 border border-gray-200 dark:border-gray-600">
                                                {{ substr($item->creator->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white text-xs">{{ $item->creator->name ?? 'Sistem' }}</div>
                                                <div class="text-[10px] text-gray-400">{{ $item->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <p class="text-lg font-medium">Tidak ada barang ditemukan.</p>
                                            <p class="text-sm opacity-70">Coba ubah filter atau kata kunci pencarian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const config = {
                create: false, 
                sortField: { field: "text", direction: "asc" }
            };
            new TomSelect("#warehouse_filter", config);
            new TomSelect("#criteria_filter", config);
            new TomSelect("#creator_filter", config);
        });
    </script>
</x-app-layout>