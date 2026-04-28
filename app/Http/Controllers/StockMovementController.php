<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display stock movement history
     */
    public function index(Request $request)
    {
        // Authorization
        $user = auth()->user();
        if (!$user || !in_array(strtolower($user->role->name), ['owner', 'manager', 'warehouse admin'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Get all active branches
        $branches = Branch::where('status', 'aktif')->orderBy('name')->get();

        // Get selected branch
        $selectedBranch = $request->query('branch_id');

        // Get movements
        $movements = [];
        if ($selectedBranch) {
            $movements = StockMovement::where('branch_id', $selectedBranch)
                ->with(['product', 'branch'])
                ->latest()
                ->paginate(20);
        }

        return view('stock-movements.index', [
            'branches' => $branches,
            'movements' => $movements,
            'selectedBranch' => $selectedBranch,
        ]);
    }

    /**
     * Get stock movement summary for a product in a branch
     */
    public function summary(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        $movements = StockMovement::where('product_id', $request->product_id)
            ->where('branch_id', $request->branch_id)
            ->latest()
            ->limit(20)
            ->get();

        $inTotal = $movements->where('type', 'IN')->sum('qty');
        $outTotal = $movements->where('type', 'OUT')->sum('qty');

        return response()->json([
            'movements' => $movements,
            'in_total' => $inTotal,
            'out_total' => $outTotal,
            'net_movement' => $inTotal - $outTotal,
        ]);
    }
}
