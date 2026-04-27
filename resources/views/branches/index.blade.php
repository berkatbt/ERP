@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center mb-4 ">
        <h2 class="text-lg font-semibold text-white ">Daftar kantor</h2>
        {{-- <div class="absolute right-45 text-sm text-white-400 bg-blue-500/10 h-10 px-4 py-2 rounded-sm center-justify">
            kantor : {{ $branches->count() }}
        </div> --}}
        <button type="button" id="openModalBtn" class="rounded-sm bg-blue-500/50 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">
            Tambah
        </button>
    </div>
 
    @if(session('success'))
        <div id="successNotification" class="text-green-500">
            {{ session('success') }}
        </div>
 
    @endif

    <div class="rounded-sm bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-[600px]">
        <div class="overflow-x-auto flex-1">
                <table class="min-w-full text-left text-sm text-slate-200">
                    <thead class="border-b border-slate-800 text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Nama Cabang</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Telepon</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Dibuat</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($branches as $branch)
                            <tr class="hover:bg-slate-900/70 transition">
                                <td class="px-4 py-3 font-medium">{{ $branch->name }}</td>
                                <td class="px-4 py-3">{{ $branch->email ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $branch->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-slate-800 text-slate-300">
                                        {{ ucfirst($branch->office_type) ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full {{ $branch->status === 'aktif' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                        {{ ucfirst($branch->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $branch->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <button onclick="openDetailModal({{ $branch->id }})" title="Detail" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="openEditModal({{ $branch->id }})" title="Edit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="openInfoModal({{ $branch->id }}, '{{ $branch->name }}', '{{ $branch->email ?? '-' }}', '{{ $branch->phone ?? '-' }}', '{{ $branch->city ?? '-' }}')" title="Info" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500/30 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="openDeleteModal({{ $branch->id }}, '{{ $branch->name }}')" title="Hapus" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada cabang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Cabang Baru - Outside Main Container -->
<div id="branchModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Tambah Cabang Baru</h2>
                <button type="button" id="closeModalBtn" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('branches.store') }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="name">Nama Cabang <span class="text-rose-400">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="phone">No. Telepon</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="city">Kota / Kabupaten</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('city')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="photo">Foto Perusahaan</label>
                    <input id="photo" name="photo" type="file" accept="image/*" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Format: JPEG, PNG, GIF (Maks 2MB)</p>
                    @error('photo')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="latitude">Latitude</label>
                        <input id="latitude" name="latitude" type="number" step="0.00000001" value="{{ old('latitude') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="longitude">Longitude</label>
                        <input id="longitude" name="longitude" type="number" step="0.00000001" value="{{ old('longitude') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="radius">Radius (Meter)</label>
                        <input id="radius" name="radius" type="number" value="{{ old('radius') }}" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="500">
                        @error('radius')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="map" class="w-full h-64 rounded-2xl border border-slate-800 bg-slate-900/80"></div>
                <p class="text-xs text-slate-400">Klik pada peta untuk mengatur lokasi atau masukkan koordinat secara manual</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="office_type">Tipe Kantor</label>
                        <select id="office_type" name="office_type" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Tipe Kantor</option>
                            <option value="kantor pusat" {{ old('office_type') == 'kantor pusat' ? 'selected' : '' }}>Kantor Pusat</option>
                            <option value="cabang" {{ old('office_type') == 'cabang' ? 'selected' : '' }}>Cabang</option>
                            {{-- <option value="gudang" {{ old('office_type') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            <option value="toko" {{ old('office_type') == 'toko' ? 'selected' : '' }}>Toko</option>
                            <option value="lainnya" {{ old('office_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option> --}}
                        </select>
                        @error('office_type')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="status">Status <span class="text-rose-400">*</span></label>
                        <select id="status" name="status" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ old('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="ml-90 flex gap-4 pt-4 ">
                    <button type="button" class="w-30 rounded-md border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeModal()">Batal</button>
                    <button type="submit" class="w-30 rounded-md bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Buat Cabang</button>
                </div>
            </form>
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

<!-- Modal Detail Cabang -->
<div id="detailModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Detail Cabang</h2>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="detailModalContent" class="mb-6 space-y-4"></div>

            <div class="flex gap-3">
                <button type="button" onclick="closeDetailModal()" class="flex-1 rounded-3xl bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-700 transition">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Cabang -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-white">Edit Cabang</h2>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_name">Nama Cabang <span class="text-rose-400">*</span></label>
                        <input id="edit_name" name="name" type="text" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_phone">No. Telepon</label>
                        <input id="edit_phone" name="phone" type="tel" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_email">Email</label>
                        <input id="edit_email" name="email" type="email" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_city">Kota / Kabupaten</label>
                        <input id="edit_city" name="city" type="text" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="edit_address">Alamat Lengkap</label>
                    <textarea id="edit_address" name="address" rows="3" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="edit_photo">Foto Perusahaan</label>
                    <input id="edit_photo" name="photo" type="file" accept="image/*" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Format: JPEG, PNG, GIF (Maks 2MB)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_latitude">Latitude</label>
                        <input id="edit_latitude" name="latitude" type="number" step="0.00000001" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="-6.2088">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_longitude">Longitude</label>
                        <input id="edit_longitude" name="longitude" type="number" step="0.00000001" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="106.8456">
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_radius">Radius (Meter)</label>
                        <input id="edit_radius" name="radius" type="number" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="500">
                    </div>
                </div>

                <div id="editMap" class="w-full h-64 rounded-3xl border border-slate-800 bg-slate-900/80"></div>
                <p class="text-xs text-slate-400">Klik pada peta untuk mengatur lokasi atau masukkan koordinat secara manual</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_office_type">Tipe Kantor</label>
                        <select id="edit_office_type" name="office_type" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Tipe Kantor</option>
                            <option value="kantor pusat">Kantor Pusat</option>
                             <option value="cabang">Cabang</option> 
                            {{-- <option value="gudang">Gudang</option>
                            <option value="toko">Toko</option>
                            <option value="lainnya">Lainnya</option>  --}}
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="edit_status">Status <span class="text-rose-400">*</span></label>
                        <select id="edit_status" name="status" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-4 ml-90">
                    <button type="button" class="w-30 rounded-md border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeEditModal()">batal</button>
                    <button type="submit" class="w-30 rounded-md bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">simpan</button>
                </div>
            </form>
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
        let map;
        let marker;
        const defaultLat = -6.2088;
        const defaultLng = 106.8456;
        let mapInitialized = false;
        let deleteFormBranchId = null;

        // Modal functions
        function openModal() {
            document.getElementById('branchModal').classList.remove('hidden');
            // Initialize map when modal is opened
            setTimeout(initializeMap, 100);
        }

        function closeModal() {
            document.getElementById('branchModal').classList.add('hidden');
        }

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

        function openDetailModal(branchId) {
            const detailModal = document.getElementById('detailModal');
            const detailContent = document.getElementById('detailModalContent');
            
            // Show loading state
            detailContent.innerHTML = '<div class="text-center text-slate-300"><p>Loading...</p></div>';
            detailModal.classList.remove('hidden');

            // Fetch branch detail
            fetch(`/branches/${branchId}/detail`)
                .then(response => response.json())
                .then(branch => {
                    let html = `
                        <div class="pb-4 border-b border-slate-700">
                            <p class="text-xs text-slate-400 mb-2">Nama Cabang</p>
                            <div class="flex justify-between items-start">
                                <p class="text-lg font-semibold text-white">${branch.name}</p>
                                <span class="inline-block px-3 py-1 text-xs rounded-full bg-slate-800 text-slate-300">${branch.office_type}</span>
                            </div>
                        </div>

                        <div class="pb-4 border-b border-slate-700">
                            <p class="text-xs text-slate-400 mb-2">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-${branch.status_class}-500/20 text-${branch.status_class}-300">${branch.status}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-700">
                            <div>
                                <p class="text-xs text-slate-400 mb-2">Email</p>
                                <p class="text-sm text-white">${branch.email}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 mb-2">Telepon</p>
                                <p class="text-sm text-white">${branch.phone}</p>
                            </div>
                        </div>

                        <div class="pb-4 border-b border-slate-700">
                            <p class="text-xs text-slate-400 mb-2">Alamat Lengkap</p>
                            <p class="text-sm text-white">${branch.address}</p>
                        </div>

                        <div class="pb-4 border-b border-slate-700">
                            <p class="text-xs text-slate-400 mb-2">Kota / Kabupaten</p>
                            <p class="text-sm text-white">${branch.city}</p>
                        </div>
                    `;

                    // Add photo if exists
                    if (branch.photo) {
                        html += `
                            <div class="pb-4 border-b border-slate-700">
                                <p class="text-xs text-slate-400 mb-2">Foto Perusahaan</p>
                                <img src="${branch.photo}" alt="${branch.name}" class="w-full h-auto rounded-lg object-cover max-h-64">
                            </div>
                        `;
                    }

                    // Add location if exists
                    if (branch.latitude && branch.longitude) {
                        html += `
                            <div class="pb-4 border-b border-slate-700">
                                <p class="text-xs text-slate-400 mb-2">Koordinat Lokasi</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <p class="text-xs text-slate-500">Latitude</p>
                                        <p class="text-xs text-white font-mono">${branch.latitude}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Longitude</p>
                                        <p class="text-xs text-white font-mono">${branch.longitude}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Radius</p>
                                        <p class="text-xs text-white">${branch.radius ? branch.radius + ' m' : '-'}</p>
                                    </div>
                                </div>
                            </div>
                            <div id="detailMap" class="w-full h-48 rounded-lg border border-slate-700 mb-4"></div>
                        `;
                    }

                    html += `
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-slate-400">Dibuat</p>
                                <p class="text-white">${branch.created_at}</p>
                            </div>
                            <div>
                                <p class="text-slate-400">Diperbarui</p>
                                <p class="text-white">${branch.updated_at}</p>
                            </div>
                        </div>
                    `;

                    detailContent.innerHTML = html;

                    // Initialize map if coordinates exist
                    if (branch.latitude && branch.longitude) {
                        setTimeout(() => {
                            const detailMapContainer = document.getElementById('detailMap');
                            if (detailMapContainer) {
                                const detailMapInstance = L.map('detailMap').setView([branch.latitude, branch.longitude], 13);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© OpenStreetMap contributors',
                                    maxZoom: 19,
                                }).addTo(detailMapInstance);

                                L.marker([branch.latitude, branch.longitude]).addTo(detailMapInstance);

                                if (branch.radius && branch.radius > 0) {
                                    L.circle([branch.latitude, branch.longitude], {
                                        color: 'blue',
                                        fillColor: '#3b82f6',
                                        fillOpacity: 0.1,
                                        weight: 2,
                                        radius: branch.radius,
                                    }).addTo(detailMapInstance);
                                }
                            }
                        }, 100);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    detailContent.innerHTML = '<div class="text-center text-rose-300"><p>Gagal memuat detail cabang</p></div>';
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function openEditModal(branchId) {
            const editModal = document.getElementById('editModal');
            editModal.classList.remove('hidden');

            // Fetch branch data
            fetch(`/branches/${branchId}/edit`)
                .then(response => response.json())
                .then(branch => {
                    // Populate form fields
                    document.getElementById('edit_name').value = branch.name || '';
                    document.getElementById('edit_phone').value = branch.phone || '';
                    document.getElementById('edit_email').value = branch.email || '';
                    document.getElementById('edit_city').value = branch.city || '';
                    document.getElementById('edit_address').value = branch.address || '';
                    document.getElementById('edit_latitude').value = branch.latitude || '';
                    document.getElementById('edit_longitude').value = branch.longitude || '';
                    document.getElementById('edit_radius').value = branch.radius || '';
                    document.getElementById('edit_office_type').value = branch.office_type || '';
                    document.getElementById('edit_status').value = branch.status || '';

                    // Set form action
                    document.getElementById('editForm').action = `/branches/${branch.id}`;

                    // Initialize map
                    setTimeout(() => initializeEditMap(branch), 100);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data cabang');
                    closeEditModal();
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            if (editMapInstance) {
                editMapInstance.remove();
                editMapInstance = null;
            }
        }

        let editMapInstance = null;
        let editMarker = null;
        let editRadiusCircle = null;

        function initializeEditMap(branch) {
            const mapContainer = document.getElementById('editMap');
            if (!mapContainer) return;

            // Remove previous map if exists
            if (editMapInstance) {
                editMapInstance.remove();
            }

            const defaultLat = -6.2088;
            const defaultLng = 106.8456;
            const lat = branch.latitude ? parseFloat(branch.latitude) : defaultLat;
            const lng = branch.longitude ? parseFloat(branch.longitude) : defaultLng;

            editMapInstance = L.map('editMap').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(editMapInstance);

            if (branch.latitude && branch.longitude) {
                addEditMarker(lat, lng);
            }

            // Click on map to place marker
            editMapInstance.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                document.getElementById('edit_latitude').value = lat.toFixed(8);
                document.getElementById('edit_longitude').value = lng.toFixed(8);
                addEditMarker(lat, lng);
            });

            // Listen to radius input changes
            document.getElementById('edit_radius').addEventListener('change', updateEditMapRadius);

            // Initial radius circle
            if (branch.radius) {
                updateEditMapRadius();
            }
        }

        function addEditMarker(lat, lng) {
            if (editMarker) {
                editMapInstance.removeLayer(editMarker);
            }
            editMarker = L.marker([lat, lng]).addTo(editMapInstance);
            updateEditMapRadius();
        }

        function updateEditMapRadius() {
            const lat = parseFloat(document.getElementById('edit_latitude').value);
            const lng = parseFloat(document.getElementById('edit_longitude').value);
            const radius = parseInt(document.getElementById('edit_radius').value) || 0;

            if (editRadiusCircle) {
                editMapInstance.removeLayer(editRadiusCircle);
            }

            if (lat && lng && radius > 0) {
                editRadiusCircle = L.circle([lat, lng], {
                    color: 'blue',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1,
                    weight: 2,
                    radius: radius,
                }).addTo(editMapInstance);
            }
        }

        function confirmDelete() {
            if (deleteFormBranchId) {
                document.getElementById('deleteForm').action = `/branches/${deleteFormBranchId}`;
                document.getElementById('deleteForm').submit();
            }
        }

        // Event listeners for modal buttons
        document.getElementById('openModalBtn').addEventListener('click', openModal);
        document.getElementById('closeModalBtn').addEventListener('click', closeModal);

        // Close modal when clicking outside
        document.getElementById('branchModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
        document.getElementById('infoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInfoModal();
            }
        });

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Initialize map
        function initializeMap() {
            if (mapInitialized) return;
            
            const mapContainer = document.getElementById('map');
            if (!mapContainer || mapContainer.style.display === 'none') return;

            map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            mapInitialized = true;

            // Load existing coordinates if available
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');

            if (latInput.value && lngInput.value) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                map.setView([lat, lng], 13);
                addMarker(lat, lng);
            }

            // Click on map to place marker
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
                addMarker(lat, lng);
            });

            radiusInput.addEventListener('change', updateMapRadius);
        }

        function addMarker(lat, lng) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);
            updateMapRadius();
        }

        // Update radius circle on map
        let radiusCircle;

        function updateMapRadius() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');
            
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const radius = parseInt(radiusInput.value) || 0;

            if (radiusCircle) {
                map.removeLayer(radiusCircle);
            }

            if (lat && lng && radius > 0) {
                radiusCircle = L.circle([lat, lng], {
                    color: 'blue',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1,
                    weight: 2,
                    radius: radius,
                }).addTo(map);
            }
        }

        // Show modal if there are validation errors
        @if ($errors->any())
            openModal();
        @endif

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
    </div>
</div>

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
