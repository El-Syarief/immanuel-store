<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Barang: ') . $item->name }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    {{-- CSS KHUSUS TOMSELECT DARK MODE --}}
    <style>
        .dark .ts-control {
            background-color: #111827 !important; /* bg-gray-900 */
            border-color: #374151 !important;     /* border-gray-700 */
            color: #f3f4f6 !important;            /* text-gray-100 */
            border-radius: 0.5rem;
            padding: 0.6rem;
        }
        .dark .ts-dropdown {
            background-color: #111827 !important;
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }
        .dark .ts-dropdown .option:hover, 
        .dark .ts-dropdown .active {
            background-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-control input {
            color: #e5e7eb !important;
        }
        .dark .ts-wrapper.multi .ts-control > div {
            background-color: #374151 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD CONTAINER --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Data Barang</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui informasi barang di bawah ini.</p>
                        </div>
                        <div class="text-right hidden sm:block">
                            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-mono font-bold border border-indigo-100 dark:border-indigo-800">
                                {{ $item->code }}
                            </span>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('items.update', $item->id) }}">
                        @csrf
                        @method('PUT') 

                        {{-- GRID 1: Kode & Nama --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="code" :value="__('Kode Barang')" class="dark:text-gray-300" />
                                <x-text-input id="code" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="text" name="code" :value="old('code', $item->code)" required />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Nama Barang')" class="dark:text-gray-300" />
                                <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="text" name="name" :value="old('name', $item->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        {{-- GRID 2: Akun/Warehouse --}}
                        <div class="mb-6">
                            <x-input-label for="warehouses" :value="__('Akun / Lokasi')" class="dark:text-gray-300" />
                            <div class="mt-1">
                                <select id="warehouses" name="warehouses[]" multiple placeholder="Pilih atau Ketik Akun Baru..." autocomplete="off">
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ (in_array($warehouse->id, (array) old('warehouses', $item->warehouses->pluck('id')->toArray())) ) ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                new TomSelect("#warehouses", {
                                    plugins: ['remove_button'],
                                    create: true,
                                    persist: false,
                                    createOnBlur: true,
                                });
                            });
                        </script>

                        {{-- GRID 3: Kriteria & Stok --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="criteria" :value="__('Kriteria')" class="dark:text-gray-300" />
                                <x-text-input id="criteria" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="text" name="criteria" :value="old('criteria', $item->criteria)" />
                            </div>

                            <div>
                                <x-input-label for="stock" :value="__('Stok Saat Ini')" class="dark:text-gray-300" />
                                <x-text-input id="stock" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" 
                                    type="number" name="stock" :value="old('stock', $item->stock)"/> 
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">* Mengubah stok di sini akan tercatat sebagai koreksi manual.</p>
                            </div>
                        </div>

                        {{-- GRID 4: Harga (Modal & Jual) --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 mb-6">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Pengaturan Harga</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="buy_price" :value="__('Harga Modal ($)')" class="dark:text-gray-300" />
                                    <div class="relative mt-1 rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <x-text-input id="buy_price" class="block w-full pl-7 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="number" step="0.01" name="buy_price" :value="old('buy_price', $item->buy_price)" required />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="sell_price" :value="__('Harga Jual ($)')" class="dark:text-gray-300" />
                                    <div class="relative mt-1 rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <x-text-input id="sell_price" class="block w-full pl-7 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600" type="number" step="0.01" name="sell_price" :value="old('sell_price', $item->sell_price)" required />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Deskripsi')" class="dark:text-gray-300" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $item->description) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('items.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4 font-bold text-sm transition">
                                Batal
                            </a>
                            <x-primary-button class="ml-4 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-500/30 border-0">
                                {{ __('Perbarui Barang') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>