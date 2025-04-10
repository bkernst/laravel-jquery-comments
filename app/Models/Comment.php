<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'email',
        'comment',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
