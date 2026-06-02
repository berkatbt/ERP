@extends('layout.app')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-white">Notifikasi Stok Minimum</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-400">Lihat produk yang telah mencapai atau berada di bawah batas minimum stok per cabang.</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-950 px-4 py-3 text-sm text-slate-300 shadow-inner">
            Total notifikasi: <span class="font-semibold text-white">{{ $notifications->total() }}</span>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-xl">
        <div class="hidden grid-cols-12 gap-4 px-6 py-4 text-xs uppercase tracking-[0.2em] text-slate-500 border-b border-white/10 lg:grid">
            <div class="col-span-3">Produk</div>
            <div class="col-span-3">Cabang</div>
            <div class="col-span-5">Keterangan</div>
            <div class="col-span-1 text-right">Tanggal</div>
        </div>

        @forelse($notifications as $notification)
            <div class="grid grid-cols-1 gap-4 px-6 py-5 transition hover:bg-white/5 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-3">
                    <p class="font-semibold text-white">{{ $notification->auditable->name ?? 'Produk tidak ditemukan' }}</p>
                    <p class="mt-1 text-xs text-slate-500">ID: {{ $notification->auditable_id }}</p>
                </div>

                <div class="lg:col-span-3">
                    <p class="font-semibold text-slate-100">{{ $notification->branch->name ?? 'Cabang tidak tersedia' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $notification->branch->city ?? '' }}</p>
                </div>

                <div class="lg:col-span-5 text-slate-300">
                    {{ $notification->description }}
                </div>

                <div class="lg:col-span-1 text-right text-slate-500">
                    {{ $notification->created_at->format('d M Y') }}
                    <div class="mt-1 text-xs text-slate-400">{{ $notification->created_at->format('H:i') }}</div>
                </div>
            </div>
            <div class="border-t border-white/10"></div>
        @empty
            <div class="px-6 py-8 text-center text-slate-400">
                Belum ada notifikasi stok minimum.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endsection
