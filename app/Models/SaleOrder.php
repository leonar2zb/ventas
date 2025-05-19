<?php

namespace App\Models;

use App\Enums\SaleOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['description'];

    protected $casts = [
        'status' => SaleOrderStatus::class,
    ];
}
