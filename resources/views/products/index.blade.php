@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Manajemen Produk</h1>
            <p class="text-sm text-slate-400">Data master produk untuk semua transaksi pembelian dan penjualan.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/80 border border-slate-800 px-4 py-3 text-sm text-slate-300">
            Total produk: {{ $products->count() }}</div>
    </div>

    @if(session('success'))
        <div id="successNotification" class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-white">Daftar Produk</h2>
        <button type="button" id="openModalBtn" class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">
            Tambah Produk Baru
        </button>
    </div>

    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-[600px]">
        <h2 class="text-lg font-semibold text-white mb-4">Daftar Produk</h2>
        <div class="overflow-x-auto flex-1">
            <table class="min-w-full text-left text-sm text-slate-200">
                <thead class="border-b border-slate-800 text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Harga Beli</th>
                        <th class="px-4 py-3">Harga Jual</th>
                        <th class="px-4 py-3">Min. Stok</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-900/70 transition">
                            <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $product->sku }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($product->price_buy, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($product->price_sell, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $product->min_stock ?? 0 }} unit</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $product->status === 'aktif' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <button onclick="openEditModal({{ $product->id }})" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openDeleteModal({{ $product->id }}, '{{ $product->name }}')" title="Hapus" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
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

<!-- Modal Tambah Produk Baru -->
<div id="productModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Tambah Produk Baru</h2>
                <button type="button" id="closeModalBtn" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="name">Nama Produk <span class="text-rose-400">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="sku">SKU <span class="text-rose-400">*</span></label>
                        <input id="sku" name="sku" type="text" value="{{ old('sku') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('sku')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="price_buy">Harga Beli <span class="text-rose-400">*</span></label>
                        <input id="price_buy" name="price_buy" type="number" step="0.01" value="{{ old('price_buy') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('price_buy')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="price_sell">Harga Jual <span class="text-rose-400">*</span></label>
                        <input id="price_sell" name="price_sell" type="number" step="0.01" value="{{ old('price_sell') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('price_sell')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="min_stock">Minimum Stok</label>
                        <input id="min_stock" name="min_stock" type="number" value="{{ old('min_stock', 0) }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('min_stock')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="status">Status <span class="text-rose-400">*</span></label>
                        <select id="status" name="status" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ old('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Buat Produk</button>
                    <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Edit Produk</h2>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_name">Nama Produk <span class="text-rose-400">*</span></label>
                        <input id="edit_name" name="name" type="text" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_sku">SKU <span class="text-rose-400">*</span></label>
                        <input id="edit_sku" name="sku" type="text" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_price_buy">Harga Beli <span class="text-rose-400">*</span></label>
                        <input id="edit_price_buy" name="price_buy" type="number" step="0.01" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_price_sell">Harga Jual <span class="text-rose-400">*</span></label>
                        <input id="edit_price_sell" name="price_sell" type="number" step="0.01" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_min_stock">Minimum Stok</label>
                        <input id="edit_min_stock" name="min_stock" type="number" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_status">Status <span class="text-rose-400">*</span></label>
                        <select id="edit_status" name="status" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Simpan Perubahan</button>
                    <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
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

<script>
    let deleteFormProductId = null;

    // Modal functions
    function openModal() {
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
    }

    function openEditModal(productId) {
        const editModal = document.getElementById('editModal');
        editModal.classList.remove('hidden');

        // Fetch product data
        fetch(`/products/${productId}/edit`)
            .then(response => response.json())
            .then(product => {
                document.getElementById('edit_name').value = product.name || '';
                document.getElementById('edit_sku').value = product.sku || '';
                document.getElementById('edit_price_buy').value = product.price_buy || '';
                document.getElementById('edit_price_sell').value = product.price_sell || '';
                document.getElementById('edit_min_stock').value = product.min_stock || '';
                document.getElementById('edit_status').value = product.status || '';

                document.getElementById('editForm').action = `/products/${product.id}`;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data produk');
                closeEditModal();
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openDeleteModal(productId, productName) {
        deleteFormProductId = productId;
        document.getElementById('deleteModalContent').innerHTML = `Apakah Anda yakin ingin menghapus produk <strong>"${productName}"</strong>? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteFormProductId = null;
    }

    function confirmDelete() {
        if (deleteFormProductId) {
            document.getElementById('deleteForm').action = `/products/${deleteFormProductId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    // Event listeners
    document.getElementById('openModalBtn').addEventListener('click', openModal);
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);

    // Close modal when clicking outside
    document.getElementById('productModal').addEventListener('click', function(e) {
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

    // Show modal if there are validation errors
    @if ($errors->any())
        openModal();
    @endif
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
