<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\BranchScope;

class AuditLog extends Model
{
    use HasFactory, BranchScope;

    protected $fillable = [
        'user_id',
        'branch_id',
        'auditable_type',
        'auditable_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
