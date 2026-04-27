<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'type',
        'qty',
        'reference',
        'reference_id',
    ];

    /**
     * Get the product associated with this movement
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch associated with this movement
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get readable type name
     */
    public function getTypeNameAttribute()
    {
        return $this->type === 'IN' ? 'Masuk' : 'Keluar';
    }
}
