@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">
        Daftar Piutang
    </h1>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR --}}
    @if(session('error'))
        <div class="bg-red-200 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full border-collapse">

            <thead class="bg-gray-100">
                <tr>

                    <th class="p-3 text-left border">
                        Invoice
                    </th>

                    <th class="p-3 text-left border">
                        Cabang
                    </th>

                    <th class="p-3 text-left border">
                        Total Hutang
                    </th>

                    <th class="p-3 text-left border">
                        Sudah Dibayar
                    </th>

                    <th class="p-3 text-left border">
                        Sisa
                    </th>

                    <th class="p-3 text-left border">
                        Status
                    </th>

                    <th class="p-3 text-left border">
                        Jatuh Tempo
                    </th>

                    <th class="p-3 text-center border">
                        Aksi
                    </th>

                </tr>
            </thead>

            <tbody>

                @forelse($receivables as $receivable)

                    <tr class="hover:bg-gray-50">

                        {{-- Invoice --}}
                        <td class="p-3 border">
                            {{ $receivable->sale->invoice }}
                        </td>

                        {{-- Branch --}}
                        <td class="p-3 border">
                            {{ $receivable->branch->name ?? '-' }}
                        </td>

                        {{-- Total --}}
                        <td class="p-3 border">
                            Rp {{ number_format($receivable->total_debt, 0, ',', '.') }}
                        </td>

                        {{-- Paid --}}
                        <td class="p-3 border text-green-600">
                            Rp {{ number_format($receivable->paid_amount, 0, ',', '.') }}
                        </td>

                        {{-- Remaining --}}
                        <td class="p-3 border text-red-600">
                            Rp {{ number_format($receivable->remaining_amount, 0, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="p-3 border">

                            @if($receivable->status == 'paid')

                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">
                                    LUNAS
                                </span>

                            @elseif($receivable->status == 'partial')

                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">
                                    CICILAN
                                </span>

                            @else

                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-sm">
                                    BELUM BAYAR
                                </span>

                            @endif

                        </td>

                        {{-- Due Date --}}
                        <td class="p-3 border">
                            {{ $receivable->due_date }}
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3 border">

                            @if($receivable->status != 'paid')

                                <form method="POST"
                                      action="{{ route('receivables.pay', $receivable->id) }}"
                                      class="flex gap-2">

                                    @csrf

                                    <input type="number"
                                           name="amount"
                                           min="1"
                                           max="{{ $receivable->remaining_amount }}"
                                           class="border p-1 w-28 rounded"
                                           placeholder="Nominal"
                                           required>

                                    <button class="bg-blue-600 text-white px-3 py-1 rounded">
                                        Bayar
                                    </button>

                                </form>

                            @else

                                <span class="text-green-600 font-semibold">
                                    Selesai
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center p-6 text-gray-500">
                            Belum ada data piutang
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection