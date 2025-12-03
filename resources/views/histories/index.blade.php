<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    
    <style>
        /* CSS TOMSELECT DARK MODE */
        .dark .ts-control {
            background-color: #111827 !important; /* bg-gray-900 */
            border-color: #374151 !important;     /* border-gray-700 */
            color: #f3f4f6 !important;            /* text-gray-100 */
            border-radius: 0.5rem;
            padding-top: 0.5rem; padding-bottom: 0.5rem;
            min-height: 42px; display: flex; align-items: center;
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }
        .dark .ts-dropdown .option:hover, 
        .dark .ts-dropdown .active {
            background-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-control input { color: #e5e7eb !important; }
        
        /* Light Mode Default */
        .ts-control {
            border-radius: 0.5rem !important; border-color: #d1d5db !important;
            padding-top: 0.5rem; padding-bottom: 0.5rem;
            min-height: 42px; display: flex; align-items: center;
        }
        .ts-wrapper.focus .ts-control {
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5) !important; border-color: #6366f1 !important;
        }
        .ts-dropdown { border-radius: 0.5rem; margin-top: 4px; }
        
        /* Scrollbar untuk Sidebar */
        #history-sidebar-panel::-webkit-scrollbar { width: 6px; }
        #history-sidebar-panel::-webkit-scrollbar-thumb { background-color: #4b5563; border-radius: 3px; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Aktivitas (Audit Log)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FILTER CARD --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm mb-6 border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <form method="GET" action="{{ route('histories.index') }}" id="filterFormHistory" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    {{-- 1. PILIH BARANG (TomSelect) --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Filter Barang</label>
                        <select id="select-item" name="item_id" placeholder="Cari Barang..." autocomplete="off" 
                            onchange="document.getElementById('filterFormHistory').submit()">
                            <option value="">Semua Barang</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->code }} - {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. KATA KUNCI (Text) --}}
                    <div class="md:col-span-3">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Kata Kunci / Aktivitas</label>
                        <input type="text" name="search_text" value="{{ request('search_text') }}" placeholder="Cth: Koreksi, Edit, Hapus..." 
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 h-[42px]"> 
                    </div>

                    {{-- 3. AKTOR --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Aktor</label>
                        <select name="user_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 h-[42px]">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. RENTANG WAKTU --}}
                    <div class="md:col-span-3">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Tanggal</label>
                        <div class="flex gap-1">
                            <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm px-1 focus:ring-indigo-500 focus:border-indigo-500 h-[42px]" title="Dari Tanggal">
                            <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm px-1 focus:ring-indigo-500 focus:border-indigo-500 h-[42px]" title="Sampai Tanggal">
                        </div>
                    </div>

                    {{-- 5. TOMBOL --}}
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition w-full shadow-md h-[42px]">
                            Cari
                        </button>
                        @if(request()->hasAny(['item_id', 'user_id', 'date_start', 'date_end', 'search_text']))
                            <a href="{{ route('histories.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-2 px-3 rounded-lg text-sm transition flex items-center justify-center border border-gray-300 dark:border-gray-600 h-[42px]" title="Reset Filter">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- TABEL RIWAYAT --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($histories as $entry)
                                @php
                                    $kind = $entry->kind;
                                    $data = $entry->data;
                                @endphp

                                <tr class="history-summary hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition duration-150 ease-in-out cursor-pointer group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $entry->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $entry->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($kind === 'history')
                                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $data->user->name ?? 'System' }}</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase">{{ $data->user->role ?? '-' }}</span>
                                        @else
                                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $data->actor->name ?? 'System' }}</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase">{{ $data->actor->role ?? '-' }}</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" title="{{ $kind === 'history' ? $data->reason : ($data->reason ?? json_encode($data->payload)) }}">
                                        @if($kind === 'history')
                                            {{ $data->reason ?? 'Update Manual' }}
                                        @else
                                            @php
                                                $summary = ucfirst(str_replace(['.','_'], ' ', $data->type));
                                                $payload = is_array($data->payload) ? $data->payload : json_decode($data->payload, true) ?? [];
                                            @endphp
                                            {{ $summary }} @if(!empty($payload['invoice_code'])) - <span class="font-mono">{{ $payload['invoice_code'] }}</span> @endif
                                        @endif
                                    </td>
                                </tr>

                                {{-- Detail row (HIDDEN - Content will be copied to Sidebar) --}}
                                <tr class="history-detail hidden">
                                    <td colspan="3" class="px-6 py-4">
                                        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                                            
                                            {{-- ====================== TIPE 1: HISTORY (STOK FLOW) ====================== --}}
                                            @if($kind === 'history')
                                                {{-- Barang Info --}}
                                                @if($data->item)
                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Barang</div>
                                                        <div class="font-bold text-indigo-600 dark:text-indigo-400 text-lg">{{ $data->item->name }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $data->item->code }}</div>
                                                        @if($data->item->warehouses && $data->item->warehouses->count())
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                                Akun: {{ $data->item->warehouses->pluck('name')->join(', ') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- [RESTORED] Price Changes --}}
                                                @if($data->old_buy_price != $data->new_buy_price || $data->old_sell_price != $data->new_sell_price)
                                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Perubahan Harga</div>
                                                        <div class="flex flex-col gap-2">
                                                            @if($data->old_buy_price != $data->new_buy_price)
                                                                <div class="flex justify-between items-center text-sm">
                                                                    <span class="text-gray-600 dark:text-gray-400 w-16">Modal:</span>
                                                                    <span class="line-through text-gray-400 dark:text-gray-500">$ {{ number_format($data->old_buy_price, 2, '.', ',') }}</span>
                                                                    <span class="text-gray-400">→</span>
                                                                    <span class="text-blue-600 dark:text-blue-400 font-bold">$ {{ number_format($data->new_buy_price, 2, '.', ',') }}</span>
                                                                </div>
                                                            @endif
                                                            @if($data->old_sell_price != $data->new_sell_price)
                                                                <div class="flex justify-between items-center text-sm">
                                                                    <span class="text-gray-600 dark:text-gray-400 w-16">Jual:</span>
                                                                    <span class="line-through text-gray-400 dark:text-gray-500">$ {{ number_format($data->old_sell_price, 2, '.', ',') }}</span>
                                                                    <span class="text-gray-400">→</span>
                                                                    <span class="text-green-600 dark:text-green-400 font-bold">$ {{ number_format($data->new_sell_price, 2, '.', ',') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Stock changes --}}
                                                @if($data->old_stock != $data->new_stock)
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Perubahan Stok</div>
                                                        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700">
                                                            <span class="text-gray-500 dark:text-gray-400">{{ $data->old_stock }}</span>
                                                            <span class="text-gray-400">→</span>
                                                            <span class="font-bold {{ $data->new_stock > $data->old_stock ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $data->new_stock }}</span>
                                                            
                                                            <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded {{ $data->new_stock > $data->old_stock ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                                                {{ $data->new_stock - $data->old_stock > 0 ? '+' : '' }}{{ $data->new_stock - $data->old_stock }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- [RESTORED] Market / Warehouse changes --}}
                                                @if(isset($data->old_market) && $data->old_market != $data->new_market)
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Akun / Market</div>
                                                        <div class="text-sm flex gap-2 items-center">
                                                            <span class="line-through text-gray-400 dark:text-gray-500">{{ $data->old_market ?? '-' }}</span>
                                                            <span class="text-gray-400">→</span>
                                                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ $data->new_market ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Keterangan --}}
                                                <div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Keterangan</div>
                                                    <div class="text-sm text-gray-800 dark:text-gray-200 p-2 bg-gray-50 dark:bg-gray-700/30 rounded border border-gray-100 dark:border-gray-700">
                                                        {{ $data->reason ?? '-' }}
                                                    </div>
                                                </div>
                                                
                                                <div class="text-xs text-gray-400 dark:text-gray-500 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                                                    <div>Created: {{ $data->created_at->toDateTimeString() }}</div>
                                                    <div>ID Riwayat: {{ $data->id }}</div>
                                                </div>

                                            {{-- ====================== TIPE 2: AUDIT (SYSTEM LOG) ====================== --}}
                                            @else
                                                @php
                                                    $payload = is_array($data->payload) ? $data->payload : json_decode($data->payload, true) ?? [];
                                                @endphp

                                                <div class="mb-4">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tipe</div>
                                                    <div class="font-bold text-gray-900 dark:text-white text-lg">{{ ucfirst(str_replace(['.','_'], ' ', $data->type)) }}</div>
                                                </div>

                                                @if(!empty($payload))
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Rincian</div>
                                                        <div class="text-sm">
                                                            <div class="space-y-3">
                                                                {{-- LOGIKA KHUSUS: Jika Payload strukturnya adalah Old vs New (seperti Edit Transaksi) --}}
                                                                @if(isset($payload['old']) && isset($payload['new']))
                                                                    <div class="grid grid-cols-2 gap-4">
                                                                        
                                                                        {{-- KOLOM LAMA (OLD) --}}
                                                                        <div class="bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-100 dark:border-red-900/30">
                                                                            <div class="text-xs font-bold text-red-500 dark:text-red-400 uppercase mb-2 border-b border-red-200 dark:border-red-800 pb-1">Sebelum (Old)</div>
                                                                            @foreach($payload['old'] as $subKey => $subVal)
                                                                                <div class="text-xs mb-2 last:mb-0">
                                                                                    {{-- MODIFIKASI 1: Ubah label di mode Grid --}}
                                                                                    <span class="font-semibold text-gray-600 dark:text-gray-400 block">
                                                                                        {{ strtolower($subKey) == 'warehouses' ? 'Akun' : ucfirst(str_replace('_', ' ', $subKey)) }}:
                                                                                    </span>
                                                                                    <div class="text-gray-800 dark:text-gray-200 break-words mt-0.5 bg-white dark:bg-gray-800 p-1 rounded border border-red-100 dark:border-red-900/30">
                                                                                        {{ is_array($subVal) ? json_encode($subVal) : ($subVal ?? '-') }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                        {{-- KOLOM BARU (NEW) --}}
                                                                        <div class="bg-green-50 dark:bg-green-900/10 p-3 rounded-lg border border-green-100 dark:border-green-900/30">
                                                                            <div class="text-xs font-bold text-green-500 dark:text-green-400 uppercase mb-2 border-b border-green-200 dark:border-green-800 pb-1">Sesudah (New)</div>
                                                                            @foreach($payload['new'] as $subKey => $subVal)
                                                                                <div class="text-xs mb-2 last:mb-0">
                                                                                    {{-- MODIFIKASI 2: Ubah label di mode Grid --}}
                                                                                    <span class="font-semibold text-gray-600 dark:text-gray-400 block">
                                                                                        {{ strtolower($subKey) == 'warehouses' ? 'Akun' : ucfirst(str_replace('_', ' ', $subKey)) }}:
                                                                                    </span>
                                                                                    <div class="text-gray-800 dark:text-gray-200 font-medium break-words mt-0.5 bg-white dark:bg-gray-800 p-1 rounded border border-green-100 dark:border-green-900/30">
                                                                                        {{ is_array($subVal) ? json_encode($subVal) : ($subVal ?? '-') }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>

                                                                {{-- LOGIKA UMUM: Loop biasa (List View) --}}
                                                                @else
                                                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg border border-gray-100 dark:border-gray-600 space-y-2">
                                                                        @foreach($payload as $k => $v)
                                                                            <div class="text-sm flex flex-col sm:flex-row sm:items-start gap-1 border-b border-gray-200 dark:border-gray-600 last:border-0 pb-2 last:pb-0">
                                                                                
                                                                                {{-- MODIFIKASI 3: Ubah label di mode List --}}
                                                                                <div class="min-w-[120px] text-xs font-bold text-gray-500 dark:text-gray-400 uppercase pt-1">
                                                                                    {{ strtolower($k) == 'warehouses' ? 'Akun' : str_replace('_', ' ', $k) }}
                                                                                </div>

                                                                                {{-- Value Content --}}
                                                                                <div class="flex-1">
                                                                                    @if(is_array($v) && isset($v['old']) && isset($v['new']))
                                                                                        {{-- Tampilan Perubahan Spesifik (Style Panah) --}}
                                                                                        <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                                                                            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs line-through decoration-red-700/50" title="Lama">
                                                                                                {{ $v['old'] }}
                                                                                            </span>
                                                                                            <span class="text-gray-400">→</span>
                                                                                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-bold" title="Baru">
                                                                                                {{ $v['new'] }}
                                                                                            </span>
                                                                                        </div>

                                                                                    @elseif($k === 'grand_total' || str_contains($k, 'price'))
                                                                                        {{-- Format Uang --}}
                                                                                        <span class="font-mono font-bold text-gray-700 dark:text-gray-200">$ {{ number_format($v, 2) }}</span>

                                                                                    @elseif(is_array($v))
                                                                                        {{-- Jika Array biasa, tampilkan sebagai JSON code block kecil --}}
                                                                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-300 break-all border border-gray-200 dark:border-gray-600">
                                                                                            {{ json_encode($v) }}
                                                                                        </code>

                                                                                    @else
                                                                                        {{-- Teks Biasa --}}
                                                                                        <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $v }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="mt-4">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Keterangan</div>
                                                    <div class="text-sm text-gray-800 dark:text-gray-200 p-2 bg-gray-50 dark:bg-gray-700/30 rounded border border-gray-100 dark:border-gray-700">
                                                        {{ $data->reason ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-4 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                                                    <div>Created: {{ $data->created_at->toDateTimeString() }}</div>
                                                    <div>ID Audit: {{ $data->id }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-lg font-medium">Belum ada riwayat aktivitas.</p>
                                            <p class="text-sm opacity-70">Semua perubahan stok & harga akan tercatat di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="history-sidebar" class="fixed inset-0 z-50 hidden">
        <div id="history-sidebar-backdrop" class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm transition-opacity"></div>
        
        <aside id="history-sidebar-panel" class="absolute right-0 top-0 h-full w-full md:w-1/3 bg-white dark:bg-gray-800 transform translate-x-full transition-transform duration-300 shadow-2xl overflow-y-auto border-l border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Detail Riwayat</h3>
                <button id="history-sidebar-close" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div id="history-sidebar-body" class="p-6 text-sm text-gray-700 dark:text-gray-300">
                {{-- KONTEN AKAN DIMASUKKAN VIA JS --}}
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup TomSelect
            new TomSelect("#select-item", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['clear_button']
            });

            // Setup Sidebar
            const sidebar = document.getElementById('history-sidebar');
            const panel = document.getElementById('history-sidebar-panel');
            const body = document.getElementById('history-sidebar-body');
            const closeBtn = document.getElementById('history-sidebar-close');
            const backdrop = document.getElementById('history-sidebar-backdrop');

            function openSidebar(html) {
                body.innerHTML = html;
                sidebar.classList.remove('hidden');
                // Timeout kecil agar transisi CSS berjalan
                setTimeout(() => panel.classList.remove('translate-x-full'), 10);
            }

            function closeSidebar() {
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                    body.innerHTML = '';
                }, 300); // Sesuaikan dengan durasi transition CSS
            }

            closeBtn.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSidebar(); });

            // Event listener untuk baris tabel
            document.querySelectorAll('.history-summary').forEach(function(row) {
                row.addEventListener('click', function() {
                    const detailRow = row.nextElementSibling;
                    if (!detailRow || !detailRow.classList.contains('history-detail')) return;
                    
                    // Ambil konten dari cell di dalam hidden row
                    const cell = detailRow.querySelector('td');
                    if (cell) openSidebar(cell.innerHTML);
                });
            });
        });
    </script>
</x-app-layout>