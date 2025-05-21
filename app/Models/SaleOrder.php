<?php

namespace App\Models;

use App\Enums\SaleOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
        $saleOrderId = $this->id;

        DB::transaction(function () use ($saleOrderId) {
            $saleOrder = SaleOrder::findOrFail($saleOrderId);

            // Calcular el total de la venta
            $totalAmount = $this->total_price;

            $saleOrder->status = SaleOrderStatus::CONFIRMED;
            $saleOrder->total_price = $totalAmount;
            $saleOrder->save();

            // Descontar stock de cada producto
            foreach ($saleOrder->saleOrderDetails as $detail) {
                if ($detail->product->stock < $detail->quantity) {
                    throw new \Exception("No hay suficiente stock para el producto {$detail->product->name}");
                }
                $stock = $detail->product->stock - $detail->quantity;
                $detail->product->update(['stock' => $stock]);
            }
        });
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
