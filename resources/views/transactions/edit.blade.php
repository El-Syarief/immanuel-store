<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaksi: ') . $transaction->invoice_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-sm text-yellow-700">
                    <p class="font-bold">Perhatian:</p>
                    <p>Anda hanya dapat mengedit data umum (Tanggal, Market, Catatan).</p>
                    <p>Jika ingin mengubah jumlah barang/item, silakan <b>Hapus Transaksi</b> ini lalu buat baru agar stok tetap akurat.</p>
                </div>

                <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="transaction_date" :value="__('Tanggal Transaksi')" />
                        <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', $transaction->transaction_date->format('Y-m-d'))" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="market" :value="__('Market / Supplier')" />
                        <x-text-input id="market" class="block mt-1 w-full" type="text" name="market" :value="old('market', $transaction->market)" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Catatan')" />
                        <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('description', $transaction->description) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 py-2">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>