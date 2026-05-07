<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'branch_id',
        'total_debt',
        'paid_amount',
        'remaining_amount',
        'status',
        'due_date',
    ];

    public function payments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
