<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class CustomerCategoryRepository extends BaseRepository implements CustomerCategoryRepositoryInterface
{
    public function __construct(CustomerCategory $model)
    {
        $this->model = $model;
    }

    public function search($searchFor)
    {
        return $this->model
        ->where(function ($q) use ($searchFor) {
            $q->where('name', 'like', '%' . $searchFor . '%');
            $q->orWhere('discount', 'like', '%' . $searchFor . '%');
        })
        ->paginate(10);
    }

    // Custom methods for Region if needed
}
