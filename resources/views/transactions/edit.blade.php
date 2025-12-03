<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Transaksi: ') }} 
            <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold ml-1">{{ $transaction->invoice_code }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            {{-- CARD CONTAINER --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    {{-- WARNING ALERT --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-500 p-5 mb-8 text-sm text-yellow-800 dark:text-yellow-400 rounded-r-xl flex gap-4 items-start shadow-sm">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-full flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-wider mb-1 text-xs">Perhatian Admin</p>
                            <p class="mb-1 leading-relaxed">Anda hanya dapat mengedit data umum (Tanggal, Market, Catatan).</p>
                            <p class="leading-relaxed opacity-90">Jika ingin mengubah <b>jumlah barang/item</b>, silakan <span class="font-bold underline cursor-help" title="Menghapus transaksi akan mengembalikan stok barang seperti semula">Hapus Transaksi</span> ini lalu buat baru agar perhitungan stok tetap akurat.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- GRID INPUT --}}
                        <div class="space-y-6">
                            
                            {{-- Tanggal --}}
                            <div>
                                <x-input-label for="transaction_date" :value="__('Tanggal Transaksi')" class="dark:text-gray-300" />
                                <x-text-input id="transaction_date" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 shadow-sm" type="date" name="transaction_date" :value="old('transaction_date', $transaction->transaction_date->format('Y-m-d'))" required />
                            </div>

                            {{-- Market --}}
                            <div>
                                <x-input-label for="market" :value="__('Market / Supplier')" class="dark:text-gray-300" />
                                <x-text-input id="market" class="block mt-1 w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 shadow-sm" type="text" name="market" :value="old('market', $transaction->market)" placeholder="Nama Customer atau Toko..." />
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <x-input-label for="description" :value="__('Catatan')" class="dark:text-gray-300" />
                                <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4" placeholder="Tambahkan catatan revisi...">{{ old('description', $transaction->description) }}</textarea>
                            </div>

                        </div>

                        {{-- FOOTER BUTTONS --}}
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 gap-4">
                            <a href="{{ route('transactions.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-bold text-sm transition px-4 py-2">
                                Batal
                            </a>
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-lg shadow-indigo-500/30 border-0 py-2.5 px-6">
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>