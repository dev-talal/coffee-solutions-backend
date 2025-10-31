<?php

namespace App\Repositories\Contracts;

interface CustomerCategoryRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search($searchFor);
}
