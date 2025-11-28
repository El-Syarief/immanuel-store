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
                                                            <div class="text-xs text-gray-500 mt-1">Gudang: {{ $data->item->warehouses->pluck('name')->join(', ') }}</div>
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
                                                                    <span class="line-through text-gray-400">Rp {{ number_format($data->old_buy_price, 0, ',', '.') }}</span>
                                                                    <span class="mx-2">→</span>
                                                                    <span class="text-blue-600 font-bold">Rp {{ number_format($data->new_buy_price, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                            @if($data->old_sell_price != $data->new_sell_price)
                                                                <div class="text-xs">
                                                                    <span class="font-semibold">Jual:</span>
                                                                    <span class="line-through text-gray-400">Rp {{ number_format($data->old_sell_price, 0, ',', '.') }}</span>
                                                                    <span class="mx-2">→</span>
                                                                    <span class="text-green-600 font-bold">Rp {{ number_format($data->new_sell_price, 0, ',', '.') }}</span>
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
                                                        <div class="text-xs text-gray-500">Gudang / Market</div>
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
                                                            @foreach($payload as $k => $v)
                                                                <div><span class="font-semibold text-gray-600">{{ ucfirst($k) }}:</span> <span class="text-gray-800">{{ is_array($v) ? json_encode($v) : $v }}</span></div>
                                                            @endforeach
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
        });
    </script>
</x-app-layout>