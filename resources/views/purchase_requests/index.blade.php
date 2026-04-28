@extends('layout.app')

@section('content')
<div>

    <div class="mb-4">
        <h1 class="text-2xl font-bold">Purchase Request</h1>
        <p class="text-sm text-slate-400">Data manajemen pembelian barang.</p>
    </div>
    
    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div id="successNotification" class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="text-white shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Daftar Request</h2>
            @if(auth()->user()->role->name == 'Warehouse Admin')
                <button onclick="openModal()" 
                    class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">
                    Buat Purchase Request
                </button>
            @endif
        </div>
        <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-[600px]">
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full text-left text-sm text-slate-200">
                    <thead class="border-b border-slate-800 text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Harga Produk</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($prs as $pr)
                            @foreach($pr->details as $d)
                                <tr class="hover:bg-slate-900/70 transition">
                                    <td class="px-4 py-3 font-medium">{{ $d->product->name }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($d->product->price_buy, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $d->qty }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $pr->status === 'aktif' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                            {{ ucfirst($pr->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-1">
                                            
                                            {{-- @if(auth()->user()->role->name == 'manager' && $pr->status == 'pending') --}}

                                                {{-- APPROVE --}}
                                                <form method="POST" action="{{ route('purchase-requests.approve', $pr->id) }}">
                                                    @csrf
                                                    <button class="bg-green-600 text-white px-2 py-1 rounded-lg">
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- REJECT --}}
                                                <form method="POST" action="{{ route('purchase-requests.reject', $pr->id) }}">
                                                    @csrf
                                                    <button class="bg-red-500/70 text-white px-2 py-1 rounded-lg">
                                                        Reject
                                                    </button>
                                                </form>

                                            {{-- @endif --}}
                                            <button onclick="openEditModal({{ $pr->id }})" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $pr->id }}, '{{ $pr->name }}')" title="Hapus" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

{{-- Form --}}
@if(auth()->user()->role->name == 'Warehouse Admin')

<div id="prModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    {{-- MODAL BOX --}}
    <div id="modalBox" class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Buat Purchase Request</h2>
            <button type="button" onclick="closeModal()" id="closeModalBtn" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('purchase-requests.store') }}" class="space-y-4">
            @csrf

            {{-- LIST PRODUK --}}
            <div id="products">
                <div class="product-item flex gap-2 mb-3">
                    <select name="products[0][id]" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none" required>
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
                        class="w-lg rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none"
                        min="1"
                        required
                    >
                </div>
            </div>

            {{-- NOTE --}}
            <textarea 
                name="note" 
                class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none"
                placeholder="Catatan tambahan..."
            ></textarea>

            {{-- ACTION --}}
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Buat</button>
                <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeModal()">Batal</button>
            </div>

        </form>

    </div>
</div>

@endif

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

</script>