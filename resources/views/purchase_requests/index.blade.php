@extends('layout.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold mb-4">Purchase Request</h1>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-200 p-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- LIST PR --}}
    {{-- ========================= --}}
    @if(auth()->user()->role->name == 'Warehouse Admin')
    <button onclick="openModal()" 
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 shadow">
        + Buat Purchase Request
    </button>
    @endif

    <div class="text-white p-4 shadow">
        <h2 class="font-bold mb-4">Daftar PR</h2>

        @foreach($prs as $pr)
            <div class="border p-3 mb-3">

                <div class="flex justify-between">
                    <div>
                        <b>ID:</b> {{ $pr->id }} <br>
                        <b>Status:</b> {{ $pr->status }} <br>
                        <b>Cabang:</b> {{ $pr->branch_id }} <br>
                        <b>By:</b> {{ $pr->user->name ?? '-' }}
                    </div>
                </div>

                {{-- DETAIL PRODUK --}}
                <div class="mt-2">
                    <b>Produk:</b>
                    <ul>
                        @foreach($pr->details as $d)
                            <li>
                                {{ $d->product->name }} - Qty: {{ $d->qty }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ========================= --}}
                {{-- ACTION BUTTON --}}
                {{-- ========================= --}}
                @if(auth()->user()->role->name == 'manager' && $pr->status == 'pending')

                    <div class="mt-3 flex gap-2">

                        {{-- APPROVE --}}
                        <form method="POST" action="{{ route('purchase-requests.approve', $pr->id) }}">
                            @csrf
                            <button class="bg-green-500 text-white px-2 py-1">
                                Approve
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form method="POST" action="{{ route('purchase-requests.reject', $pr->id) }}">
                            @csrf
                            <button class="bg-red-500 text-white px-2 py-1">
                                Reject
                            </button>
                        </form>

                    </div>

                @endif

            </div>
        @endforeach

    </div>

</div>

{{-- Form --}}
@if(auth()->user()->role->name == 'Warehouse Admin')

<div id="prModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    {{-- MODAL BOX --}}
    <div id="modalBox" class="bg-black w-full max-w-2xl rounded-2xl shadow-2xl p-6 transform scale-95 opacity-0 transition-all duration-300">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Buat Purchase Request</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-red-500 text-xl">✕</button>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('purchase-requests.store') }}">
            @csrf

            {{-- LIST PRODUK --}}
            <div id="products">
                <div class="product-item flex gap-2 mb-3">
                    <select name="products[0][id]" class="border p-2 rounded w-1/2" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>

                    <input 
                        type="number" 
                        name="products[0][qty]" 
                        placeholder="Qty" 
                        class="border p-2 rounded w-1/4" 
                        min="1"
                        required
                    >

                    <button type="button" onclick="removeProduct(this)" 
                        class="bg-red-500 text-white px-3 rounded">
                        ✕
                    </button>
                </div>
            </div>

            {{-- TAMBAH PRODUK --}}
            <button type="button" onclick="addProduct()" 
                class="text-sm bg-yellow-400 px-3 py-1 rounded mb-3">
                + Tambah Produk
            </button>

            {{-- NOTE --}}
            <textarea 
                name="note" 
                class="border p-2 w-full mb-4 rounded" 
                placeholder="Catatan tambahan..."
            ></textarea>

            {{-- ACTION --}}
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" 
                    class="bg-gray-300 px-4 py-2 rounded">
                    Batal
                </button>

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Submit
                </button>
            </div>

        </form>

    </div>
</div>

@endif

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>

<script>
let index = 1;

// ================= MODAL =================
function openModal() {
    let modal = document.getElementById('prModal');
    let box = document.getElementById('modalBox');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    let modal = document.getElementById('prModal');
    let box = document.getElementById('modalBox');

    box.classList.add('scale-95', 'opacity-0');
    box.classList.remove('scale-100', 'opacity-100');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

// klik luar = close
document.addEventListener('click', function(e) {
    let modal = document.getElementById('prModal');
    let box = document.getElementById('modalBox');

    if (e.target === modal) {
        closeModal();
    }
});

// ================= PRODUK =================
function addProduct() {
    let container = document.getElementById('products');

    let html = `
        <div class="product-item flex gap-2 mb-3">
            <select name="products[${index}][id]" class="border p-2 rounded w-1/2" required>
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <input 
                type="number" 
                name="products[${index}][qty]" 
                placeholder="Qty" 
                class="border p-2 rounded w-1/4" 
                min="1"
                required
            >

            <button type="button" onclick="removeProduct(this)" 
                class="bg-red-500 text-white px-3 rounded">
                ✕
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    index++;
}

function removeProduct(btn) {
    btn.parentElement.remove();
}
</script>