@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Manajemen Stok</h1>
            <p class="text-sm text-slate-400">Kelola stok barang untuk setiap cabang.</p>
        </div>
        <button type="button" id="openModalBtn" class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition" {{ !$selectedBranch ? 'disabled' : '' }} {{ !$selectedBranch ? 'style=opacity:0.5;cursor:not-allowed;' : '' }}>
            Tambah Stok Baru
        </button>
    </div>

    @if(session('success'))
        <div id="successNotification" class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-auto">
        <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-300 mb-3">Pilih Cabang</label>
            <form method="GET" action="{{ route('stocks.index') }}" class="flex gap-3">
                <select name="branch_id" onchange="this.form.submit()" class="flex-1 rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedBranch && $stocks->count() > 0)
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-white">Stok Barang</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="border-b border-slate-800 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Nama Produk</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Harga Beli</th>
                                <th class="px-4 py-3">Harga Jual</th>
                                <th class="px-4 py-3">Stok</th>
                                <th class="px-4 py-3">Min. Stok</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($stocks as $stock)
                                <tr class="hover:bg-slate-900/70 transition">
                                    <td class="px-4 py-3 font-medium">{{ $stock->product->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $stock->product->sku }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($stock->product->price_buy, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($stock->product->price_sell, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-3 py-1 rounded-full font-semibold 
                                            {{ $stock->stock > $stock->product->min_stock ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                            {{ $stock->stock }} unit
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $stock->product->min_stock }} unit</td>
                                    <td class="px-4 py-3">
                                        @if($stock->stock <= 0)
                                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-rose-500/20 text-rose-300">Habis</span>
                                        @elseif($stock->stock <= $stock->product->min_stock)
                                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-300">Rendah</span>
                                        @else
                                            <span class="inline-block px-2 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-300">Aman</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-1">
                                            <button onclick="openEditModal({{ $stock->id }})" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $stock->id }}, '{{ $stock->product->name }}')" title="Hapus" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800">
                    <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4">
                        <p class="text-xs text-slate-400 mb-2">Total Produk</p>
                        <p class="text-2xl font-bold text-white">{{ $stocks->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4">
                        <p class="text-xs text-slate-400 mb-2">Stok Aman</p>
                        <p class="text-2xl font-bold text-emerald-300">{{ $stocks->where('stock', '>', collect($stocks)->pluck('product.min_stock')->first())->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4">
                        <p class="text-xs text-slate-400 mb-2">Stok Rendah</p>
                        <p class="text-2xl font-bold text-yellow-300">{{ $stocks->filter(fn($s) => $s->stock > 0 && $s->stock <= $s->product->min_stock)->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4">
                        <p class="text-xs text-slate-400 mb-2">Stok Habis</p>
                        <p class="text-2xl font-bold text-rose-300">{{ $stocks->where('stock', '<=', 0)->count() }}</p>
                    </div>
                </div>
            </div>
        @elseif($selectedBranch)
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-slate-400">Belum ada stok untuk cabang ini.</p>
                <p class="text-sm text-slate-500 mt-2">Klik tombol "Tambah Stok Baru" untuk membuat stok produk.</p>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h5.581m0 0H19M9 9h6m0 0H9m0 0h6"></path>
                </svg>
                <p class="text-slate-400">Pilih cabang untuk melihat stok.</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="rounded-3xl bg-blue-500/10 border border-blue-400 p-4">
        {{-- <h3 class="font-semibold text-blue-200 mb-2">💡 Informasi</h3>
        <ul class="text-sm text-blue-200 space-y-1 list-disc list-inside">
            <li>Stok ditampilkan per cabang (berbasis gudang cabang)</li>
            <li>Status "Aman" berarti stok lebih dari minimum stok yang ditetapkan</li>
            <li>Status "Rendah" berarti stok mendekati atau sama dengan minimum stok</li>
            <li>Status "Habis" berarti stok sudah 0 atau kurang</li>
            <li>Setiap perubahan stok akan dicatat dalam riwayat pergerakan stok</li>
        </ul> --}}
    </div>
</div>

<!-- Modal Tambah Stok Baru -->
<div id="stockModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Tambah Stok Baru</h2>
                <button type="button" id="closeModalBtn" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('stocks.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="branch_id" id="form_branch_id" value="{{ $selectedBranch }}">
                
                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="product_id">Produk <span class="text-rose-400">*</span></label>
                    <select id="product_id" name="product_id" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($availableProducts as $product)
                            <option value="{{ $product->id }}" data-sku="{{ $product->sku }}">
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="stock">Jumlah Stok <span class="text-rose-400">*</span></label>
                    <input id="stock" name="stock" type="number" value="{{ old('stock', 0) }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                    @error('stock')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Tambah Stok</button>
                    <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Stok -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Edit Stok</h2>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm text-slate-300 mb-2">Produk</label>
                    <input id="edit_product_name" type="text" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="edit_stock">Jumlah Stok <span class="text-rose-400">*</span></label>
                    <input id="edit_stock" name="stock" type="number" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Simpan Perubahan</button>
                    <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Stok -->
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

<script>
    let deleteFormStockId = null;

    // Modal functions
    function openModal() {
        const branchId = document.querySelector('select[name="branch_id"]').value;
        if (!branchId) {
            alert('Pilih cabang terlebih dahulu');
            return;
        }
        document.getElementById('form_branch_id').value = branchId;
        document.getElementById('stockModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('stockModal').classList.add('hidden');
    }

    function openEditModal(stockId) {
        const editModal = document.getElementById('editModal');
        editModal.classList.remove('hidden');

        // Fetch stock data
        fetch(`/stocks/${stockId}/edit`)
            .then(response => response.json())
            .then(stock => {
                document.getElementById('edit_product_name').value = stock.product_name + ' (' + stock.product_sku + ')';
                document.getElementById('edit_stock').value = stock.stock || '';

                document.getElementById('editForm').action = `/stocks/${stock.id}`;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data stok');
                closeEditModal();
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openDeleteModal(stockId, productName) {
        deleteFormStockId = stockId;
        document.getElementById('deleteModalContent').innerHTML = `Apakah Anda yakin ingin menghapus stok produk <strong>"${productName}"</strong>? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteFormStockId = null;
    }

    function confirmDelete() {
        if (deleteFormStockId) {
            document.getElementById('deleteForm').action = `/stocks/${deleteFormStockId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    // Event listeners
    document.getElementById('openModalBtn').addEventListener('click', openModal);
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);

    // Close modal when clicking outside
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

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
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrolling-behavior: smooth;
    }
</style>
@endsection
