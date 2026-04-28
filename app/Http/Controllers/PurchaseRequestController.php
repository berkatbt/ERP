<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $prs = PurchaseRequest::with('details.product', 'user')
            ->latest()
            ->get();

        $products = Product::all();

        return view('purchase_requests.index', compact('prs', 'products'));
    }

    // Warehouse buat PR
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role->name !== 'Warehouse Admin') {
            abort(403, 'Hanya warehouse yang bisa membuat PR');
        }

        $request->validate([
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $pr = PurchaseRequest::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'status' => 'pending',
            'note' => $request->note
        ]);

        foreach ($request->products as $item) {
            $pr->details()->create([
                'product_id' => $item['id'],
                'qty' => $item['qty']
            ]);
        }

        return back()->with('success', 'Purchase Request berhasil dibuat');
    }

    // Approve
    public function approve($id)
    {
        $user = auth()->user();
        $pr = PurchaseRequest::findOrFail($id);

        if (!in_array($user->role->name, ['manager', 'owner'])) {
            abort(403);
        }

        if ($user->role->name === 'manager' && $pr->branch_id !== $user->branch_id) {
            abort(403);
        }

        if ($pr->status !== 'pending') {
            return back()->with('error', 'Purchase Request sudah diproses');
        }

        $pr->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now()
        ]);

        return back()->with('success', 'Purchase Request disetujui');
    }

    // Reject
    public function reject(Request $request, $id)
    {
        $user = auth()->user();
        $pr = PurchaseRequest::findOrFail($id);

        if (!in_array($user->role->name, ['manager', 'owner'])) {
            abort(403);
        }

        if ($user->role->name === 'manager' && $pr->branch_id !== $user->branch_id) {
            abort(403);
        }

        if ($pr->status !== 'pending') {
            return back()->with('error', 'Purchase Request sudah diproses');
        }

        $pr->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_note' => $request->note
        ]);

        return back()->with('success', 'Purchase Request ditolak');
    }
}
