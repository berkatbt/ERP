<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivablePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receivable_id',
        'amount',
        'payment_date',
        'user_id',
    ];
}
