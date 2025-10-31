<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);

    public function getUsersByRole(string $role, bool $exclude = false);

    public function getStaffByWarehouse(string $warehouseId);
}
