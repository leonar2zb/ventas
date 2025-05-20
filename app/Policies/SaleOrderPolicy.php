<?php

namespace App\Policies;

use App\Enums\SaleOrderStatus;
use App\Models\SaleOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SaleOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SaleOrder $saleOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models. Only sellers can create sale orders.
     */
    public function create(User $user): bool
    {
        $can =  $user->role?->name === 'Seller';
        return $can;
    }

    /**
     * Determine whether the user can update the model. Once a sale order is confirmed or cancelled, it cannot be updated.
     */
    public function update(User $user, SaleOrder $saleOrder): bool
    {
        return true; /* Provicionalmente, permito la actualización de cualquier orden de venta para 
        poder acceder desde Edit a ver los detalles SaleOrderDetail y en esta última sí tengo en cuenta el estado de la orden de venta.
        $can = $saleOrder->status === SaleOrderStatus::PENDING
            && $user->role?->name === 'Seller';
        return $can;*/
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SaleOrder $saleOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SaleOrder $saleOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SaleOrder $saleOrder): bool
    {
        return false;
    }
}
