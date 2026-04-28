@extends('layout.app')

@section('content')
<div class="text-white">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Detail Purchase Request</h1>
            <p class="text-slate-400 text-sm">Informasi lengkap pengajuan</p>
        </div>

        <a href="{{ route('purchase-requests.index') }}"
           class="rounded-3xl border border-slate-700 px-4 py-2 text-sm hover:bg-slate-800">
            ← Kembali
        </a>
    </div>

    {{-- CARD --}}
    <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 space-y-6">

        {{-- INFO --}}
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400">Diajukan Oleh</p>
                <p class="font-semibold">{{ $pr->user->name }}</p>
            </div>

            <div>
                <p class="text-slate-400">Status</p>
                <span class="px-2 py-1 text-xs rounded-full 
                    {{ $pr->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                    {{ ucfirst($pr->status) }}
                </span>
            </div>

            <div>
                <p class="text-slate-400">Tanggal</p>
                <p class="font-semibold">
                    {{ $pr->created_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>

        {{-- PRODUK (single) --}}
        <div>
            <h3 class="text-lg font-semibold mb-3">Produk</h3>

            @php $d = $pr->details->first(); @endphp

            <div class="bg-slate-900/70 rounded-2xl p-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold text-white">
                        {{ $d->product->name ?? '-' }}
                    </p>
                    <p class="text-sm text-slate-400">
                        Qty: {{ $d->qty ?? 0 }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-slate-400">Harga</p>
                    <p class="font-semibold">
                        Rp {{ number_format($d->product->price_buy ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- TOTAL --}}
        <div class="text-right font-semibold text-lg">
            Total: Rp {{ number_format(($d->qty ?? 0) * ($d->product->price_buy ?? 0), 0, ',', '.') }}
        </div>

        {{-- NOTE --}}
        <div>
            <p class="text-slate-400 text-sm mb-1">Catatan</p>
            <div class="bg-slate-900/70 rounded-2xl p-4 text-sm">
                {{ $pr->note ?? '-' }}
            </div>
        </div>

        {{-- TRACKING (FULL WIDTH TIMELINE) --}}
        <div>
            <h3 class="text-lg font-semibold mb-3">Tracking</h3>

            @if($pr->tracking->isEmpty())
                <p class="text-sm text-slate-400">Belum ada tracking</p>
            @else
                <div class="relative pl-4 border-l border-slate-700 space-y-4">
                    @foreach($pr->tracking as $t)
                        <div class="flex gap-3 items-center">
                            {{-- DOT --}}
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>

                            {{-- CONTENT --}}
                            <div class="">
                                <p class="text-sm font-semibold text-white">
                                    {{ $t->tracking }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $t->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection