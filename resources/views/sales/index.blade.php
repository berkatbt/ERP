@extends('layout.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-bold text-white">Kasir Penjualan</h1>
        <p class="text-slate-400">
            Klik produk untuk menambahkan ke transaksi
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-3xl bg-rose-500/10 border border-rose-400 text-rose-200 p-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ============================= --}}
            {{-- LEFT : PRODUCT LIST --}}
            {{-- ============================= --}}
            <div class="lg:col-span-2">

                <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">

                    <div class="mb-5">
                        <input
                            type="text"
                            id="search-product"
                            placeholder="Cari produk..."
                            class="w-full rounded-3xl border border-slate-800 bg-slate-900 px-5 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                        @foreach($products as $product)

                            <button
                                type="button"
                                class="product-card text-left rounded-3xl border border-slate-800 bg-slate-900 hover:border-blue-500 hover:bg-slate-800 transition p-4"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price_sell }}"
                            >

                                <div class="space-y-2">

                                    <div>
                                        <h3 class="font-bold text-white">
                                            {{ $product->name }}
                                        </h3>

                                        <p class="text-sm text-slate-400">
                                            {{ $product->stock->stock ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="text-emerald-300 font-bold text-lg">
                                        Rp {{ number_format($product->price_sell, 0, ',', '.') }}
                                    </div>

                                </div>

                            </button>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- ============================= --}}
            {{-- RIGHT : SUMMARY --}}
            {{-- ============================= --}}
            <div>

                <div class="sticky top-5 rounded-3xl bg-slate-950/80 border border-slate-800 p-6">

                    <h2 class="text-xl font-bold text-white mb-5">
                        Summary Transaksi
                    </h2>

                    <div id="cart-items" class="space-y-3">

                        <div class="text-slate-500 text-sm">
                            Belum ada produk dipilih
                        </div>

                    </div>

                    <hr class="border-slate-800 my-5">

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm text-slate-300 mb-2">
                                Metode Pembayaran
                            </label>

                            <select
                                name="payment_type"
                                class="w-full rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3 text-white"
                            >
                                <option value="cash">Cash</option>
                                <option value="credit">Piutang</option>
                            </select>
                        </div>

                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-slate-300">Total</span>
                            <span id="grand-total" class="text-emerald-300">
                                Rp 0
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-3xl bg-emerald-600 hover:bg-emerald-500 text-white py-4 font-bold text-lg transition"
                        >
                            Simpan Transaksi
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

    {{-- ===================================== --}}
    {{-- TABLE TRANSAKSI --}}
    {{-- ===================================== --}}
    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-white">
                Riwayat Penjualan
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-sm">
                        <th class="text-left py-3">Invoice</th>
                        <th class="text-left py-3">Kasir</th>
                        <th class="text-left py-3">Pembayaran</th>
                        <th class="text-left py-3">Status</th>
                        <th class="text-right py-3">Total</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($sales as $sale)

                        <tr class="border-b border-slate-900 hover:bg-slate-900/50 transition">

                            <td class="py-4 text-white font-semibold">
                                {{ $sale->invoice }}
                            </td>

                            <td class="py-4 text-slate-300">
                                {{ $sale->user->name }}
                            </td>

                            <td class="py-4">
                                <span class="capitalize text-slate-300">
                                    {{ $sale->payment_type }}
                                </span>
                            </td>

                            <td class="py-4">

                                @if($sale->status == 'paid')

                                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        Paid
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-xs bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Unpaid
                                    </span>

                                @endif

                            </td>

                            <td class="py-4 text-right text-emerald-300 font-bold">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-500">
                                Belum ada transaksi
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- ====================================== --}}
{{-- JS --}}
{{-- ====================================== --}}
<script>

let cart = {};

function renderCart() {

    let container = document.getElementById('cart-items');

    container.innerHTML = '';

    let total = 0;

    if(Object.keys(cart).length === 0){

        container.innerHTML = `
            <div class="text-slate-500 text-sm">
                Belum ada produk dipilih
            </div>
        `;

        document.getElementById('grand-total').innerText = 'Rp 0';

        return;
    }

    Object.values(cart).forEach((item, index) => {

        let subtotal = item.qty * item.price;

        total += subtotal;

        container.innerHTML += `

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <h4 class="font-semibold text-white">
                            ${item.name}
                        </h4>

                        <div class="text-sm text-slate-400">
                            Rp ${Number(item.price).toLocaleString('id-ID')}
                        </div>
                    </div>

                    <div class="text-right">

                        <div class="flex items-center gap-2 justify-end">

                            <button
                                type="button"
                                onclick="decreaseQty(${item.id})"
                                class="w-8 h-8 rounded-full bg-slate-800 text-white"
                            >
                                -
                            </button>

                            <span class="text-white font-bold min-w-[20px] text-center">
                                ${item.qty}
                            </span>

                            <button
                                type="button"
                                onclick="increaseQty(${item.id})"
                                class="w-8 h-8 rounded-full bg-blue-600 text-white"
                            >
                                +
                            </button>

                        </div>

                        <div class="text-emerald-300 font-bold mt-2">
                            Rp ${subtotal.toLocaleString('id-ID')}
                        </div>

                    </div>

                </div>

                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][qty]" value="${item.qty}">

            </div>

        `;
    });

    document.getElementById('grand-total').innerText =
        'Rp ' + total.toLocaleString('id-ID');
}

function increaseQty(id){
    cart[id].qty++;
    renderCart();
}

function decreaseQty(id){

    cart[id].qty--;

    if(cart[id].qty <= 0){
        delete cart[id];
    }

    renderCart();
}

document.querySelectorAll('.product-card').forEach(card => {

    card.addEventListener('click', function(){

        let id = this.dataset.id;

        if(cart[id]){

            cart[id].qty++;

        } else {

            cart[id] = {
                id: id,
                name: this.dataset.name,
                price: Number(this.dataset.price),
                qty: 1
            };
        }

        renderCart();
    });

});

document.getElementById('search-product').addEventListener('keyup', function(){

    let keyword = this.value.toLowerCase();

    document.querySelectorAll('.product-card').forEach(card => {

        let name = card.dataset.name.toLowerCase();

        if(name.includes(keyword)){
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }

    });

});

</script>

@endsection