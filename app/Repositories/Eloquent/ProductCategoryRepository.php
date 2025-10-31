<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductCategory;
use App\Models\Product;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    public function __construct(ProductCategory $model)
    {
        $this->model = $model;
    }

    public function search(string $name)
    {
        return $this->model
        ->where(function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%');
        })
        ->paginate(10);
    }

    public function getParentCategories(string $type = 'paginated')
    {
        $parentCategories = $this->model
            ->where('parent_id', null)
            ->where('status', 1);
        if ($type == 'paginated') {
            return $parentCategories->paginate(10);
        }
        return $parentCategories->all();
    }

    public function getChildren(string $id)
    {
        return $this->model
            ->where('parent_id', $id)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllChildCategories()
    {
        return $this->model
            ->where('parent_id', '!=', null)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getProductCount(string $id)
    {
        return Product::where('product_category_id', $id)->count();
    }

    // Custom methods for Region if needed
}
