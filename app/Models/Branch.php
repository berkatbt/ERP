<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'photo',
        'city',
        'latitude',
        'longitude',
        'radius',
        'office_type',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
