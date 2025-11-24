<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Aktivitas (Audit Log)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('histories.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Barang</label>
                        <select name="item_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Barang</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->code }} - {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Aktor</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Dari Tgl</label>
                        <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Sampai Tgl</label>
                        <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition w-full">
                            Cari
                        </button>
                        @if(request()->hasAny(['item_id', 'user_id', 'date_start', 'date_end']))
                            <a href="{{ route('histories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-3 rounded-lg text-sm transition text-center" title="Reset">
                                ↺
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aktor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perubahan Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perubahan Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perubahan Market</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($histories as $history)
                                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-medium text-gray-900">{{ $history->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $history->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">{{ $history->user->name ?? 'System' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $history->user->role ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($history->item)
                                            <div class="text-sm font-bold text-indigo-600">{{ $history->item->name }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $history->item->code }}</div>
                                        @else
                                            <span class="text-red-500 italic text-sm font-bold">(Item Dihapus)</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $history->reason }}">
                                        {{ $history->reason ?? 'Update Manual' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($history->old_buy_price != $history->new_buy_price)
                                            <div class="mb-1">
                                                <span class="text-xs text-gray-500 font-bold uppercase">Modal:</span>
                                                <span class="line-through text-gray-400 text-xs">{{ number_format($history->old_buy_price) }}</span>
                                                <span class="text-gray-400 mx-1">&rarr;</span>
                                                <span class="font-bold text-blue-600">{{ number_format($history->new_buy_price) }}</span>
                                            </div>
                                        @endif

                                        @if($history->old_sell_price != $history->new_sell_price)
                                            <div>
                                                <span class="text-xs text-gray-500 font-bold uppercase">Jual:</span>
                                                <span class="line-through text-gray-400 text-xs">{{ number_format($history->old_sell_price) }}</span>
                                                <span class="text-gray-400 mx-1">&rarr;</span>
                                                <span class="font-bold text-green-600">{{ number_format($history->new_sell_price) }}</span>
                                            </div>
                                        @endif

                                        @if($history->old_buy_price == $history->new_buy_price && $history->old_sell_price == $history->new_sell_price)
                                            <span class="text-gray-300 text-xs italic">Tidak berubah</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($history->old_stock != $history->new_stock)
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400">{{ $history->old_stock }}</span>
                                                <span class="text-gray-300">&rarr;</span>
                                                <span class="font-bold {{ $history->new_stock > $history->old_stock ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $history->new_stock }}
                                                </span>
                                                <span class="text-xs font-bold px-1.5 py-0.5 rounded {{ $history->new_stock > $history->old_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $history->new_stock > $history->old_stock ? '+' : '' }}{{ $history->new_stock - $history->old_stock }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-300 text-xs italic">Tetap</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($history->old_market != $history->new_market)
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-400 line-through">{{ $history->old_market ?? '-' }}</span>
                                                <span class="font-bold text-purple-600">{{ $history->new_market ?? '-' }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-300 text-xs italic">Tetap</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">Belum ada riwayat aktivitas.</p>
                                            <p class="text-sm text-gray-400">Semua perubahan stok & harga akan tercatat di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>