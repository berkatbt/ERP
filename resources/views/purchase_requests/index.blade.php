@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold mb-4">Purchase Request</h1>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-200 p-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- FORM BUAT PR (WAREHOUSE) --}}
    {{-- ========================= --}}
    @if(auth()->user()->role == 'warehouse')
    <div class="bg-white p-4 mb-6 shadow">
        <h2 class="font-bold mb-2">Buat Request Pembelian</h2>

        <form method="POST" action="{{ route('purchase-requests.store') }}">
            @csrf

            <div id="products">
                <div class="flex gap-2 mb-2">
                    <select name="products[0][id]" class="border p-1">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number" name="products[0][qty]" placeholder="Qty" class="border p-1" required>
                </div>
            </div>

            <textarea name="note" class="border p-1 w-full mb-2" placeholder="Catatan"></textarea>

            <button class="bg-blue-500 text-white px-3 py-1">
                Submit
            </button>
        </form>
    </div>
    @endif

    {{-- ========================= --}}
    {{-- LIST PR --}}
    {{-- ========================= --}}
    <div class="bg-white p-4 shadow">
        <h2 class="font-bold mb-4">Daftar PR</h2>

        @foreach($prs as $pr)
            <div class="border p-3 mb-3">

                <div class="flex justify-between">
                    <div>
                        <b>ID:</b> {{ $pr->id }} <br>
                        <b>Status:</b> {{ $pr->status }} <br>
                        <b>Cabang:</b> {{ $pr->branch_id }} <br>
                        <b>By:</b> {{ $pr->user->name ?? '-' }}
                    </div>
                </div>

                {{-- DETAIL PRODUK --}}
                <div class="mt-2">
                    <b>Produk:</b>
                    <ul>
                        @foreach($pr->details as $d)
                            <li>
                                {{ $d->product->name }} - Qty: {{ $d->qty }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ========================= --}}
                {{-- ACTION BUTTON --}}
                {{-- ========================= --}}
                @if(auth()->user()->role == 'manager' && $pr->status == 'pending')

                    <div class="mt-3 flex gap-2">

                        {{-- APPROVE --}}
                        <form method="POST" action="{{ route('purchase-requests.approve', $pr->id) }}">
                            @csrf
                            <button class="bg-green-500 text-white px-2 py-1">
                                Approve
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form method="POST" action="{{ route('purchase-requests.reject', $pr->id) }}">
                            @csrf
                            <button class="bg-red-500 text-white px-2 py-1">
                                Reject
                            </button>
                        </form>

                    </div>

                @endif

            </div>
        @endforeach

    </div>

</div>
@endsection
