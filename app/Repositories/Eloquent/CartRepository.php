<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        $this->model = $model;
    }

    public function checkProductInCart($data)
    {
        $cartItem = $this->model->where('product_id', $data['product_id']);

        if(isset($data['session_id'])) $cartItem->where('session_id', $data['session_id']);
        else $cartItem->where('user_id', $data['user_id']);

        $cartItem = $cartItem->first();
        return $cartItem;
    }

    public function getCustomerCart($userId, $isGuest , int $perPage = 20)
    {
        $cartItems = $this->model;
        if($isGuest) {
            $cartItems = $cartItems->where('session_id', $userId);
        }else{
            $cartItems = $cartItems->where('user_id', $userId);
        }
        return $cartItems->with('product')->paginate($perPage);
    }

    public function syncCart($guestId)
    {
        $userId = auth()->id();

        $guestCarts = $this->model->where('session_id', $guestId)->get();

        if ($guestCarts->isEmpty()) {
            return true;
        }

        $productIds = $guestCarts->pluck('product_id');

        // Get user's existing cart items for these products
        $userCarts = $this->model->where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        foreach ($guestCarts as $guestCart) {
            if (isset($userCarts[$guestCart->product_id])) {
                // Merge quantities
                $userCart = $userCarts[$guestCart->product_id];
                $userCart->quantity += $guestCart->quantity;
                $userCart->save();
                $guestCart->delete();
            } else {
                $guestCart->user_id = $userId;
                $guestCart->session_id = null;
                $guestCart->save();
            }
        }

        return true;
    }



    public function count($userId, $isGuest)
    {
        $cartItems = $this->model;
        if($isGuest) {
            $cartItems = $cartItems->where('session_id', $userId);
        }else{
            $cartItems = $cartItems->where('user_id', $userId);
        }
        return $cartItems->count();
    }
}
