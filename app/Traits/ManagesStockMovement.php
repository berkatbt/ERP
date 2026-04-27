<?php

namespace App\Traits;

use App\Models\Stock;
use App\Models\StockMovement;

trait ManagesStockMovement
{
    /**
     * Record a stock movement and update stock quantity
     * 
     * @param int $productId - Product ID
     * @param int $branchId - Branch ID
     * @param string $type - Movement type: 'IN' or 'OUT'
     * @param int $quantity - Quantity to move
     * @param string $reference - Reference type (e.g., 'Pembelian', 'Penjualan', 'Retur', 'Adjustment')
     * @param int|null $referenceId - ID of the reference transaction
     * @return StockMovement
     */
    public function recordStockMovement(
        int $productId,
        int $branchId,
        string $type,
        int $quantity,
        string $reference,
        ?int $referenceId = null
    ): StockMovement {
        // Validate type
        if (!in_array($type, ['IN', 'OUT'])) {
            throw new \InvalidArgumentException("Invalid stock movement type: $type. Must be 'IN' or 'OUT'");
        }

        // Create or get existing stock record
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'branch_id' => $branchId],
            ['stock' => 0]
        );

        // Update stock quantity based on type
        if ($type === 'IN') {
            $stock->increment('stock', $quantity);
        } else {
            $stock->decrement('stock', $quantity);
        }

        // Record the movement
        $movement = StockMovement::create([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => $type,
            'qty' => $quantity,
            'reference' => $reference,
            'reference_id' => $referenceId,
        ]);

        return $movement;
    }

    /**
     * Get stock quantity for a product in a specific branch
     * 
     * @param int $productId
     * @param int $branchId
     * @return int
     */
    public function getStockQuantity(int $productId, int $branchId): int
    {
        $stock = Stock::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->first();

        return $stock ? $stock->stock : 0;
    }

    /**
     * Update stock quantity directly (for adjustment)
     * 
     * @param int $productId
     * @param int $branchId
     * @param int $newQuantity
     * @param string $reason - Reason for adjustment
     * @return Stock
     */
    public function adjustStock(int $productId, int $branchId, int $newQuantity, string $reason = 'Manual Adjustment'): Stock
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId, 'branch_id' => $branchId],
            ['stock' => 0]
        );

        $oldQuantity = $stock->stock;
        $difference = $newQuantity - $oldQuantity;

        // Update stock
        $stock->update(['stock' => $newQuantity]);

        // Record movement for the difference
        if ($difference !== 0) {
            $type = $difference > 0 ? 'IN' : 'OUT';
            $quantity = abs($difference);

            StockMovement::create([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'type' => $type,
                'qty' => $quantity,
                'reference' => 'Adjustment',
                'reference_id' => null,
            ]);
        }

        return $stock;
    }

    /**
     * Get stock movement history for a product
     * 
     * @param int $productId
     * @param int $branchId
     * @param int $limit
     * @return mixed
     */
    public function getStockMovementHistory(int $productId, int $branchId, int $limit = 50)
    {
        return StockMovement::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
