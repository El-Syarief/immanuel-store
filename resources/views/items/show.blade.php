<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Barang: ') . $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row justify-between md:items-start gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
                            @if($item->criteria)
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded border border-blue-200">
                                    {{ $item->criteria }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 font-mono">Kode: {{ $item->code }}</p>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Stok</p>
                        <p class="text-3xl font-bold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $item->stock }} <span class="text-sm text-gray-500 font-normal">Unit</span>
                        </p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Harga</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3 border border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Harga Jual</span>
                                    <span class="font-bold text-lg text-gray-900">$ {{ number_format($item->sell_price, 2, '.', ',') }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2 flex justify-between items-center">
                                    <span class="text-gray-600">Harga Modal</span>
                                    <span class="font-medium text-gray-900">$ {{ number_format($item->buy_price, 2, '.', ',') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500">Margin Profit</span>
                                    <span class="text-green-600 font-bold">+ $ {{ number_format($item->sell_price - $item->buy_price, 2, '.', ',') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Akun</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($item->warehouses as $wh)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m-4-2a2 2 0 012-2h5a2 2 0 012 2"></path></svg>
                                        {{ $wh->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 italic text-sm">Belum ditempatkan di akun manapun.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Deskripsi Barang</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 min-h-[120px] text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                {{ $item->description ?: 'Tidak ada deskripsi.' }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Metadata</h3>
                            <ul class="text-sm space-y-2">
                                <li class="flex justify-between">
                                    <span class="text-gray-500">Dibuat Oleh:</span>
                                    <span class="text-gray-900 font-medium">{{ $item->creator->name ?? 'Sistem' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-500">Tanggal Input:</span>
                                    <span class="text-gray-900">{{ $item->created_at->format('d M Y, H:i') }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-500">Terakhir Update:</span>
                                    <span class="text-gray-900">{{ $item->updated_at->format('d M Y, H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('items.index') }}" class="text-gray-600 hover:text-gray-900 font-bold text-sm flex items-center gap-1">
                        &larr; Kembali
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <div class="flex items-center gap-3">
                            <a href="{{ route('items.edit', $item->id) }}" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-bold py-2 px-4 rounded-lg transition text-sm">
                                Edit Barang
                            </a>
                            
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini? Data riwayat mungkin akan terpengaruh.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow-sm">
                                    Hapus Barang
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>