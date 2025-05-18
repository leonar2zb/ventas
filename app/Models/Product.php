<?php

namespace App\Models;

use App\Enums\Unit;
use App\Enums\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'category',
        'unit',
        'stock',
    ];

    protected $casts = [
        'unit' => Unit::class,
        'category' => ProductCategory::class,
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->user_id = Auth::id();
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
