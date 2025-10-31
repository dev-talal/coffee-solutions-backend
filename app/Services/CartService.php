<?php

namespace App\Services;

use App\Repositories\Contracts\CartRepositoryInterface;

class CartService
{
    protected CartRepositoryInterface $CartRepositoryInterface;

    public function __construct(CartRepositoryInterface $CartRepositoryInterface)
    {
        $this->CartRepositoryInterface = $CartRepositoryInterface;
    }

    public function paginate($userId, $isGuest ,int $perPage = 20)
    {
        return $this->CartRepositoryInterface->getCustomerCart($userId, $isGuest, $perPage);
    }

    public function create(array $data)
    {
        $cartItem = $this->CartRepositoryInterface->checkProductInCart($data);

        if ($cartItem) {
            $data['quantity'] = $data['quantity'] ?? $cartItem->quantity + 1;
            return $this->CartRepositoryInterface->update($cartItem->id, $data);
        }else{
            $data['quantity'] = $data['quantity'] ?? 1;
        }

        return $this->CartRepositoryInterface->create($data);
    }

    public function checkProductInCart(array $data)
    {
        return $this->CartRepositoryInterface->checkProductInCart($data);
    }

    public function find(string $id)
    {
        return $this->CartRepositoryInterface->find($id);
    }

    public function delete(string $id)
    {
        return $this->CartRepositoryInterface->delete($id);
    }

    public function syncCart($guestId)
    {
        return $this->CartRepositoryInterface->syncCart($guestId);
    }

    public function count($userId, $isGuest)
    {
        return $this->CartRepositoryInterface->count($userId, $isGuest);
    }
}