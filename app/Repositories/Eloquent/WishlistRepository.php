<?php

namespace App\Repositories\Eloquent;

use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class WishlistRepository extends BaseRepository implements WishlistRepositoryInterface
{
    public function __construct(Wishlist $model)
    {
        $this->model = $model;
    }

    public function deleteWishlist(string $productId, string $customerId)
    {
        return $this->model->where('product_id', $productId)
            ->where('user_id', $customerId)
            ->delete();
    }

    public function checkProductInWishlist(string $productId, string $customerId)
    {
        return $this->model->where('product_id', $productId)
            ->where('user_id', $customerId)
            ->first();
    }

    public function fetchCustomerWishlists(string $customerId, int $perPage = 10)
    {
        return $this->model->where('user_id', $customerId)
            ->paginate($perPage);
    }
}
