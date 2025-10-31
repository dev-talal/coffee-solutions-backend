<?php

namespace App\Repositories\Contracts;

interface CityRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get cities by region with optional pagination.
     *
     * @param string $regionId
     * @param string $type
     * @return mixed
     */
    public function getCitiesByRegion(string $regionId, string $type = 'paginated');
    /**
     * Search cities by name.
     *
     * @param string $name
     * @return mixed
     */
    public function search(string $name, string $regionId);
}
