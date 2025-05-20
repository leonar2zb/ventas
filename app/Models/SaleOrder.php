<?php

namespace App\Models;

use App\Enums\SaleOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SaleOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['description'];

    protected $casts = [
        'status' => SaleOrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleOrderDetails(): HasMany
    {
        return $this->hasMany(SaleOrderDetail::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->saleOrderDetails->sum(function ($detail) {
            return $detail->quantity * $detail->product->price;
        });
    }

    public function confirm()
    {
        $this->status = SaleOrderStatus::CONFIRMED;
        $this->save();
    }

    public function cancel()
    {
        $this->status = SaleOrderStatus::CANCELLED;
        $this->save();
    }

    protected static function booted()
    {
        static::creating(function ($saleOrder) { // make sure to set the user_id when creating a new sale order
            if (Auth::guest()) {
                throw new \Exception('User not authenticated');
            }
            $saleOrder->user_id = Auth::id();
            $saleOrder->status = SaleOrderStatus::PENDING; // default status
        });
    }
}
