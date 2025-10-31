<?php

namespace App\Services;
use Illuminate\Support\Arr;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    protected ProductRepositoryInterface $products;

    public function __construct(ProductRepositoryInterface $products)
    {
        $this->products = $products;
    }

    public function paginateProducts(int $perPage = 10)
    {
        return $this->products->paginateProducts($perPage);
    }

    public function allProducts()
    {
        return $this->products->allProducts();
    }

    public function findProduct(string $id)
    {
        return $this->products->find($id);
    }

    public function create(array $data)
    {
        $product = $this->transformData($data);
        $createdProduct = $this->products->create($product);
        if($createdProduct){
           if (isset($data['images']) && is_array($data['images'])) {
               $this->saveImages($data, $createdProduct);
            }

            // if(isset($data['customer_category_ids']) && isset($data['cutomer_category_id_price'])){
            //     $this->saveProductPrices($data, $createdProduct);
            // }
        }
        return $createdProduct;
    }

    public function update(array $data, string $id)
    {
        $product = $this->transformData($data);
        $updatedProduct = $this->products->update($id, $product);
        if (isset($data['images']) && is_array($data['images'])) {
            $this->saveImages($data, $updatedProduct);
        }
        // if(isset($data['customer_category_ids']) && isset($data['cutomer_category_id_price'])){
        //     $this->saveProductPrices($data, $updatedProduct);
        // }
        return $updatedProduct;
    }

    public function delete(string $id): bool
    {
        return $this->products->delete($id);
    }

    public function search(string $name, int $isPromotion = 0,  $priceFrom = '',  $priceTo = '',  $sortBy = '')
    {
        return $this->products->search($name, $isPromotion, $priceFrom, $priceTo, $sortBy);
    }

    public function saveImages(array $data, $createdProduct)
    {
        $createdProduct->images()->delete();
        $images = array_map(fn($img) => ['image' => $img], $data['images']);
        $createdProduct->images()->createMany($images);
    }

    public function recentProducts()
    {
        return $this->products->recentProducts();
    }

    public function getCategoryProducts(string $id)
    {
        return $this->products->getCategoryProducts($id);
    }

    public function suggestedProducts(string $productId, string $categoryId)
    {
        return $this->products->suggestedProducts($productId, $categoryId);
    }

    public function homePopularProducts($isPaginated = false)
    {
        return $this->products->homePopularProducts($isPaginated);
    }

    // public function saveProductPrices(array $data, $createdProduct)
    // {
    //     $createdProduct->productPrices()->delete();
    //     $createdProduct->productPrices()->createMany(
    //         collect($data['customer_category_ids'])->map(function ($id, $index) use ($data) {
    //             return [
    //                 'customer_category_id' => $id,
    //                 'price' => $data['cutomer_category_id_price'][$index],
    //             ];
    //         })->toArray()
    //     );
    // }

    public function transformData(array $data)
    {
        $data = Arr::only($data, [
            'name', 'code', 'description', 'quantity',
            'product_category_id', 'price', 'status',
            'is_uom_small', 'pieces_per_box', 'added_by',
            'ar_name', 'ar_description','product_unit_id', 'uom_product_unit_id',
        ]);
        return $data;
    }
}