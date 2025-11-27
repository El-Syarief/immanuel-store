<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Barang Baru') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('items.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Kode Barang (Unik)')" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Barang')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="warehouses" :value="__('Akun (Bisa pilih banyak / ketik baru)')" />
                            <select id="warehouses" name="warehouses[]" multiple placeholder="Pilih atau Ketik Akun Baru..." autocomplete="off">
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ in_array($warehouse->id, (array) old('warehouses', [])) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                new TomSelect("#warehouses", {
                                    plugins: ['remove_button'], // Tombol X untuk hapus pilihan
                                    create: true, // Bolehkan user mengetik nama baru
                                    persist: false,
                                    createOnBlur: true,
                                });
                            });
                        </script>

                        <div class="mb-4">
                            <x-input-label for="criteria" :value="__('Kriteria (Opsional)')" />
                            <x-text-input id="criteria" class="block mt-1 w-full" type="text" name="criteria" :value="old('criteria')" />
                            <x-input-error :messages="$errors->get('criteria')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="stock" :value="__('Stok Awal')" />
                                
                                <x-text-input id="stock" class="block mt-1 w-full" 
                                    type="number" name="stock" :value="0"/>
                            </div>
                            
                            <div>
                                <x-input-label for="buy_price" :value="__('Harga Modal ($)')" />
                                <x-text-input id="buy_price" class="block mt-1 w-full" type="number" step="0.01" name="buy_price" :value="old('buy_price', 0)" required />
                            </div>

                            <div>
                                <x-input-label for="sell_price" :value="__('Harga Jual ($)')" />
                                <x-text-input id="sell_price" class="block mt-1 w-full" type="number" step="0.01" name="sell_price" :value="old('sell_price', 0)" required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Deskripsi (Opsional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('items.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button class="ml-4">
                                {{ __('Simpan Barang') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>