<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- INVOICE CARD --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
                
                {{-- HEADER INVOICE --}}
                <div class="p-8 bg-gray-50 dark:bg-gray-700/20 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white font-mono tracking-tight">{{ $transaction->invoice_code }}</h1>
                            <div class="hidden md:block">
                                @if($transaction->type == 'out')
                                    <span class="bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-500/30 uppercase tracking-wide">PENJUALAN</span>
                                @else
                                    <span class="bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 text-xs font-bold px-3 py-1 rounded-full border border-green-200 dark:border-green-500/30 uppercase tracking-wide">PEMBELIAN</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-1">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="font-medium">Tanggal:</span> {{ $transaction->transaction_date->format('d F Y') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="font-medium">Admin/Kasir:</span> {{ $transaction->user->name }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m-4-2a2 2 0 012-2h5a2 2 0 012 2"></path></svg>
                                <span class="font-medium">Market/Supplier:</span> {{ $transaction->market ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="text-left md:text-right w-full md:w-auto mt-4 md:mt-0">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</p>
                        <p class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">
                            $ {{ number_format($transaction->grand_total, 2, '.', ',') }}
                        </p>

                        @if($transaction->description)
                            <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-xl border border-yellow-200 dark:border-yellow-700/50 text-left md:max-w-xs">
                                <p class="text-[10px] font-bold text-yellow-800 dark:text-yellow-500 uppercase tracking-wide mb-1">Catatan:</p>
                                <p class="text-sm text-yellow-900 dark:text-yellow-200 italic leading-snug">"{{ $transaction->description }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TABEL RINCIAN --}}
                <div class="p-0 overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold tracking-wider">Barang</th>
                                <th class="px-6 py-4 text-left font-bold tracking-wider">Lokasi (Snapshot)</th>
                                <th class="px-6 py-4 text-right font-bold tracking-wider">Harga Satuan (Deal)</th>
                                <th class="px-6 py-4 text-right font-bold tracking-wider">
                                    {{ $transaction->type == 'in' ? 'Ref. Harga Jual' : 'Ref. Modal' }}
                                </th>
                                <th class="px-6 py-4 text-center font-bold tracking-wider">Qty</th>
                                <th class="px-6 py-4 text-right font-bold tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($transaction->details as $detail)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    {{-- Barang --}}
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $detail->item->name ?? 'Barang Dihapus' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-500 font-mono mt-0.5">{{ $detail->item->code ?? '-' }}</div>
                                    </td>

                                    {{-- Lokasi --}}
                                    <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400 max-w-xs break-words">
                                        @if(!empty($detail->location_snapshot))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(explode(',', $detail->location_snapshot) as $loc)
                                                    <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-600">
                                                        {{ trim($loc) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600 italic">-</span>
                                        @endif
                                    </td>

                                    {{-- Harga Deal --}}
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-200">
                                        @php
                                            $hargaDeal = $detail->price;
                                            if ($hargaDeal <= 0) {
                                                $hargaDeal = ($transaction->type == 'in') ? $detail->buy_price_snapshot : $detail->sell_price_snapshot;
                                            }
                                        @endphp
                                        $ {{ number_format($hargaDeal, 2, '.', ',') }}
                                    </td>

                                    {{-- Ref. Modal / Profit --}}
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        @php
                                            $hargaAudit = ($transaction->type == 'in') ? $detail->sell_price_snapshot : $detail->buy_price_snapshot;
                                            $selisih = ($transaction->type == 'out') ? ($hargaDeal - $hargaAudit) : ($hargaAudit - $hargaDeal);
                                            $persen = ($hargaAudit > 0) ? ($selisih / $hargaAudit) * 100 : 0;
                                            
                                            // Warna Conditional Dark Mode Friendly
                                            $warnaSelisih = $selisih >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                                        @endphp
                                        
                                        <div class="text-gray-500 dark:text-gray-400 mb-1">$ {{ number_format($hargaAudit, 2, '.', ',') }}</div>
                                        
                                        <div class="text-xs {{ $warnaSelisih }} font-bold bg-gray-50 dark:bg-gray-900/50 inline-block px-1.5 py-0.5 rounded">
                                            {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }} 
                                            ({{ number_format($persen, 1) }}%)
                                        </div>
                                    </td>

                                    {{-- Qty --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $detail->quantity }}
                                        </span>
                                    </td>
                                    
                                    {{-- Subtotal --}}
                                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                        $ {{ number_format($detail->subtotal, 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700/20 border-t border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="5" class="px-6 py-5 text-right font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Pembayaran</td>
                                <td class="px-6 py-5 text-right font-extrabold text-2xl text-indigo-600 dark:text-indigo-400">
                                    $ {{ number_format($transaction->grand_total, 2, '.', ',') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- FOOTER CARD --}}
                <div class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-8 py-5 flex items-center justify-between">
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-bold text-sm flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Riwayat
                    </a>
                    
                    {{-- Tombol Print/Download (Optional Placeholder) --}}
                    <!-- <button onclick="window.print()" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-bold text-sm flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Halaman
                    </button> -->
                </div>

            </div>
        </div>
    </div>
</x-app-layout>