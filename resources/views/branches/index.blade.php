@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Manajemen Cabang</h1>
            <p class="text-sm text-slate-400">Atur semua cabang usaha di sistem ERP. Hanya Owner yang dapat menambahkan cabang baru.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/80 border border-slate-800 px-4 py-3 text-sm text-slate-300">
            Total cabang: {{ $branches->count() }}</div>
    </div>

    @if(session('success'))
        <div class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-white">Daftar Cabang</h2>
        <button type="button" id="openModalBtn" class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">
            Tambah Cabang Baru
        </button>
    </div>

    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-[600px]">
        <h2 class="text-lg font-semibold text-white mb-4">Daftar Cabang</h2>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada cabang.</td>
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
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
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
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="phone">No. Telepon</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="city">Kota / Kabupaten</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('city')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-2" for="photo">Foto Perusahaan</label>
                    <input id="photo" name="photo" type="file" accept="image/*" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Format: JPEG, PNG, GIF (Maks 2MB)</p>
                    @error('photo')
                        <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="latitude">Latitude</label>
                        <input id="latitude" name="latitude" type="number" step="0.00000001" value="{{ old('latitude') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="longitude">Longitude</label>
                        <input id="longitude" name="longitude" type="number" step="0.00000001" value="{{ old('longitude') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="radius">Radius (Meter)</label>
                        <input id="radius" name="radius" type="number" value="{{ old('radius') }}" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="500">
                        @error('radius')
                            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="map" class="w-full h-64 rounded-3xl border border-slate-800 bg-slate-900/80"></div>
                <p class="text-xs text-slate-400">Klik pada peta untuk mengatur lokasi atau masukkan koordinat secara manual</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-2" for="office_type">Tipe Kantor</label>
                        <select id="office_type" name="office_type" class="w-full rounded-3xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Tipe Kantor</option>
                            <option value="kantor pusat" {{ old('office_type') == 'kantor pusat' ? 'selected' : '' }}>Kantor Pusat</option>
                            <option value="cabang" {{ old('office_type') == 'cabang' ? 'selected' : '' }}>Cabang</option>
                            <option value="gudang" {{ old('office_type') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            <option value="toko" {{ old('office_type') == 'toko' ? 'selected' : '' }}>Toko</option>
                            <option value="lainnya" {{ old('office_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('office_type')
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
                    <button type="submit" class="flex-1 rounded-3xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-400 transition">Buat Cabang</button>
                    <button type="button" class="flex-1 rounded-3xl border border-slate-800 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-900 transition" onclick="closeModal()">Batal</button>
                </div>
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

        // Modal functions
        function openModal() {
            document.getElementById('branchModal').classList.remove('hidden');
            // Initialize map when modal is opened
            setTimeout(initializeMap, 100);
        }

        function closeModal() {
            document.getElementById('branchModal').classList.add('hidden');
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
    </script>
    </div>
</div>
@endsection
