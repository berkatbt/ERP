<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = [
        'sale_id',
        'user_id',
        'branch_id',
        'reason',
    ];

    public function details() {
        return  $this->hasMany(SaleReturnDetail::class);
    }

    public function sale() {
        return $this->belongsTo(Sale::class);
    }
}
