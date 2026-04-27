@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Riwayat Pergerakan Stok</h1>
        </div>
    </div>

    <div class="rounded-3xl bg-slate-950/80 border border-slate-800 p-6 flex flex-col h-auto">
        <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-300 mb-3">Pilih Cabang</label>
            <form method="GET" action="{{ route('stock-movements.index') }}" class="flex gap-3">
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

        @if($selectedBranch && $movements->count() > 0)
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-white">Pergerakan Stok</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="border-b border-slate-800 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Referensi</th>
                                <th class="px-4 py-3">Info</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($movements as $movement)
                                <tr class="hover:bg-slate-900/70 transition">
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $movement->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $movement->product->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $movement->product->sku }}</td>
                                    <td class="px-4 py-3">
                                        @if($movement->type === 'IN')
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-emerald-500/20 text-emerald-300">
                                                ↓ Masuk
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-rose-500/20 text-rose-300">
                                                ↑ Keluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold">
                                        <span class="inline-block px-2 py-1 rounded bg-slate-900 border 
                                            {{ $movement->type === 'IN' ? 'border-emerald-500 text-emerald-300' : 'border-rose-500 text-rose-300' }}">
                                            {{ $movement->qty }} unit
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-medium text-slate-400">
                                            {{ ucfirst($movement->reference) }}
                                            @if($movement->reference_id)
                                                <span class="text-slate-500">#{{ $movement->reference_id }}</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-400 text-xs">
                                        @php
                                            $date = $movement->created_at;
                                            $now = now();
                                            $diff = $now->diffForHumans($date);
                                        @endphp
                                        {{ $diff }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($movements instanceof \Illuminate\Pagination\Paginator)
                    <div class="mt-6 pt-6 border-t border-slate-800">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
        @elseif($selectedBranch)
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-slate-400">Belum ada pergerakan stok untuk cabang ini.</p>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <p class="text-slate-400">Pilih cabang untuk melihat riwayat pergerakan stok.</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div ">
        <p class="text-sm text-slate-400">Lihat semua perubahan stok barang untuk setiap cabang.</p>
        {{-- <h3 class="font-semibold text-blue-200 mb-2">💡 Informasi</h3>
        <ul class="text-sm text-blue-200 space-y-1 list-disc list-inside">
            <li><strong>Masuk (↓)</strong> = Stok bertambah dari pembelian, retur penjualan, atau adjustment</li>
            <li><strong>Keluar (↑)</strong> = Stok berkurang dari penjualan, retur pembelian, atau adjustment</li>
            <li>Setiap perubahan stok dicatat dengan referensi sumber perubahan</li>
            <li>Gunakan untuk audit trail dan verifikasi akurasi stok</li>
        </ul> --}}
    </div>
</div>

@endsection
