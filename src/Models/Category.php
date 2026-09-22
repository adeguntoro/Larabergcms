<?php

namespace LarabergCms\LarabergCms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function larabergs(): HasMany
    {
        return $this->hasMany(Laraberg::class);
    }
}