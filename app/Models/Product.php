<?php

namespace App\Models;

use App\Enums\Unit;
use App\Enums\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'user_id',
    ];

    protected $casts = [
        'unit' => Unit::class,
        'category' => ProductCategory::class,
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
