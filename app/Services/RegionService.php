<?php

namespace App\Services;

use App\Repositories\Contracts\RegionRepositoryInterface;

class RegionService
{
    protected RegionRepositoryInterface $regionRepository;

    public function __construct(RegionRepositoryInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->regionRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->regionRepository->all();
    }

    public function find(string $id)
    {
        return $this->regionRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->regionRepository->create($data);
    }

    public function update(array $data, string $id)
    {
        return $this->regionRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->regionRepository->delete($id);
    }

    public function search(string $name)
    {
        return $this->regionRepository->search($name);
    }

    public function getCitiesByRegion(string $regionId)
    {
        return $this->regionRepository->getCitiesByRegion($regionId);
    }
}