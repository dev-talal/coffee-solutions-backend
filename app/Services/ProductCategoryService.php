<?php

namespace App\Services;

use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Services\CommonService;

class ProductCategoryService
{
    protected ProductCategoryRepositoryInterface $productCategoryRepository;
    protected CommonService $commonService;

    public function __construct(ProductCategoryRepositoryInterface $productCategoryRepository, CommonService $commonService)
    {
        $this->productCategoryRepository = $productCategoryRepository;
        $this->commonService = $commonService;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->productCategoryRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->productCategoryRepository->all();
    }

    public function find(string $id)
    {
        return $this->productCategoryRepository->find($id);
    }

    public function create(array $data)
    {
        $path = $this->commonService->uploadFile($data['icon'], 'product-categories');
        if ($path) {
            $data['icon'] = $path;
        }
        return $this->productCategoryRepository->create($data);
    }

    public function update(array $data, string $id)
    {
        if (isset($data['icon'])) {
            $path = $this->commonService->uploadFile($data['icon'], 'product-categories');
            if ($path) {
                $data['icon'] = $path;
            }
        } else {
            unset($data['icon']);
        }
        return $this->productCategoryRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->productCategoryRepository->delete($id);
    }

    public function search(string $name)
    {
        return $this->productCategoryRepository->search($name);
    }

    public function getParentCategories()
    {
        return $this->productCategoryRepository->getParentCategories();
    }

    public function getChildren(string $id)
    {
        return $this->productCategoryRepository->getChildren($id);
    }

    public function getAllChildCategories()
    {
        return $this->productCategoryRepository->getAllChildCategories();
    }

    public function getProductCount(string $id)
    {
        return $this->productCategoryRepository->getProductCount($id);
    }
}