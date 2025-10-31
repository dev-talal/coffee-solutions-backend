<?php

namespace App\Repositories\Contracts;

interface TaxRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search(string $name);
}
