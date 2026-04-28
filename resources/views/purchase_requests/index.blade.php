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
                            <th class="px-4 py-3">Diajukan Oleh</th>
                            <th class="px-4 py-3">Tracking</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($prs as $pr)
                            @foreach($pr->details as $d)
                                <tr class="hover:bg-slate-900/70 transition">
                                    <td class="px-4 py-3 font-medium">{{ $d->product->name }}</td>
                                    <td class="px-4 py-3">{{ $pr->user->name }}</td>
                                    <td class="px-4 py-3">{{ $pr->latestTracking->tracking }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $pr->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                            {{ ucfirst($pr->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-1">
                                            
                                            @if(auth()->user()->role->name == 'Manager' && $pr->status == 'pending')

                                                <form method="POST" action="{{ route('purchase-requests.update', $pr->id) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="hidden" name="status" value="approved">

                                                    <button class="bg-green-600 text-white px-2 py-1 rounded-lg">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('purchase-requests.update', $pr->id) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="hidden" name="status" value="rejected">

                                                    <button class="bg-red-500/70 text-white px-2 py-1 rounded-lg">
                                                        Reject
                                                    </button>
                                                </form>

                                            @endif
                                            <a href="{{ route('purchase-requests.show', $pr->id) }}" 
                                            title="Detail"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 transition">
                                                
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M15 12H9m12 0c0 4-4 8-9 8s-9-4-9-8 4-8 9-8 9 4 9 8z"/>
                                                </svg>
                                            </a>
                                            <button onclick="openEditModal({{ json_encode($pr->load('details.product', 'tracking')) }})" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $pr->id }}, '{{ $d->product->name }}')" title="Hapus" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
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
                    <select name="product_id" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>

                    <input 
                        type="number" 
                        name="qty" 
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

<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999]">

    <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Edit Purchase Request</h2>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                ✕
            </button>
        </div>

        {{-- FORM --}}
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div id="editProducts"></div>

            <textarea name="note" id="editNote"
                class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 mt-3"
                placeholder="Catatan tambahan..."></textarea>

            <select name="tracking" id="editTracking"
                class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 mt-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <option hidden value="">Pilih Tracking</option>
                <option value="Menunggu Approval">Menunggu Approval</option>
                <option hidden value="Diapprove oleh manager">Diapprove oleh manager</option>
                <option hidden value="Ditolak oleh manager">Ditolak oleh manager</option>
                <option value="Dalam Pengiriman">Dalam Pengiriman</option>
                <option value="Selesai">Selesai</option>
            </select>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-white">
                    Update
                </button>

                <button type="button" onclick="closeEditModal()"
                    class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-slate-300">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal Hapus Produk -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Konfirmasi Penghapusan</h2>
                <button type="button" onclick="closeDeleteModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p id="deleteModalContent" class="text-slate-300 mb-6"></p>

            <div class="flex gap-4">
                <button type="button" onclick="confirmDelete()" class="flex-1 rounded-3xl bg-rose-500 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-400 transition">Hapus</button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition">Batal</button>
            </div>

            <form id="deleteForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@endif

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

function openEditModal(pr) {
    let modal = document.getElementById('editModal');
    let container = document.getElementById('editProducts');
    let form = document.getElementById('editForm');

    form.action = `/purchase-requests/update/${pr.id}`;

    let d = pr.details[0];

    container.innerHTML = `
        <div class="flex gap-2 mb-3">
            <select name="product_id" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                ${generateOptions(d.product_id)}
            </select>

            <input type="number" name="qty" value="${d.qty}" class="w-lg rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
        </div>
    `;

    document.getElementById('editNote').value = pr.note ?? '';
    if (pr.latest_tracking.tracking.includes('Ditolak oleh manager', 'Menunggu Approval')) {
        document.getElementById('editTracking').disabled = true;
    }
    document.getElementById('editTracking').value = pr.latest_tracking?.tracking ?? '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    let modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// generate option select
function generateOptions(selectedId) {
    let products = @json($products);
    let html = `<option hidden value="">Pilih Produk</option>`;

    products.forEach(p => {
        html += `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>
                    ${p.name}
                </option>`;
    });

    return html;
}

// Auto-hide success notification after 2 seconds
const successNotification = document.getElementById('successNotification');
if (successNotification) {
    setTimeout(() => {
        successNotification.style.opacity = '0';
        setTimeout(() => {
            successNotification.style.display = 'none';
        }, 500);
    }, 2000);
}

// function delete

let deleteId = null;

function openDeleteModal(id, name) {
    deleteId = id;

    let modal = document.getElementById('deleteModal');
    let content = document.getElementById('deleteModalContent');

    content.innerHTML = `
        Yakin ingin menghapus pengajuan <b class="text-white">${name}</b>?<br>
        Data ini tidak bisa dikembalikan.
    `;

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDeleteModal() {
    let modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function confirmDelete() {
    let form = document.getElementById('deleteForm');

    // set action dinamis
    form.action = `/purchase-requests/${deleteId}`;

    form.submit();
}

// klik luar = close
document.addEventListener('click', function(e) {
    let modal = document.getElementById('deleteModal');

    if (e.target === modal) {
        closeDeleteModal();
    }
});
</script>