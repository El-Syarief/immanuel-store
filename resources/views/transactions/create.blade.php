<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        .ts-wrapper.multi .ts-control>div { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 4px; }
        /* Animasi Modal */
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Transaksi Baru') }}</h2>
    </x-slot>

    <div class="py-12" x-data="transactionApp()" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
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

                    {{-- PANEL KIRI: Pilih Barang --}}
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Jenis Transaksi</h3>
                            <div class="flex rounded-md shadow-sm">
                                <button type="button" @click="setMode('out')"
                                    :class="transactionType === 'out' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border rounded-l-lg w-1/2 transition">📤 Penjualan (OUT)</button>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" @click="setMode('in')"
                                        :class="transactionType === 'in' ? 'bg-green-600 text-white' : 'bg-white text-gray-700'"
                                        class="px-4 py-2 text-sm font-medium border rounded-r-lg w-1/2 transition">📥 Pembelian (IN)</button>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Pilih Barang</h3>
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
                            <button type="button" @click="addToCart()"
                                class="mt-4 w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">+ Masukkan</button>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100"
                            x-show="transactionType === 'out'" x-transition>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Target Market / Customer <span class="text-red-500">*</span></label>
                            <input class="border-gray-300 rounded-md shadow-sm w-full" type="text" name="market" placeholder="Contoh: Pelanggan A...">
                        </div>
                    </div>

                    {{-- PANEL KANAN: Keranjang --}}
                    <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Keranjang Belanja</h3>
                                <span class="text-xs font-mono text-gray-500" x-text="currentInvoiceCode"></span>
                            </div>
                            <div class="text-right">
                                <span x-show="transactionType === 'out'" class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded">PENJUALAN</span>
                                <span x-show="transactionType === 'in'" class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">PEMBELIAN</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto mb-6 border rounded-lg min-h-[300px]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barang</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-1/4">Lokasi/Akun</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Harga</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase bg-yellow-50 border-l border-r border-yellow-100">
                                            <span x-text="transactionType === 'out' ? 'Ref. Modal' : 'Ref. Jual'"></span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in cart" :key="item.ui_id">
                                        <tr>
                                            <td class="px-4 py-3 align-top">
                                                <div class="font-bold text-sm" x-text="item.name"></div>
                                                <div class="text-xs text-gray-500" x-text="item.code"></div>
                                                <div x-show="transactionType === 'out'" class="text-xs text-red-500" x-text="'Sisa: ' + (item.max_stock - item.qty)"></div>
                                            </td>

                                            <td class="px-4 py-3 text-xs text-gray-600 align-top">
                                                {{-- KITA AKTIFKAN TOMSELECT DI KEDUA MODE (IN & OUT) --}}
                                                <div x-init="initRowTomSelect($el, item)">
                                                    <select multiple placeholder="Pilih Lokasi..." class="text-xs">
                                                        {{-- Option via JS --}}
                                                    </select>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 align-top"><input type="number" step="0.01" x-model="item.price" @input="updateTotal()" class="w-24 p-1 text-sm border rounded"></td>
                                            <td class="px-4 py-3 align-top text-right text-sm bg-yellow-50 border-l border-r border-yellow-100">
                                                <div x-text="formatCurrency(item.audit_price)"></div>
                                            </td>
                                            <td class="px-4 py-3 align-top"><input type="number" x-model="item.qty" @input="updateTotal()" min="1" class="w-16 p-1 text-sm border rounded text-center"></td>
                                            <td class="px-4 py-3 align-top text-right font-bold text-sm" x-text="formatCurrency(item.price * item.qty)"></td>
                                            <td class="px-4 py-3 align-top text-center"><button type="button" @click="removeItem(index)" class="text-red-500 font-bold">X</button></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-gray-600">Total Item: <span class="font-bold" x-text="cart.length"></span></div>
                                <div class="text-2xl font-bold text-gray-800">Total: <span class="text-indigo-600" x-text="formatCurrency(grandTotal)"></span></div>
                            </div>
                            <div class="w-full mb-4">
                                <label class="block font-medium text-sm text-gray-700">Catatan</label>
                                <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm text-sm" rows="2"></textarea>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('transactions.index') }}" class="w-1/3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg text-center">Batal</a>
                                <button type="submit" :disabled="cart.length === 0" class="w-2/3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg disabled:opacity-50">Simpan Transaksi</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL PERINGATAN (POP UP) --}}
                <div x-show="showWarningModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>

                    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl z-10 overflow-hidden transform transition-all">
                        <div class="bg-yellow-50 px-6 py-4 border-b border-yellow-200 flex items-center gap-3">
                            <span class="text-3xl">⚠️</span>
                            <div>
                                <h3 class="text-lg font-bold text-yellow-800">Konfirmasi Perubahan Data Lokasi/Akun</h3>
                                <p class="text-sm text-yellow-700">Tindakan ini akan <b>mengubah data master lokasi barang</b> secara permanen.</p>
                            </div>
                        </div>
                        
                        <div class="p-6 max-h-[60vh] overflow-y-auto">
                            <p class="text-gray-700 mb-4">Berikut adalah barang yang lokasi akunnya berbeda dengan data master barang:</p>
                            
                            <template x-for="msg in warningMessages">
                                <div class="mb-4 bg-gray-50 p-3 rounded border border-gray-200">
                                    <div class="font-bold text-indigo-700" x-text="msg.item"></div>
                                    <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                        <div>
                                            <span class="block text-xs text-gray-500 uppercase">Lokasi Lama (Master)</span>
                                            <div class="text-gray-800 font-mono bg-white px-2 py-1 rounded border mt-1" x-text="msg.old || '-'"></div>
                                        </div>
                                        <div>
                                            <span class="block text-xs text-gray-500 uppercase">Lokasi Baru (Input)</span>
                                            <div class="text-gray-800 font-mono bg-yellow-50 px-2 py-1 rounded border border-yellow-200 mt-1" x-text="msg.new || '-'"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <p class="text-sm font-bold text-gray-700 mt-6 text-center">Apakah Anda yakin ingin menyimpan perubahan ini ke Data Master Barang?</p>
                        </div>

                        <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showWarningModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-bold">Batal & Periksa Lagi</button>
                            <button type="button" @click="confirmSubmit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold shadow">Ya, Simpan Perubahan</button>
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
                        create: false, sortField: { field: "text", direction: "asc" }, placeholder: "Ketik Kode..."
                    });
                },

                initRowTomSelect(el, item) {
                    const selectEl = el.querySelector('select');
                    globalWarehouses.forEach(wh => {
                        const opt = document.createElement('option');
                        opt.value = wh.name; opt.text = wh.name;
                        selectEl.appendChild(opt);
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
                        plugins: ['remove_button'],
                        create: true,
                        persist: false, 
                        onChange: (val) => {
                            item.warehouses = Array.isArray(val) ? val.join(',') : val;
                        }
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

                    if (this.transactionType === 'out' && stock <= 0) { alert('Habis!'); this.tomSelectInstance.clear(); return; }

                    const exist = this.cart.find(i => i.id === val);
                    if (exist) {
                        if (this.transactionType === 'out' && exist.qty >= stock) alert('Stok kurang!');
                        else exist.qty++;
                    } else {
                        this.cart.push({
                            ui_id: Date.now(),
                            id: val,
                            name: opt.getAttribute('data-name'),
                            code: opt.getAttribute('data-code'),
                            price: price,
                            audit_price: audit,
                            warehouses: wh,
                            original_warehouses: wh,
                            qty: 1,
                            max_stock: stock,
                            subtotal: price
                        });
                    }
                    this.updateTotal();
                    this.tomSelectInstance.clear();
                },

                removeItem(idx) { this.cart.splice(idx, 1); this.updateTotal(); },

                updateTotal() {
                    this.grandTotal = 0;
                    this.cart.forEach(i => { i.subtotal = i.price * i.qty; this.grandTotal += i.subtotal; });
                },

                formatCurrency(number) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 }).format(number); },

                // FUNGSI UTAMA: CEK SEBELUM SUBMIT
                checkBeforeSubmit() {
                    this.warningMessages = []; // Reset pesan

                    // Loop semua item di keranjang
                    for (let item of this.cart) {
                        let inputArr = item.warehouses ? item.warehouses.split(',').map(s => s.trim()).filter(s => s !== "").sort() : [];
                        let originalArr = item.original_warehouses ? item.original_warehouses.split(',').map(s => s.trim()).filter(s => s !== "").sort() : [];

                        // Bandingkan
                        if (JSON.stringify(inputArr) !== JSON.stringify(originalArr)) {
                            // Jika beda, tambahkan ke daftar peringatan
                            this.warningMessages.push({
                                item: `${item.name} (${item.code})`,
                                old: originalArr.join(', '),
                                new: inputArr.join(', ')
                            });
                        }
                    }

                    // Jika ada perbedaan (array peringatan tidak kosong), tampilkan Modal
                    if (this.warningMessages.length > 0) {
                        this.showWarningModal = true;
                    } else {
                        // Jika aman, langsung submit
                        this.confirmSubmit();
                    }
                },

                confirmSubmit() {
                    document.getElementById('cart_items_input').value = JSON.stringify(this.cart);
                    // Submit form manual karena kita pakai @submit.prevent
                    document.getElementById('transactionForm').submit();
                }
            };
        }
    </script>
</x-app-layout>