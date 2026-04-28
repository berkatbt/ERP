<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Product;
use App\Models\TrackingPurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $prs = PurchaseRequest::with('details.product', 'user', 'tracking', 'latestTracking')
            ->latest()
            ->get();

        $products = Product::all();

        return view('purchase_requests.index', compact('prs', 'products'));
    }

    public function show($id)
    {
        $pr = PurchaseRequest::with([
            'user',
            'details.product',
            'tracking'
        ])->findOrFail($id);

        return view('purchase_requests.show', compact('pr'));
    }

    // Warehouse buat PR
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role->name !== 'Warehouse Admin') {
            abort(403, 'Hanya warehouse yang bisa membuat PR');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $pr = PurchaseRequest::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'status' => 'pending',
            'note' => $request->note
        ]);

        $pr->details()->create([
            'product_id' => $request->product_id,
            'qty' => $request->qty
        ]);

        // buat tracking
        TrackingPurchaseRequest::create([
            'purchase_request_id' => $pr->id,
            'tracking' => 'Menunggu Approval'
        ]);

        return back()->with('success', 'Purchase Request berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $pr = PurchaseRequest::findOrFail($id);

        $pr->update([
            'note' => $request->note ?? $pr->note
        ]);

        // approval dan rejected
        if ($request->status == 'approved') {
            $pr->update([
                'status' => 'Approved',
                'approved_at' => now()
            ]);

            TrackingPurchaseRequest::create([
                'user_id' => auth()->user()->id,
                'purchase_request_id' => $pr->id,
                'tracking' => 'Diapprove oleh manager'
            ]);
        } else if ($request->status == 'rejected') {
            $pr->update([
                'status' => 'rejected',
                'approved_at' => now()
            ]);

            TrackingPurchaseRequest::create([
                'user_id' => auth()->user()->id,
                'purchase_request_id' => $pr->id,
                'tracking' => 'Ditolak oleh manager'
            ]);
        }

        // update tracking
        if ($request->tracking) {
            TrackingPurchaseRequest::create([
                'user_id' => auth()->user()->id,
                'purchase_request_id' => $pr->id,
                'tracking' => $request->tracking
            ]);
        }

        // update detail
        if ($request->product_id && $request->qty) {
            $pr->details()->delete();
    
            $pr->details()->create([
                'product_id' => $request->product_id,
                'qty' => $request->qty
            ]);
        }

        return back()->with('success', 'Purchase Request berhasil diupdate');
    }

    public function destroy($id) 
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->delete();

        return back()->with('success', 'Purchase Request berhasil dihapus.');
    }
}
