<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                
                <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $transaction->invoice_code }}</h1>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600"><span class="font-bold">Tanggal:</span> {{ $transaction->transaction_date->format('d F Y') }}</p>
                            <p class="text-sm text-gray-600"><span class="font-bold">Kasir:</span> {{ $transaction->user->name }}</p>
                            <p class="text-sm text-gray-600"><span class="font-bold">Market:</span> {{ $transaction->market ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($transaction->type == 'out')
                            <span class="bg-indigo-100 text-indigo-800 text-lg font-bold px-4 py-1 rounded">OUT (JUAL)</span>
                        @else
                            <span class="bg-green-100 text-green-800 text-lg font-bold px-4 py-1 rounded">IN (BELI)</span>
                        @endif
                        
                        @if($transaction->description)
                            <div class="mt-4 bg-yellow-50 p-2 rounded border border-yellow-200 text-left max-w-xs">
                                <p class="text-xs font-bold text-yellow-800 uppercase">Catatan:</p>
                                <p class="text-sm text-yellow-900 italic">"{{ $transaction->description }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Rincian Barang</h3>
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Barang</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($transaction->details as $detail)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-800">{{ $detail->item->name ?? 'Barang Dihapus' }}</div>
                                        <div class="text-xs text-gray-500">{{ $detail->item->code ?? '-' }}</div>
                                    </td>
                                    
                                    <td class="px-4 py-3 text-right text-sm">
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-sm font-bold">{{ $detail->quantity }}</td>
                                    
                                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-bold text-lg text-gray-600">TOTAL</td>
                                <td class="px-4 py-4 text-right font-bold text-xl text-indigo-600">
                                    Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">
                        &larr; Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>