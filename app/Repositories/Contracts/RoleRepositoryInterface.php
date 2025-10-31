<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search($searchFor);
}
