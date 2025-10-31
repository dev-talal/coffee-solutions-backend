<?php

namespace App\Repositories\Contracts;

interface WishlistRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteWishlist(string $productId, string $customerId);

    public function checkProductInWishlist(string $productId, string $customerId);

    public function fetchCustomerWishlists(string $customerId, int $perPage = 10);
}
