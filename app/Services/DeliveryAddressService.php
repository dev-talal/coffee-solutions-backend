<?php

namespace App\Services;

use App\Repositories\Contracts\DeliveryAddressRepositoryInterface;

class DeliveryAddressService
{
    protected DeliveryAddressRepositoryInterface $deliveryAddressRepository;

    public function __construct(DeliveryAddressRepositoryInterface $deliveryAddressRepository)
    {
        $this->deliveryAddressRepository = $deliveryAddressRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->deliveryAddressRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->deliveryAddressRepository->all();
    }

    public function find(string $id)
    {
        return $this->deliveryAddressRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->deliveryAddressRepository->create($data);
    }

    public function update(array $data, string $id)
    {
        return $this->deliveryAddressRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->deliveryAddressRepository->delete($id);
    }
}