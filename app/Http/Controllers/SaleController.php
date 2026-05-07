<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Receivable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\ManagesStockMovement;

class SaleController extends Controller
{
    use ManagesStockMovement;

    /**
     * Halaman kasir
     */
    public function index()
    {
        $user = Auth::user();

        // Allow all authenticated users to access sales
        $allowedRoles = ['owner', 'manager', 'warehouse admin', 'finance admin', 'cashier'];
        $userRole = strtolower($user->role->name ?? '');
        
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $products = Product::where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('sales.index', compact('products'));
    }

    /**
     * Simpan transaksi penjualan
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Allow all authenticated users to process sales
        $allowedRoles = ['owner', 'manager', 'warehouse admin', 'finance admin', 'cashier'];
        $userRole = strtolower($user->role->name ?? '');
        
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'payment_type' => 'required|in:cash,credit',
        ]);

        DB::beginTransaction();

        try {

            $total = 0;

            /**
             * VALIDASI STOCK + HITUNG TOTAL
             */
            foreach ($request->products as $item) {

                $product = Product::findOrFail($item['id']);

                $stock = Stock::where('product_id', $product->id)
                    ->where('branch_id', $user->branch_id)
                    ->first();

                if (!$stock || $stock->stock < $item['qty']) {
                    throw new \Exception(
                        "Stock {$product->name} tidak mencukupi"
                    );
                }

                $total += $product->price_sell * $item['qty'];
            }

            /**
             * SIMPAN SALE
             */
            $sale = Sale::create([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'invoice' => 'INV-' . time(),
                'total' => $total,
                'payment_type' => $request->payment_type,
                'status' => $request->payment_type === 'cash'
                    ? 'paid'
                    : 'unpaid',
            ]);

            /**
             * JIKA CREDIT
             */
            if ($request->payment_type === 'credit') {

                \App\Models\Receivable::create([
                    'sale_id' => $sale->id,
                    'branch_id' => $user->branch_id,
                    'total_debt' => $total,
                    'paid_amount' => 0,
                    'remaining_amount' => $total,
                    'status' => 'unpaid',
                    'due_date' => now()->addDays(30)
                ]);
            }

            /**
             * SIMPAN DETAIL + KURANGI STOCK
             */
            foreach ($request->products as $item) {

                $product = Product::findOrFail($item['id']);

                $subtotal = $product->price_sell * $item['qty'];

                // DETAIL SALE
                $sale->details()->create([
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price_sell,
                    'subtotal' => $subtotal,
                ]);

                // UPDATE STOCK
                $stock = Stock::where('product_id', $product->id)
                    ->where('branch_id', $user->branch_id)
                    ->first();

                $stock->decrement('stock', $item['qty']);

                // STOCK MOVEMENT
                $this->recordStockMovement(
                    $product->id,
                    $user->branch_id,
                    'OUT',
                    $item['qty'],
                    'Penjualan',
                    $sale->id
                );
            }

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Transaksi berhasil');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}