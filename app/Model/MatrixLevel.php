<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MatrixLevel extends Model
{
    protected $fillable = [
        'level',
        'position_name',
        'required_members',
        'incentive_amount',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'required_members' => 'integer',
        'incentive_amount' => 'float',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
