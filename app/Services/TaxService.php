<?php

namespace App\Services;

use App\Repositories\Contracts\TaxRepositoryInterface;

class TaxService
{
    protected TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->taxRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->taxRepository->all();
    }

    public function find(string $id)
    {
        return $this->taxRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->taxRepository->create($data);
    }

    public function update(array $data, string $id)
    {
        return $this->taxRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->taxRepository->delete($id);
    }

    public function search(string $name)
    {
        return $this->taxRepository->search($name);
    }
}