<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Transaksi Baru') }}</h2>
    </x-slot>

    <div class="py-12" x-data="transactionApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.store') }}" @submit.prevent="submitForm">
                @csrf
                <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="cart_items" id="cart_items_input">
                <input type="hidden" name="type" x-model="transactionType">
                
                <input type="hidden" name="invoice_code" x-model="currentInvoiceCode">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Jenis Transaksi</h3>
                            <div class="flex rounded-md shadow-sm">
                                <button type="button" @click="setMode('out')"
                                    :class="transactionType === 'out' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border rounded-l-lg w-1/2 transition">📤 Penjualan</button>
                                <button type="button" @click="setMode('in')"
                                    :class="transactionType === 'in' ? 'bg-green-600 text-white' : 'bg-white text-gray-700'"
                                    class="px-4 py-2 text-sm font-medium border rounded-r-lg w-1/2 transition">📥 Pembelian</button>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <label class="block font-medium text-sm text-gray-700 mb-1">
                                <span x-text="transactionType === 'out' ? 'Target Market / Customer' : 'Asal Market / Supplier'"></span>
                            </label>
                            <input class="border-gray-300 rounded-md shadow-sm w-full" type="text" name="market" x-model="marketInput" placeholder="Contoh: Tokopedia, Agen Budi...">
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Pilih Barang</h3>
                            <select id="item_select" class="w-full" placeholder="Cari Barang..." autocomplete="off">
                                <option value="">Cari Barang...</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        data-code="{{ $item->code }}"
                                        data-sell-price="{{ $item->sell_price }}"
                                        data-buy-price="{{ $item->buy_price }}"
                                        data-market="{{ $item->market }}"
                                        data-stock="{{ $item->stock }}">
                                        {{ $item->code }} - {{ $item->name }} (Stok: {{ $item->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" @click="addToCart()" class="mt-4 w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">+ Masukkan</button>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Keranjang Belanja</h3>
                                <span class="text-sm font-mono font-bold text-indigo-600" x-text="currentInvoiceCode"></span>
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
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Harga</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-sm" x-text="item.name"></div>
                                                <div class="text-xs text-gray-500" x-text="item.code"></div>
                                                <div x-show="transactionType === 'out'" class="text-xs text-red-500" x-text="'Sisa Stok: ' + (item.max_stock - item.qty)"></div>
                                            </td>
                                            <td class="px-4 py-3"><input type="number" x-model="item.price" @input="updateTotal()" class="w-28 p-1 text-sm border rounded"></td>
                                            <td class="px-4 py-3"><input type="number" x-model="item.qty" @input="updateTotal()" min="1" class="w-16 p-1 text-sm border rounded"></td>
                                            <td class="px-4 py-3 text-right font-bold text-sm" x-text="formatRupiah(item.price * item.qty)"></td>
                                            <td class="px-4 py-3 text-center"><button type="button" @click="removeItem(index)" class="text-red-500 font-bold">X</button></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-gray-600">Total Item: <span class="font-bold" x-text="cart.length"></span></div>
                                <div class="text-2xl font-bold text-gray-800">Total: <span class="text-indigo-600" x-text="formatRupiah(grandTotal)"></span></div>
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
            </form>
        </div>
    </div>

    <script>
        function transactionApp() {
            return {
                cart: [],
                grandTotal: 0,
                transactionType: 'out',
                marketInput: '',
                tomSelectInstance: null,
                
                // Variabel dari Controller
                todayPrefix: '{{ $todayPrefix }}',
                seqString: '{{ $seqString }}',
                
                // Computed Property (Invoice Code Dinamis)
                get currentInvoiceCode() {
                    const typeCode = this.transactionType === 'out' ? 'OUT' : 'IN';
                    return `INV-${typeCode}-${this.todayPrefix}-${this.seqString}`;
                },

                init() {
                    this.tomSelectInstance = new TomSelect("#item_select", {
                        create: false,
                        sortField: { field: "text", direction: "asc" },
                        placeholder: "Ketik Kode atau Nama..."
                    });
                },

                setMode(mode) {
                    if (this.cart.length > 0) {
                        if (!confirm('Ganti mode akan mereset keranjang. Lanjut?')) return;
                    }
                    this.transactionType = mode;
                    this.cart = [];
                    this.grandTotal = 0;
                    this.marketInput = '';
                    this.tomSelectInstance.clear();
                },

                addToCart() {
                    const selectValue = this.tomSelectInstance.getValue();
                    if (!selectValue) return;

                    // Cari option asli di DOM
                    const originalSelect = document.getElementById('item_select');
                    let selectedOption = null;
                    for(let opt of originalSelect.options) {
                        if(opt.value == selectValue) { selectedOption = opt; break; }
                    }

                    if (!selectedOption) return;

                    const id = selectedOption.value;
                    // PERBAIKAN: Tambahkan '|| 0' agar tidak NaN
                    const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                    
                    // Ambil harga jual & beli dengan pengaman
                    const sellPrice = parseInt(selectedOption.getAttribute('data-sell-price')) || 0;
                    const buyPrice = parseInt(selectedOption.getAttribute('data-buy-price')) || 0;

                    // Tentukan harga berdasarkan tipe transaksi
                    const price = (this.transactionType === 'out') ? sellPrice : buyPrice;

                    // Cek Stok untuk Transaksi Keluar
                    if (this.transactionType === 'out' && stock <= 0) {
                        alert('Stok Habis!'); 
                        this.tomSelectInstance.clear(); 
                        return;
                    }

                    // Auto-fill Market untuk IN
                    const itemMarket = selectedOption.getAttribute('data-market');
                    if (this.transactionType === 'in' && !this.marketInput && itemMarket) {
                        this.marketInput = itemMarket;
                    }

                    // Masukkan ke keranjang
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        if (this.transactionType === 'out' && existing.qty >= stock) {
                            alert('Stok tidak cukup!');
                        } else {
                            existing.qty++;
                        }
                    } else {
                        this.cart.push({
                            id: id,
                            name: selectedOption.getAttribute('data-name'),
                            code: selectedOption.getAttribute('data-code'),
                            price: price, // <--- Ini sekarang DIJAMIN ANGKA
                            qty: 1,
                            max_stock: stock,
                            subtotal: price
                        });
                    }
                    
                    this.updateTotal();
                    this.tomSelectInstance.clear();
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.updateTotal();
                },

                updateTotal() {
                    this.grandTotal = 0;
                    this.cart.forEach(i => {
                        i.subtotal = i.price * i.qty;
                        this.grandTotal += i.subtotal;
                    });
                },

                formatRupiah(num) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
                },

                submitForm(e) {
                    document.getElementById('cart_items_input').value = JSON.stringify(this.cart);
                    e.target.submit();
                }
            };
        }
    </script>
</x-app-layout>