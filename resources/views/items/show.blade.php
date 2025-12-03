<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Barang: ') . $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
                
                {{-- HEADER KARTU --}}
                <div class="p-8 bg-gray-50 dark:bg-gray-700/20 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between md:items-start gap-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $item->name }}</h1>
                            @if($item->criteria)
                                <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-xs font-bold px-3 py-1 rounded-full border border-blue-200 dark:border-blue-800 uppercase tracking-wide">
                                    {{ $item->criteria }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 font-mono text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            {{ $item->code }}
                        </div>
                    </div>
                    
                    <div class="text-left md:text-right bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Stok Fisik</p>
                        <p class="text-4xl font-extrabold {{ $item->stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $item->stock }} <span class="text-sm text-gray-500 dark:text-gray-500 font-medium">Unit</span>
                        </p>
                    </div>
                </div>

                {{-- BADAN KARTU --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    {{-- KOLOM KIRI --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Informasi Finansial
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 space-y-4 border border-gray-200 dark:border-gray-700/50">
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700 border-dashed">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Harga Jual</span>
                                    <span class="font-bold text-xl text-gray-900 dark:text-white">$ {{ number_format($item->sell_price, 2, '.', ',') }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700 border-dashed">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Harga Modal</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-300">$ {{ number_format($item->buy_price, 2, '.', ',') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-1">
                                    <span class="text-xs font-bold uppercase text-gray-400">Potensi Profit</span>
                                    <span class="text-green-600 dark:text-green-400 font-bold bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded text-sm border border-green-100 dark:border-green-900">
                                        + $ {{ number_format($item->sell_price - $item->buy_price, 2, '.', ',') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m-4-2a2 2 0 012-2h5a2 2 0 012 2"></path></svg>
                                Lokasi Akun
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($item->warehouses as $wh)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/40 transition cursor-default">
                                        {{ $wh->name }}
                                    </span>
                                @empty
                                    <div class="flex items-center text-gray-400 dark:text-gray-500 italic text-sm gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Belum ditempatkan di akun manapun.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                Deskripsi Barang
                            </h3>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700/50 min-h-[140px] text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-line shadow-inner">
                                {{ $item->description ?: 'Tidak ada deskripsi tambahan untuk barang ini.' }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Metadata
                            </h3>
                            <ul class="text-sm space-y-3 bg-white dark:bg-gray-800 rounded-lg">
                                <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 border-dashed">
                                    <span class="text-gray-500 dark:text-gray-400">Dibuat Oleh</span>
                                    <div class="flex items-center gap-2">
                                        <div class="h-5 w-5 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                            {{ substr($item->creator->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $item->creator->name ?? 'Sistem' }}</span>
                                    </div>
                                </li>
                                <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 border-dashed">
                                    <span class="text-gray-500 dark:text-gray-400">Tanggal Input</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $item->created_at->format('d M Y, H:i') }}</span>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400">Terakhir Update</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-mono text-xs">{{ $item->updated_at->format('d M Y, H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- FOOTER KARTU --}}
                <div class="bg-gray-50 dark:bg-gray-700/20 px-8 py-5 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('items.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-bold text-sm flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a href="{{ route('items.edit', $item->id) }}" class="flex-1 sm:flex-none text-center bg-yellow-100 hover:bg-yellow-200 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400 dark:hover:bg-yellow-500/30 dark:border dark:border-yellow-500/50 font-bold py-2.5 px-5 rounded-xl transition text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Barang
                            </a>
                            
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini? Data riwayat mungkin akan terpengaruh.');" class="flex-1 sm:flex-none">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl transition text-sm shadow-lg shadow-red-500/30 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>