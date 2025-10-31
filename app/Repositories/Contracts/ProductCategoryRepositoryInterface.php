<?php

namespace App\Repositories\Contracts;

interface ProductCategoryRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search(string $name);

    public function getParentCategories(string $type = 'paginated');

    public function getChildren(string $id);

    public function getAllChildCategories();

    public function getProductCount(string $id);
}
