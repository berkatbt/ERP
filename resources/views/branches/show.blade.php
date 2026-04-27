@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Detail Cabang</h1>
            <p class="text-sm text-slate-400">Informasi lengkap tentang cabang {{ $branch->name }}</p>
        </div>
        <a href="{{ route('branches.index') }}" class="inline-flex items-center gap-2 rounded-3xl bg-slate-900/80 border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Photo Section -->
            @if($branch->photo)
            <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Foto Perusahaan</h2>
                <img src="{{ asset('storage/' . $branch->photo) }}" alt="{{ $branch->name }}" class="w-full h-auto rounded-2xl object-cover max-h-96">
            </div>
            @endif

            <!-- Basic Information -->
            <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Informasi Dasar</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-start pb-4 border-b border-slate-800">
                        <div>
                            <p class="text-sm text-slate-400">Nama Cabang</p>
                            <p class="text-lg font-semibold text-white">{{ $branch->name }}</p>
                        </div>
                        <span class="inline-block px-3 py-1 text-xs rounded-full bg-slate-800 text-slate-300">
                            {{ ucfirst($branch->office_type) ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start pb-4 border-b border-slate-800">
                        <div>
                            <p class="text-sm text-slate-400">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $branch->status === 'aktif' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                {{ ucfirst($branch->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-start pb-4 border-b border-slate-800">
                        <div>
                            <p class="text-sm text-slate-400">Email</p>
                            <p class="text-base text-white">{{ $branch->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-start pb-4 border-b border-slate-800">
                        <div>
                            <p class="text-sm text-slate-400">Telepon</p>
                            <p class="text-base text-white">{{ $branch->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-slate-400">Tanggal Dibuat</p>
                        <p class="text-base text-white">{{ $branch->created_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Informasi Lokasi</h2>
                <div class="space-y-4">
                    <div class="pb-4 border-b border-slate-800">
                        <p class="text-sm text-slate-400 mb-2">Alamat Lengkap</p>
                        <p class="text-base text-white">{{ $branch->address ?? '-' }}</p>
                    </div>

                    <div class="flex gap-4 pb-4 border-b border-slate-800">
                        <div class="flex-1">
                            <p class="text-sm text-slate-400 mb-2">Kota / Kabupaten</p>
                            <p class="text-base text-white">{{ $branch->city ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pb-4 border-b border-slate-800">
                        <div>
                            <p class="text-sm text-slate-400 mb-2">Latitude</p>
                            <p class="text-sm text-white font-mono">{{ $branch->latitude ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-2">Longitude</p>
                            <p class="text-sm text-white font-mono">{{ $branch->longitude ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-2">Radius</p>
                            <p class="text-sm text-white">{{ $branch->radius ? $branch->radius . ' m' : '-' }}</p>
                        </div>
                    </div>

                    @if($branch->latitude && $branch->longitude)
                    <div id="map" class="w-full h-64 rounded-2xl border border-slate-800 bg-slate-900/80"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info Cabang -->
<div id="infoModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-sm w-full">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Informasi Cabang</h2>
                <button type="button" onclick="closeInfoModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="infoModalContent" class="mb-6"></div>

            <button type="button" onclick="closeInfoModal()" class="w-full rounded-3xl bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-700 transition">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Hapus Cabang -->
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

<!-- Leaflet Map Script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    let deleteFormBranchId = null;

    function openDeleteModal(branchId, branchName) {
        deleteFormBranchId = branchId;
        document.getElementById('deleteModalContent').innerHTML = `Apakah Anda yakin ingin menghapus cabang <strong>"${branchName}"</strong>? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteFormBranchId = null;
    }

    function openInfoModal(branchId, branchName, email, phone, city) {
        const infoContent = `
            <div class="space-y-3">
                <div class="pb-3 border-b border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Nama Cabang</p>
                    <p class="text-sm font-semibold text-white">${branchName}</p>
                </div>
                <div class="pb-3 border-b border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Email</p>
                    <p class="text-sm text-white">${email}</p>
                </div>
                <div class="pb-3 border-b border-slate-700">
                    <p class="text-xs text-slate-400 mb-1">Telepon</p>
                    <p class="text-sm text-white">${phone}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Kota</p>
                    <p class="text-sm text-white">${city}</p>
                </div>
            </div>
        `;
        document.getElementById('infoModalContent').innerHTML = infoContent;
        document.getElementById('infoModal').classList.remove('hidden');
    }

    function closeInfoModal() {
        document.getElementById('infoModal').classList.add('hidden');
    }

    function confirmDelete() {
        if (deleteFormBranchId) {
            document.getElementById('deleteForm').action = `/branches/${deleteFormBranchId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.getElementById('infoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeInfoModal();
        }
    });

    // Initialize map if coordinates exist
    @if($branch->latitude && $branch->longitude)
    const map = L.map('map').setView([{{ $branch->latitude }}, {{ $branch->longitude }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    L.marker([{{ $branch->latitude }}, {{ $branch->longitude }}]).addTo(map);

    @if($branch->radius)
    L.circle([{{ $branch->latitude }}, {{ $branch->longitude }}], {
        color: 'blue',
        fillColor: '#3b82f6',
        fillOpacity: 0.1,
        weight: 2,
        radius: {{ $branch->radius }},
    }).addTo(map);
    @endif
    @endif
</script>
@endsection
