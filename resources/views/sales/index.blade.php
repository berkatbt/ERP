@extends('layout.app')

@section('content')

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold text-white mb-2">Kasir Penjualan</h1>
    <p class="text-slate-400 mb-6">Input transaksi penjualan produk secara cepat dan mudah.</p>

    @if(session('success'))
        <div class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-3xl bg-rose-500/10 border border-rose-400 text-rose-200 p-4 mb-4">{{ session('error') }}</div>
    @endif

    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm text-slate-300 mb-2 font-semibold">Daftar Produk</label>
                <div id="product-list" class="space-y-2">
                    <div class="flex flex-col md:flex-row gap-2 product-item items-center">
                        <select name="products[0][id]" class="rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-2 text-slate-100 product-select focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price_sell }}">
                                    {{ $product->name }} (Rp {{ number_format($product->price_sell, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][qty]" class="rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-2 text-slate-100 qty focus:outline-none focus:ring-2 focus:ring-blue-500 w-24" value="1" min="1">
                        <span class="subtotal px-4 py-2 min-w-[100px] text-right">Rp 0</span>
                        <button type="button" class="remove rounded-full bg-rose-500/80 hover:bg-rose-500 text-white px-3 py-1 transition">&times;</button>
                    </div>
                </div>
                <button type="button" id="add-product" class="mt-3 rounded-3xl bg-blue-500 hover:bg-blue-400 text-white px-6 py-2 font-semibold transition">
                    + Tambah Produk
                </button>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <label class="block text-sm text-slate-300 mb-2 font-semibold">Pembayaran</label>
                    <select name="payment_type" class="rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="credit">Piutang</option>
                    </select>
                </div>
                <div class="text-lg font-bold text-white mt-4 md:mt-0">
                    <span>Total: </span>
                    <span id="total" class="text-emerald-300">Rp 0</span>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="rounded-3xl bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3 font-semibold text-lg transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================= --}}
<script>


let index = 1;
document.getElementById('add-product').addEventListener('click', function () {
    let html = `
    <div class=\"flex flex-col md:flex-row gap-2 product-item items-center\">
        <select name=\"products[${index}][id]\" class=\"rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-2 text-slate-100 product-select focus:outline-none focus:ring-2 focus:ring-blue-500\">
            @foreach($products as $product)
                <option value=\"{{ $product->id }}\" data-price=\"{{ $product->price_sell }}\">
                    {{ $product->name }} (Rp {{ number_format($product->price_sell, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
        <input type=\"number\" name=\"products[${index}][qty]\" class=\"rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-2 text-slate-100 qty focus:outline-none focus:ring-2 focus:ring-blue-500 w-24\" value=\"1\" min=\"1\">
        <span class=\"subtotal px-4 py-2 min-w-[100px] text-right\">Rp 0</span>
        <button type=\"button\" class=\"remove rounded-full bg-rose-500/80 hover:bg-rose-500 text-white px-3 py-1 transition\">&times;</button>
    </div>
    `;
    document.getElementById('product-list').insertAdjacentHTML('beforeend', html);
    index++;
    calculateTotal();
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove')){
        e.target.closest('.product-item').remove();
        calculateTotal();
    }
});

document.addEventListener('change', function(e){
    if(e.target.classList.contains('product-select') || e.target.classList.contains('qty')){
        calculateTotal();
    }
});

function calculateTotal(){
    let total = 0;
    document.querySelectorAll('.product-item').forEach(item => {
        let select = item.querySelector('.product-select');
        let qty = item.querySelector('.qty').value;
        let price = select.options[select.selectedIndex].dataset.price;
        let subtotal = price * qty;
        item.querySelector('.subtotal').innerText = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        total += Number(subtotal);
    });
    document.getElementById('total').innerText = 'Rp ' + total.toLocaleString('id-ID');
}
calculateTotal();

</script>

@endsection