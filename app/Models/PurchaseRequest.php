<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScope;

class PurchaseRequest extends Model
{
    use HasFactory, BranchScope;

    protected $fillable = [
        'user_id',
        'branch_id',
        'status',
        'note',
        'approved_by',
        'approved_at',
        'approval_note',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    public function tracking()
    {
        return $this->hasMany(TrackingPurchaseRequest::class)->orderBy('created_at', 'asc');
    }
    public function latestTracking()
    {
        return $this->hasOne(TrackingPurchaseRequest::class)->latestOfMany();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}