<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MatrixMember extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'position',
        'depth',
        'path',
    ];

    protected $casts = [
        'position' => 'integer',
        'depth' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'user_id');
    }

    public function getDescendantCount(): int
    {
        if (!$this->path) {
            return 0;
        }
        return self::where('path', 'like', $this->path . '/%')
            ->orWhere('path', 'like', '%/' . $this->user_id . '/%')
            ->count();
    }
}
