<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Receivable;
use App\Models\AuditLog;

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
            ->with('stocks')
            ->whereHas('stocks', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $product->stock = $product->stocks->first();
                unset($product->stocks);

                return $product;
            });

        $sales = Sale::with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('sales.index', compact(
            'products',
            'sales'
        ));
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

                Receivable::create([
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
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price_sell,
                    'subtotal' => $subtotal,
                ]);

                // UPDATE STOCK + STOCK MOVEMENT
                $stock = Stock::where('product_id', $product->id)
                    ->where('branch_id', $user->branch_id)
                    ->first();

                $oldStock = $stock->stock;

                $this->recordStockMovement(
                    $product->id,
                    $user->branch_id,
                    'OUT',
                    $item['qty'],
                    'Penjualan',
                    $sale->id
                );

                $stock->refresh();

                if ($oldStock > ($product->min_stock ?? 0) && $stock->stock <= ($product->min_stock ?? 0)) {
                    AuditLog::create([
                        'user_id' => $user->id,
                        'branch_id' => $user->branch_id,
                        'auditable_type' => Product::class,
                        'auditable_id' => $product->id,
                        'action' => 'stock_minimum',
                        'description' => "Stok {$product->name} di cabang {$user->branch->name} telah mencapai minimum {$product->min_stock} unit.",
                        'old_values' => ['stock' => $oldStock],
                        'new_values' => ['stock' => $stock->stock],
                        'url' => route('sales.index'),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
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