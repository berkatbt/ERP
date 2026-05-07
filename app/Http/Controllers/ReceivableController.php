<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function pay(Request $request, Receivable $receivable)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // 🔥 VALIDASI PEMBAYARAN
        if ($request->amount > $receivable->remaining_amount) {
            return back()->with('error', 'Pembayaran melebihi sisa piutang');
        }

        // 🔥 SIMPAN PEMBAYARAN
        $receivable->payments()->create([
            'amount' => $request->amount,
            'payment_date' => now(),
            'user_id' => auth()->id(),
        ]);

        // 🔥 UPDATE PIUTANG
        $receivable->paid_amount += $request->amount;
        $receivable->remaining_amount -= $request->amount;

        // 🔥 UPDATE STATUS
        if ($receivable->remaining_amount <= 0) {

            $receivable->status = 'paid';

            // update sale jadi paid
            $receivable->sale->update([
                'status' => 'paid',
            ]);

        } else {

            $receivable->status = 'partial';
        }

        $receivable->save();

        return back()->with('success', 'Pembayaran berhasil');
    }

    public function index()
    {
        $receivables = Receivable::with([
            'sale',
            'branch',
        ])->latest()->get();

        return view('receivables.index', compact('receivables'));
    }
}
