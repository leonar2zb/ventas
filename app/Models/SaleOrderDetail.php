<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class SaleOrderDetail extends Model
{
    protected $fillable = [
        'sale_order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function saleOrder(): BelongsTo
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }

    protected static function booted()
    {
        parent::boot();

        static::creating(function ($saleOrderDetail) {
            $product = $saleOrderDetail->product;
            // as a last resort, check if the product exists before creating the sale order detail
            if ($product->stock < $saleOrderDetail->quantity) {
                // throw new \Exception("Not enough stock for {$product->name}. Availability: {$product->stock}"); //abrubt the creation                
                return false; // abort creation and silence the error
            }

            // only create the sale order detail if the product does not already exist in the sale order
            $exists = SaleOrderDetail::where('sale_order_id', $saleOrderDetail->sale_order_id)
                ->where('product_id', $saleOrderDetail->product_id)
                ->exists();

            if ($exists)
                return false; // abort creation if the product already exists in the sale order detail
            //throw ValidationException::withMessages(['product_id' => 'Este producto ya está en la orden. Modifica la cantidad en lugar de agregarlo nuevamente.']);

            $saleOrderDetail->unit_price = $product->price; // set the unit price to the product's price

        });

        static::updating(function ($saleOrderDetail) {
            $product = $saleOrderDetail->product;
            // as a last resort, check if the product exists before updating the sale order detail
            if ($product->stock < $saleOrderDetail->quantity) {
                return false; // stop the update and silence the error
            }
        });
    }
}
