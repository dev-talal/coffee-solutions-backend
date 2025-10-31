<?php

namespace App\Repositories\Eloquent;

use App\Models\Tax;
use App\Repositories\Contracts\TaxRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class TaxRepository extends BaseRepository implements TaxRepositoryInterface
{
    public function __construct(Tax $model)
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

    // Custom methods for Region if needed
}
