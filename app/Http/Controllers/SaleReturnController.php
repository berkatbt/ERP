<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'reason' => 'nullable|string',
            'products' => 'required|array'
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        try {

            $sale = Sale::with('details')->findOrFail($request->sale_id);

            // CREATE RETURN
            $return = SaleReturn::create([
                'sale_id' => $sale->id,
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'reason' => $request->reason
            ]);

            foreach ($request->products as $item) {

                // VALIDASI PRODUK
                if (!isset($item['id']) || !isset($item['qty'])) {
                    throw new \Exception('Data produk tidak valid');
                }

                // DETAIL RETURN
                $return->details()->create([
                    'product_id' => $item['id'],
                    'qty' => $item['qty']
                ]);

                // CARI STOCK
                $stock = Stock::where('product_id', $item['id'])
                    ->where('branch_id', $user->branch_id)
                    ->first();

                // JIKA STOCK BELUM ADA
                if (!$stock) {
                    $stock = Stock::create([
                        'product_id' => $item['id'],
                        'branch_id' => $user->branch_id,
                        'stock' => 0
                    ]);
                }

                // TAMBAH STOCK
                $stock->increment('stock', $item['qty']);

                // OPTIONAL STOCK MOVEMENT
                if (method_exists($this, 'recordStockMovement')) {

                    $this->recordStockMovement(
                        $item['id'],
                        $user->branch_id,
                        'IN',
                        $item['qty'],
                        'Retur Penjualan',
                        $sale->id
                    );
                }
            }

            DB::commit();

            return back()->with('success', 'Retur berhasil');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}