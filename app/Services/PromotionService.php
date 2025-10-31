<?php

namespace App\Services;
use Illuminate\Support\Arr;
use DB;
use App\Repositories\Contracts\ProductRepositoryInterface;

class PromotionService
{
    protected ProductRepositoryInterface $products;

    public function __construct(ProductRepositoryInterface $products)
    {
        $this->products = $products;
    }

    public function paginatedPromotions()
    {
        return $this->products->paginatedPromotions();
    }

    public function findPromotion(string $id)
    {
        return $this->products->findPromotion($id);
    }

    public function create(array $data)
    {
        $product = $this->transformData($data);
        $createdPromotion = $this->products->create($product);
        if($createdPromotion){
           if (isset($data['images']) && is_array($data['images'])) {
               $this->saveImages($data, $createdPromotion);
            }

            if (isset($data['promotion_products'])) {
                $this->saveProducts($data, $createdPromotion);
            }
        }
        return $createdPromotion;
    }

    public function update(array $data, string $id)
    {
        $product = $this->transformData($data);
        $updatedProduct = $this->products->update($id, $product);
        if (isset($data['images']) && is_array($data['images'])) {
            $this->saveImages($data, $updatedProduct);
        }
        if (isset($data['promotion_products'])) {
            $this->saveProducts($data, $updatedProduct);
        }
        return $updatedProduct;
    }

    public function delete(string $id): bool
    {
        return $this->products->delete($id);
    }

    public function search(string $name)
    {
        return $this->products->search($name, 1);
    }

    public function saveImages(array $data, $createdProduct)
    {
        $createdProduct->images()->delete();
        $images = array_map(fn($img) => ['image' => $img], $data['images']);
        $createdProduct->images()->createMany($images);
    }

    public function saveProducts(array $data, $createdPromotion)
    {
        $createdPromotion->promotionProducts()->delete();

       $products = collect($data['promotion_products'])
            ->map(fn($item) => [
                'product_id'   => $item['product_id'],
                'quantity'     => $item['quantity'] ?? 1,
                'promotion_id' => $createdPromotion->id,
            ])
            ->toArray();

        DB::table('promotion_products')->insert($products);

    }

    public function transformData(array $data)
    {
        $data = Arr::only($data, [
            'name', 'code', 'description','quantity',
            'product_category_id', 'price', 'status',
            'added_by', 'promotion_end_date','is_promotion',
            'ar_name', 'ar_description'
        ]);
        return $data;
    }
}