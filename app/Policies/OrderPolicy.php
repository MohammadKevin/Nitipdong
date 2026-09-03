<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks. Super admin and admin have global access.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Buyer ownership
        if ($order->user_id === $user->id) {
            return true;
        }

        // Seller ownership via store
        if ($order->store && $order->store->user_id === $user->id) {
            return true;
        }

        // Assigned courier
        if ($order->courier_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can pay for the order.
     */
    public function pay(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'pending';
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Orders already completed or cancelled cannot be cancelled
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return false;
        }

        // Buyer can cancel if order hasn't been shipped yet
        if ($order->user_id === $user->id) {
            return in_array($order->status, ['pending', 'paid', 'processing']);
        }

        // Seller can cancel if order belongs to their store and not shipped
        if ($order->store && $order->store->user_id === $user->id) {
            return in_array($order->status, ['pending', 'paid', 'processing']);
        }

        return false;
    }

    /**
     * Determine whether the user can confirm delivery / completion.
     */
    public function confirmReceived(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && in_array($order->status, ['shipped', 'delivered']);
    }

    /**
     * Determine whether the user can review items in this order.
     */
    public function review(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'completed';
    }
}
