<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* 1. Menyamakan Border Radius (rounded-lg = 0.5rem) & Warna Border */
        .ts-control {
            border-radius: 0.5rem !important; /* rounded-lg */
            border-color: #d1d5db !important; /* border-gray-300 */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            min-height: 42px; /* Menyamakan tinggi dengan input sebelah */
            display: flex;
            align-items: center;
        }

        /* 2. Menghilangkan shadow biru bawaan TomSelect saat aktif agar mirip input biasa */
        .ts-wrapper.focus .ts-control {
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5) !important; /* ring-indigo-500 */
            border-color: #6366f1 !important; /* border-indigo-500 */
        }

        /* 3. Merapikan posisi text di dalam */
        .ts-dropdown {
            border-radius: 0.5rem;
            margin-top: 4px;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Aktivitas (Audit Log)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('histories.index') }}" id="filterFormHistory" class="grid grid-cols-1 md:grid-cols-12 md:grid-cols-12 gap-4 items-end">
                    
                    {{-- 1. PILIH BARANG (TomSelect) --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Filter Barang</label>
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
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Kata Kunci / Aktivitas</label>
                        <input type="text" name="search_text" value="{{ request('search_text') }}" placeholder="Cth: Koreksi, Edit, Hapus..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 h-[42px]"> 
                        {{-- h-[42px] agar tingginya sama dengan TomSelect --}}
                    </div>

                    {{-- 3. AKTOR --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Aktor</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 h-[42px]">
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
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Tanggal</label>
                        <div class="flex gap-1">
                            <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full border-gray-300 rounded-lg text-sm px-1 focus:ring-indigo-500 focus:border-indigo-500 h-[42px]" title="Dari Tanggal">
                            <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full border-gray-300 rounded-lg text-sm px-1 focus:ring-indigo-500 focus:border-indigo-500 h-[42px]" title="Sampai Tanggal">
                        </div>
                    </div>

                    {{-- 5. TOMBOL --}}
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition w-full shadow-md h-[42px]">
                            Cari
                        </button>
                        @if(request()->hasAny(['item_id', 'user_id', 'date_start', 'date_end', 'search_text']))
                            <a href="{{ route('histories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-3 rounded-lg text-sm transition flex items-center justify-center border border-gray-300 h-[42px]" title="Reset Filter">
                                ✕
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
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($histories as $entry)
                                @php
                                    $kind = $entry->kind;
                                    $data = $entry->data;
                                @endphp

                                <tr class="history-summary hover:bg-blue-50 transition duration-150 ease-in-out cursor-pointer">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-medium text-gray-900">{{ $entry->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $entry->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($kind === 'history')
                                            <div class="text-sm font-bold text-gray-800">{{ $data->user->name ?? 'System' }}</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $data->user->role ?? '-' }}</span>
                                        @else
                                            <div class="text-sm font-bold text-gray-800">{{ $data->actor->name ?? 'System' }}</div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $data->actor->role ?? '-' }}</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $kind === 'history' ? $data->reason : ($data->reason ?? json_encode($data->payload)) }}">
                                        @if($kind === 'history')
                                            {{ $data->reason ?? 'Update Manual' }}
                                        @else
                                            @php
                                                $summary = ucfirst(str_replace(['.','_'], ' ', $data->type));
                                                $payload = is_array($data->payload) ? $data->payload : json_decode($data->payload, true) ?? [];
                                            @endphp
                                            {{ $summary }} @if(!empty($payload['invoice_code'])) - {{ $payload['invoice_code'] }} @endif
                                        @endif
                                    </td>
                                </tr>

                                {{-- Detail row (hidden by default) --}}
                                <tr class="history-detail hidden bg-gray-50">
                                    <td colspan="3" class="px-6 py-4">
                                        <div class="text-sm text-gray-700 space-y-3">
                                            @if($kind === 'history')
                                                {{-- Item info (if available) --}}
                                                @if($data->item)
                                                    <div>
                                                        <div class="text-xs text-gray-500">Barang</div>
                                                        <div class="font-bold text-indigo-600">{{ $data->item->name }} <span class="text-gray-500 font-mono">({{ $data->item->code }})</span></div>
                                                        @if($data->item->warehouses && $data->item->warehouses->count())
                                                            <div class="text-xs text-gray-500 mt-1">Akun: {{ $data->item->warehouses->pluck('name')->join(', ') }}</div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Price changes --}}
                                                @if($data->old_buy_price != $data->new_buy_price || $data->old_sell_price != $data->new_sell_price)
                                                    <div>
                                                        <div class="text-xs text-gray-500">Perubahan Harga</div>
                                                        <div class="flex flex-col gap-1">
                                                            @if($data->old_buy_price != $data->new_buy_price)
                                                                <div class="text-xs">
                                                                    <span class="font-semibold">Modal:</span>
                                                                    <span class="line-through text-gray-400">$ {{ number_format($data->old_buy_price, 2, '.', ',') }}</span>
                                                                    <span class="mx-2">→</span>
                                                                    <span class="text-blue-600 font-bold">$ {{ number_format($data->new_buy_price, 2, '.', ',') }}</span>
                                                                </div>
                                                            @endif
                                                            @if($data->old_sell_price != $data->new_sell_price)
                                                                <div class="text-xs">
                                                                    <span class="font-semibold">Jual:</span>
                                                                    <span class="line-through text-gray-400">$ {{ number_format($data->old_sell_price, 2, '.', ',') }}</span>
                                                                    <span class="mx-2">→</span>
                                                                    <span class="text-green-600 font-bold">$ {{ number_format($data->new_sell_price, 2, '.', ',') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Stock changes --}}
                                                @if($data->old_stock != $data->new_stock)
                                                    <div>
                                                        <div class="text-xs text-gray-500">Perubahan Stok</div>
                                                        <div class="text-sm">
                                                            <span class="text-gray-500">{{ $data->old_stock }}</span>
                                                            <span class="mx-2">→</span>
                                                            <span class="font-bold {{ $data->new_stock > $data->old_stock ? 'text-green-600' : 'text-red-600' }}">{{ $data->new_stock }}</span>
                                                            <span class="ml-2 text-xs px-1 py-0.5 rounded {{ $data->new_stock > $data->old_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                {{ $data->new_stock - $data->old_stock > 0 ? '+' : '' }}{{ $data->new_stock - $data->old_stock }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Market / Warehouse changes --}}
                                                @if(isset($data->old_market) && $data->old_market != $data->new_market)
                                                    <div>
                                                        <div class="text-xs text-gray-500">Akun / Market</div>
                                                        <div class="text-sm">
                                                            <span class="line-through text-gray-400">{{ $data->old_market ?? '-' }}</span>
                                                            <span class="mx-2">→</span>
                                                            <span class="font-bold text-purple-600">{{ $data->new_market ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Reason and raw details --}}
                                                <div>
                                                    <div class="text-xs text-gray-500">Keterangan</div>
                                                    <div class="text-sm text-gray-800">{{ $data->reason ?? '-' }}</div>
                                                </div>

                                                {{-- Fallback: show JSON-ish raw fields for advanced inspection --}}
                                                <div class="text-xs text-gray-400">
                                                    <div>Created: {{ $data->created_at->toDateTimeString() }}</div>
                                                    <div>ID Riwayat: {{ $data->id }}</div>
                                                </div>

                                            @else
                                                {{-- Audit detail rendering --}}
                                                @php
                                                    $payload = is_array($data->payload) ? $data->payload : json_decode($data->payload, true) ?? [];
                                                @endphp

                                                <div>
                                                    <div class="text-xs text-gray-500">Tipe</div>
                                                    <div class="font-bold">{{ ucfirst(str_replace(['.','_'], ' ', $data->type)) }}</div>
                                                </div>

                                                @if(!empty($payload))
                                                    <div>
                                                        <div class="text-xs text-gray-500">Rincian</div>
                                                        <div class="text-sm">
                                                            <div class="space-y-3">
                                                                {{-- LOGIKA KHUSUS: Jika Payload strukturnya adalah Old vs New (seperti Edit Transaksi) --}}
                                                                @if(isset($payload['old']) && isset($payload['new']))
                                                                    <div class="grid grid-cols-2 gap-4">
                                                                        <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                                                            <div class="text-xs font-bold text-red-500 uppercase mb-2">Sebelum (Old)</div>
                                                                            @foreach($payload['old'] as $subKey => $subVal)
                                                                                <div class="text-xs mb-1">
                                                                                    {{-- MODIFIKASI 1: Ubah label di mode Grid --}}
                                                                                    <span class="font-semibold text-gray-600">
                                                                                        {{ strtolower($subKey) == 'warehouses' ? 'Akun' : ucfirst(str_replace('_', ' ', $subKey)) }}:
                                                                                    </span>
                                                                                    <div class="text-gray-800 break-words">{{ is_array($subVal) ? json_encode($subVal) : $subVal }}</div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                                                            <div class="text-xs font-bold text-green-500 uppercase mb-2">Sesudah (New)</div>
                                                                            @foreach($payload['new'] as $subKey => $subVal)
                                                                                <div class="text-xs mb-1">
                                                                                    {{-- MODIFIKASI 2: Ubah label di mode Grid --}}
                                                                                    <span class="font-semibold text-gray-600">
                                                                                        {{ strtolower($subKey) == 'warehouses' ? 'Akun' : ucfirst(str_replace('_', ' ', $subKey)) }}:
                                                                                    </span>
                                                                                    <div class="text-gray-800 font-medium break-words">{{ is_array($subVal) ? json_encode($subVal) : $subVal }}</div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>

                                                                {{-- LOGIKA UMUM: Loop biasa (seperti Edit Barang / Screenshot kamu saat ini) --}}
                                                                @else
                                                                    @foreach($payload as $k => $v)
                                                                        <div class="text-sm border-b border-gray-100 last:border-0 pb-2 last:pb-0">
                                                                            <div class="flex flex-col sm:flex-row sm:items-start gap-1">
                                                                                
                                                                                {{-- MODIFIKASI 3: Ubah label di mode List (Arrow) --}}
                                                                                <div class="min-w-[120px] text-xs font-bold text-gray-500 uppercase pt-1">
                                                                                    {{ strtolower($k) == 'warehouses' ? 'Akun' : str_replace('_', ' ', $k) }}
                                                                                </div>

                                                                                {{-- Value Content --}}
                                                                                <div class="flex-1">
                                                                                    @if(is_array($v) && isset($v['old']) && isset($v['new']))
                                                                                        {{-- Tampilan Perubahan Spesifik (Style Panah) --}}
                                                                                        <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                                                                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs line-through decoration-red-700/50" title="Lama">
                                                                                                {{ $v['old'] }}
                                                                                            </span>
                                                                                            <span class="text-gray-400">→</span>
                                                                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold" title="Baru">
                                                                                                {{ $v['new'] }}
                                                                                            </span>
                                                                                        </div>

                                                                                    @elseif($k === 'grand_total' || str_contains($k, 'price'))
                                                                                        {{-- Format Uang --}}
                                                                                        <span class="font-mono font-bold text-gray-700">$ {{ number_format($v, 2) }}</span>

                                                                                    @elseif(is_array($v))
                                                                                        {{-- Jika Array biasa, tampilkan sebagai JSON code block kecil --}}
                                                                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600 break-all">
                                                                                            {{ json_encode($v) }}
                                                                                        </code>

                                                                                    @else
                                                                                        {{-- Teks Biasa --}}
                                                                                        <span class="text-gray-800 font-medium">{{ $v }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div>
                                                    <div class="text-xs text-gray-500">Keterangan</div>
                                                    <div class="text-sm text-gray-800">{{ $data->reason ?? '-' }}</div>
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    <div>Created: {{ $data->created_at->toDateTimeString() }}</div>
                                                    <div>ID Audit: {{ $data->id }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-500">
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

</div>

    <!-- Sidebar for history detail (slide-in from right) -->
    <div id="history-sidebar" class="fixed inset-0 z-50 hidden">
        <div id="history-sidebar-backdrop" class="absolute inset-0 bg-black opacity-40"></div>
        <aside id="history-sidebar-panel" class="absolute right-0 top-0 h-full w-full md:w-1/3 bg-white transform translate-x-full transition-transform duration-200 shadow-xl overflow-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-bold text-lg">Detail Riwayat</h3>
                <button id="history-sidebar-close" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>
            <div id="history-sidebar-body" class="p-6 text-sm text-gray-700"></div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('history-sidebar');
            const panel = document.getElementById('history-sidebar-panel');
            const body = document.getElementById('history-sidebar-body');
            const closeBtn = document.getElementById('history-sidebar-close');
            const backdrop = document.getElementById('history-sidebar-backdrop');

            function openSidebar(html) {
                body.innerHTML = html;
                sidebar.classList.remove('hidden');
                // allow next tick for transition
                requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
            }

            function closeSidebar() {
                panel.classList.add('translate-x-full');
                // wait for transition then hide
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                    body.innerHTML = '';
                }, 220);
            }

            closeBtn.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSidebar(); });

            document.querySelectorAll('.history-summary').forEach(function(row) {
                row.addEventListener('click', function() {
                    const detail = row.nextElementSibling;
                    if (!detail) return;
                    const cell = detail.querySelector('td');
                    if (!cell) return;
                    openSidebar(cell.innerHTML);
                });
            });

            new TomSelect("#select-item", {
                // maxItems: 1,
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['clear_button']
            });
        });
    </script>
</x-app-layout>