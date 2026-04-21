<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BranchScope
{
    protected static function bootBranchScope(): void
    {
        static::addGlobalScope('branch', function (Builder $query) {
            if (auth()->check() && strtolower(auth()->user()->role) !== 'owner') {
                $query->where('branch_id', auth()->user()->branch_id);
            }
        });
    }
}
