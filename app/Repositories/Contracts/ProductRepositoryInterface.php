<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search(string $name, int $isPromotion = 0,  $priceFrom = '',  $priceTo = '',  $sortBy = '');

    public function recentProducts();

    public function getCategoryProducts(string $id);

    public function paginatedPromotions();

    public function findPromotion(string $id);

    public function allProducts();

    public function findProduct(string $id);

    public function paginateProducts(int $perPage = 10);

    public function suggestedProducts(string $productId, string $categoryId);

    public function homePopularProducts($isPaginated = false);
}
