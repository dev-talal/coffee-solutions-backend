<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function checkProductInCart(array $data);

    public function getCustomerCart($userId, $isGuest, int $perPage = 20);

    public function syncCart($guestId);

    public function count($userId, $isGuest);
}
