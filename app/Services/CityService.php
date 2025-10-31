<?php

namespace App\Services;

use App\Repositories\Contracts\CityRepositoryInterface;

class CityService
{
    protected CityRepositoryInterface $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->cityRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->cityRepository->all();
    }

    public function find(string $id)
    {
        return $this->cityRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->cityRepository->create($data);
    }

    public function update(array $data, string $id)
    {
        return $this->cityRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->cityRepository->delete($id);
    }

    public function getCitiesByRegion(string $regionId, string $type = 'paginated')
    {
        return $this->cityRepository->getCitiesByRegion($regionId, $type);
    }

    public function search(string $name, string $regionId)
    {
        return $this->cityRepository->search($name, $regionId);
    }
}