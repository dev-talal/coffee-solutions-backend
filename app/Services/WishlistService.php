<?php

namespace App\Services;

use App\Repositories\Contracts\WishlistRepositoryInterface;

class WishlistService
{
    protected WishlistRepositoryInterface $WishlistRepositoryInterface;

    public function __construct(WishlistRepositoryInterface $WishlistRepositoryInterface)
    {
        $this->WishlistRepositoryInterface = $WishlistRepositoryInterface;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->WishlistRepositoryInterface->paginate($perPage);
    }

    public function fetchCustomerWishlists(string $customerId, int $perPage = 10)
    {
        return $this->WishlistRepositoryInterface->fetchCustomerWishlists($customerId, $perPage);
    }

    public function create(array $data)
    {
        return $this->WishlistRepositoryInterface->create($data);
    }

    public function deleteWishlist(string $productId, string $customerId)
    {
        return $this->WishlistRepositoryInterface->deleteWishlist($productId, $customerId);
    }

    public function checkProductInWishlist(string $productId, string $customerId)
    {
        return $this->WishlistRepositoryInterface->checkProductInWishlist($productId, $customerId);
    }

    
}