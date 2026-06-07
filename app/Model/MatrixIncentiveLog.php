<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MatrixIncentiveLog extends Model
{
    protected $fillable = [
        'user_id',
        'matrix_level_id',
        'level',
        'position_name',
        'amount',
        'total_team_members',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'amount' => 'float',
        'total_team_members' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function matrixLevel()
    {
        return $this->belongsTo(MatrixLevel::class, 'matrix_level_id');
    }
}
