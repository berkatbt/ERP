<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Branch;
use App\Models\Product;
use App\Traits\ManagesStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    use ManagesStockMovement;

    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager', 'warehouse'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $branches = Branch::where('status', 'aktif')->orderBy('name')->get();
        
        // Get selected branch from request or use first branch
        $selectedBranch = $request->get('branch_id');
        if (!$selectedBranch && $branches->count() > 0) {
            $selectedBranch = $branches->first()->id;
        }

        $stocks = collect();
        if ($selectedBranch) {
            $stocks = Stock::where('branch_id', $selectedBranch)
                ->with('product')
                ->orderBy('id')
                ->get();
        }

        $availableProducts = Product::where('status', 'aktif')->orderBy('name')->get();

        return view('stocks.index', [
            'branches' => $branches,
            'stocks' => $stocks,
            'selectedBranch' => $selectedBranch,
            'availableProducts' => $availableProducts,
        ]);
    }

    /**
     * Store a new stock for a product in a branch
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager', 'warehouse'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::unique('stocks')->where(function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
            ],
            'branch_id' => 'required|integer|exists:branches,id',
            'stock' => 'required|integer|min:0',
        ]);

        $stock = Stock::create($data);

        // Record initial stock movement
        $this->recordStockMovement(
            $stock->product_id,
            $stock->branch_id,
            'IN',
            $stock->stock,
            'Inisial Stok',
            null
        );

        return redirect()->route('stocks.index', ['branch_id' => $data['branch_id']])
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    /**
     * Get stock data for edit
     */
    public function edit(Stock $stock)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager', 'warehouse'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return response()->json([
            'id' => $stock->id,
            'product_id' => $stock->product_id,
            'branch_id' => $stock->branch_id,
            'stock' => $stock->stock,
            'product_name' => $stock->product->name,
            'product_sku' => $stock->product->sku,
        ]);
    }

    /**
     * Update stock quantity (with adjustment recording)
     */
    public function update(Request $request, Stock $stock)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager', 'warehouse'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $oldStock = $stock->stock;
        $newStock = $data['stock'];
        $difference = $newStock - $oldStock;

        // Update stock
        $stock->update($data);

        // Record adjustment if there's a difference
        if ($difference !== 0) {
            $type = $difference > 0 ? 'IN' : 'OUT';
            $quantity = abs($difference);

            $this->recordStockMovement(
                $stock->product_id,
                $stock->branch_id,
                $type,
                $quantity,
                'Adjustment',
                null
            );
        }

        return redirect()->route('stocks.index', ['branch_id' => $stock->branch_id])
            ->with('success', 'Stok berhasil diperbarui.');
    }

    /**
     * Delete a stock record
     */
    public function destroy(Stock $stock)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan. Hanya Manager atau Owner yang dapat menghapus stok.');
        }

        $productName = $stock->product->name;
        $branchId = $stock->branch_id;
        $stock->delete();

        return redirect()->route('stocks.index', ['branch_id' => $branchId])
            ->with('success', "Stok produk '$productName' berhasil dihapus.");
    }
}
