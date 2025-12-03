<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    {{-- CSS KHUSUS TOMSELECT DARK MODE --}}
    <style>
        /* Base Dark Mode for TomSelect */
        .dark .ts-control {
            background-color: #111827 !important; /* gray-900 */
            border-color: #374151 !important;     /* border-gray-700 */
            color: #f3f4f6 !important;            /* text-gray-100 */
            border-radius: 0.5rem;
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
        
        /* TomSelect Multi Item Tag Style */
        .dark .ts-wrapper.multi .ts-control > div {
            background-color: #374151 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }

        /* Light Mode Default Override */
        .ts-wrapper.multi .ts-control > div { 
            background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 4px; 
        }
        
        /* Alpine Cloak */
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Transaksi Baru') }}</h2>
    </x-slot>

    <div class="py-12" x-data="transactionApp()" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-6 rounded shadow-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form id="transactionForm" method="POST" action="{{ route('transactions.store') }}" @submit.prevent="checkBeforeSubmit">
                @csrf
                <input type="hidden" name="invoice_code" x-model="currentInvoiceCode">
                <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="cart_items" id="cart_items_input">
                <input type="hidden" name="type" x-model="transactionType">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- PANEL KIRI: KONTROL --}}
                    <div class="md:col-span-1 space-y-6">
                        
                        {{-- Card Jenis Transaksi --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Jenis Transaksi</h3>
                            <div class="flex rounded-xl shadow-sm">
                                <button type="button" @click="setMode('out')"
                                    :class="transactionType === 'out' ? 'bg-indigo-600 text-white shadow-inner' : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                    class="px-4 py-2.5 text-sm font-bold border border-transparent rounded-l-xl w-1/2 transition">
                                    📤 Penjualan
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" @click="setMode('in')"
                                        :class="transactionType === 'in' ? 'bg-green-600 text-white shadow-inner' : 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'"
                                        class="px-4 py-2.5 text-sm font-bold border border-transparent rounded-r-xl w-1/2 transition">
                                        📥 Pembelian
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Card Pilih Barang --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Pilih Barang</h3>
                            <div class="mb-4">
                                <select id="item_select" class="w-full" placeholder="Cari Barang..." autocomplete="off">
                                    <option value="">Cari Barang...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-name="{{ $item->name }}"
                                            data-code="{{ $item->code }}" data-sell-price="{{ $item->sell_price }}"
                                            data-buy-price="{{ $item->buy_price }}" data-stock="{{ $item->stock }}"
                                            data-warehouses="{{ $item->warehouses->pluck('name')->join(',') }}">
                                            {{ $item->code }} - {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" @click="addToCart()"
                                class="w-full bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition transform active:scale-95">
                                + Masukkan ke Keranjang
                            </button>
                        </div>

                        {{-- Card Target Market (Hanya OUT) --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300"
                            x-show="transactionType === 'out'" x-transition>
                            <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">Target Market / Customer <span class="text-red-500">*</span></label>
                            <input class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5 transition shadow-sm" type="text" name="market" placeholder="Contoh: Pelanggan A...">
                        </div>
                    </div>

                    {{-- PANEL KANAN: KERANJANG --}}
                    <div class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Keranjang Belanja</h3>
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded mt-1 inline-block" x-text="currentInvoiceCode"></span>
                            </div>
                            <div class="text-right">
                                <span x-show="transactionType === 'out'" class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 text-xs font-bold px-3 py-1.5 rounded-lg border border-indigo-200 dark:border-indigo-800">PENJUALAN</span>
                                <span x-show="transactionType === 'in'" class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-bold px-3 py-1.5 rounded-lg border border-green-200 dark:border-green-800">PEMBELIAN</span>
                            </div>
                        </div>

                        {{-- TABEL ITEM --}}
                        <!-- <div class="flex-grow overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl mb-6 custom-scrollbar"> -->
                        <div class="flex-grow overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg min-h-[300px] mb-6 custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/30">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Barang</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">Lokasi/Akun</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/10">
                                            <span x-text="transactionType === 'out' ? 'Ref. Modal' : 'Ref. Jual'"></span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in cart" :key="item.ui_id">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            {{-- Nama Barang --}}
                                            <td class="px-4 py-3 align-top">
                                                <div class="font-bold text-sm text-gray-900 dark:text-white" x-text="item.name"></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5" x-text="item.code"></div>
                                                <div x-show="transactionType === 'out'" class="text-[10px] font-bold text-red-500 dark:text-red-400 mt-1 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded inline-block" x-text="'Sisa Stok: ' + (item.max_stock - item.qty)"></div>
                                            </td>

                                            {{-- Lokasi TomSelect --}}
                                            <td class="px-4 py-3 text-xs text-gray-600 align-top">
                                                <div x-init="initRowTomSelect($el, item)">
                                                    <select multiple placeholder="Pilih Lokasi..." class="text-xs w-full"></select>
                                                </div>
                                            </td>

                                            {{-- Input Harga --}}
                                            <td class="px-4 py-3 align-top">
                                                <input type="number" step="0.01" x-model="item.price" @input="updateTotal()" 
                                                    class="w-24 p-1.5 text-sm font-bold border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-right shadow-sm">
                                            </td>

                                            {{-- Ref Harga --}}
                                            <td class="px-4 py-3 align-top text-right text-sm bg-yellow-50 dark:bg-yellow-900/10 border-l border-r border-yellow-100 dark:border-yellow-900/20">
                                                <div class="text-gray-700 dark:text-gray-300 font-mono" x-text="formatCurrency(item.audit_price)"></div>
                                            </td>

                                            {{-- Input Qty --}}
                                            <td class="px-4 py-3 align-top">
                                                <input type="number" x-model="item.qty" @input="updateTotal()" min="1" 
                                                    class="w-16 p-1.5 text-sm font-bold border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-center shadow-sm">
                                            </td>

                                            {{-- Subtotal --}}
                                            <td class="px-4 py-3 align-top text-right font-bold text-sm text-gray-900 dark:text-white font-mono" x-text="formatCurrency(item.price * item.qty)"></td>
                                            
                                            {{-- Hapus --}}
                                            <td class="px-4 py-3 align-top text-center">
                                                <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/30">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="cart.length === 0">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                            Keranjang masih kosong. Silakan pilih barang di sebelah kiri.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- FOOTER KERANJANG --}}
                        <div class="mt-auto border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex justify-between items-end mb-6">
                                <div class="text-gray-500 dark:text-gray-400 text-sm">
                                    Total Item: <span class="font-bold text-gray-800 dark:text-white text-base ml-1" x-text="cart.length"></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Pembayaran</p>
                                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400" x-text="formatCurrency(grandTotal)"></p>
                                </div>
                            </div>
                            
                            <div class="w-full mb-6">
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                                <textarea name="description" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" rows="2" placeholder="Tambahkan catatan jika perlu..."></textarea>
                            </div>
                            
                            <div class="flex gap-4">
                                <a href="{{ route('transactions.index') }}" class="w-1/3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-3.5 px-4 rounded-xl text-center transition">
                                    Batal
                                </a>
                                <button type="submit" :disabled="cart.length === 0" class="w-2/3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition transform active:scale-95 flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL PERINGATAN (POP UP) --}}
                <div x-show="showWarningModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl z-10 overflow-hidden transform transition-all border border-gray-100 dark:border-gray-700">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-5 border-b border-yellow-100 dark:border-yellow-900/30 flex items-center gap-4">
                            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/40 rounded-full">
                                <span class="text-2xl">⚠️</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-500">Konfirmasi Perubahan Data Lokasi</h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-600/80">Tindakan ini akan <b>mengubah data master lokasi barang</b> secara permanen.</p>
                            </div>
                        </div>
                        
                        <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                            <p class="text-gray-700 dark:text-gray-300 mb-4 text-sm">Berikut adalah barang yang lokasi akunnya berbeda dengan data master:</p>
                            
                            <template x-for="msg in warningMessages">
                                <div class="mb-4 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700/50">
                                    <div class="font-bold text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <span x-text="msg.item"></span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi Lama (Master)</span>
                                            <div class="text-gray-600 dark:text-gray-400 font-mono bg-white dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-xs" x-text="msg.old || '-'"></div>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-yellow-600 dark:text-yellow-500 uppercase tracking-wider mb-1">Lokasi Baru (Input)</span>
                                            <div class="text-gray-800 dark:text-gray-200 font-mono bg-yellow-50 dark:bg-yellow-900/20 px-3 py-2 rounded-lg border border-yellow-200 dark:border-yellow-900/30 text-xs font-bold" x-text="msg.new || '-'"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mt-6 text-center">Apakah Anda yakin ingin menyimpan perubahan ini ke Master Data?</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="showWarningModal = false" class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-bold text-sm transition shadow-sm">
                                Batal & Periksa Lagi
                            </button>
                            <button type="button" @click="confirmSubmit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/20 transition transform active:scale-95">
                                Ya, Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        const globalWarehouses = @json($warehouses);

        function transactionApp() {
            return {
                cart: [], grandTotal: 0, transactionType: 'out', tomSelectInstance: null,
                todayPrefix: '{{ $todayPrefix }}', seqString: '{{ $seqString }}',
                showWarningModal: false, warningMessages: [],

                get currentInvoiceCode() { return `INV-${this.transactionType === 'out' ? 'OUT' : 'IN'}-${this.todayPrefix}-${this.seqString}`; },

                init() {
                    this.tomSelectInstance = new TomSelect("#item_select", {
                        create: false, sortField: { field: "text", direction: "asc" }, placeholder: "Ketik Kode atau Nama..."
                    });
                },

                initRowTomSelect(el, item) {
                    const selectEl = el.querySelector('select');
                    globalWarehouses.forEach(wh => {
                        const opt = document.createElement('option'); opt.value = wh.name; opt.text = wh.name; selectEl.appendChild(opt);
                    });

                    if (item.warehouses) {
                        const existingValues = item.warehouses.split(',').map(s => s.trim());
                        existingValues.forEach(val => {
                             if (![...selectEl.options].some(o => o.value === val)) {
                                 const opt = document.createElement('option'); opt.value = val; opt.text = val; selectEl.appendChild(opt);
                             }
                             const optionToSelect = [...selectEl.options].find(o => o.value === val);
                             if (optionToSelect) optionToSelect.selected = true;
                        });
                    }

                    new TomSelect(selectEl, {
                        plugins: ['remove_button'], create: true, persist: false, 
                        onChange: (val) => { item.warehouses = Array.isArray(val) ? val.join(',') : val; }
                    });
                },

                setMode(mode) {
                    if (this.cart.length > 0 && !confirm('Reset keranjang?')) return;
                    this.transactionType = mode; this.cart = []; this.grandTotal = 0; this.tomSelectInstance.clear();
                },

                addToCart() {
                    const val = this.tomSelectInstance.getValue();
                    if (!val) return;
                    const opt = document.querySelector(`#item_select option[value="${val}"]`);

                    const stock = parseInt(opt.getAttribute('data-stock')) || 0;
                    const sell = parseFloat(opt.getAttribute('data-sell-price')) || 0;
                    const buy = parseFloat(opt.getAttribute('data-buy-price')) || 0;
                    const wh = opt.getAttribute('data-warehouses') || '';

                    let price = (this.transactionType === 'out') ? sell : buy;
                    let audit = (this.transactionType === 'out') ? buy : sell;

                    if (this.transactionType === 'out' && stock <= 0) { alert('Stok Habis!'); this.tomSelectInstance.clear(); return; }

                    const exist = this.cart.find(i => i.id === val);
                    if (exist) {
                        if (this.transactionType === 'out' && exist.qty >= stock) alert('Stok tidak mencukupi!');
                        else exist.qty++;
                    } else {
                        this.cart.push({
                            ui_id: Date.now(), id: val, name: opt.getAttribute('data-name'), code: opt.getAttribute('data-code'),
                            price: price, audit_price: audit, warehouses: wh, original_warehouses: wh, qty: 1, max_stock: stock, subtotal: price
                        });
                    }
                    this.updateTotal();
                    this.tomSelectInstance.clear();
                },

                removeItem(idx) { this.cart.splice(idx, 1); this.updateTotal(); },

                updateTotal() { this.grandTotal = 0; this.cart.forEach(i => { i.subtotal = i.price * i.qty; this.grandTotal += i.subtotal; }); },

                formatCurrency(number) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }).format(number); },

                checkBeforeSubmit() {
                    this.warningMessages = [];
                    for (let item of this.cart) {
                        let inputArr = item.warehouses ? item.warehouses.split(',').map(s => s.trim()).filter(s => s !== "").sort() : [];
                        let originalArr = item.original_warehouses ? item.original_warehouses.split(',').map(s => s.trim()).filter(s => s !== "").sort() : [];

                        if (JSON.stringify(inputArr) !== JSON.stringify(originalArr)) {
                            this.warningMessages.push({ item: `${item.name} (${item.code})`, old: originalArr.join(', '), new: inputArr.join(', ') });
                        }
                    }

                    if (this.warningMessages.length > 0) { this.showWarningModal = true; } 
                    else { this.confirmSubmit(); }
                },

                confirmSubmit() {
                    document.getElementById('cart_items_input').value = JSON.stringify(this.cart);
                    document.getElementById('transactionForm').submit();
                }
            };
        }
    </script>
</x-app-layout>