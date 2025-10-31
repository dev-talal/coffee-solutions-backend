<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function allProducts()
    {
        return $this->model
            ->where('is_promotion', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function findProduct(string $id)
    {
        return $this->model->where('is_promotion', 0)->find($id);
    }

    public function paginateProducts(int $perPage = 10)
    {
        return $this->model
            ->where('is_promotion', 0)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function search(string $name, int $isPromotion = 0,  $priceFrom = '',  $priceTo = '',  $sortBy = '')
    {
        
        $query = $this->model->newQuery();
        if ($priceFrom != '' && $priceTo != '') {
            $query->whereBetween('price', [$priceFrom, $priceTo]);
        }
        if (!$priceFrom && !$priceTo && !$sortBy) {
            $query->where('is_promotion', $isPromotion);
        }

        // Sorting
        if ($sortBy != '') {
            Log::info("Sorting by: $sortBy");
            switch ($sortBy) {
                case 'recent-products':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'low-to-high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'high-to-low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular-products':
                    $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->select(
                        'products.*',
                        DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                    )
                    ->where(function ($q) {
                        $q->whereNull('order_items.created_at')
                            ->orWhere('order_items.created_at', '>=', Carbon::now()->subDays(30));
                    })
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold');
                    break;
            }
        }

        // Search filter
        if (!empty($name)) {
            $query->where('name', 'like', "%{$name}%");
        }

        // Pagination
        return $query->paginate(10);

    }

    public function recentProducts()
    {
        return $this->model
            ->where('status', 1)
            ->where('is_promotion', 0)
            ->orderBy('created_at', 'desc')
            ->take(15)->get();
    }

    public function getCategoryProducts(string $id)
    {
        return $this->model
            ->where('product_category_id', $id)
            ->where('status', 1)->where('is_promotion', 0)
            ->orderBy('name', 'asc')
            ->paginate(15);
    }

    public function paginatedPromotions()
    {
        return $this->model
            ->where('is_promotion', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function findPromotion(string $id)
    {
        return $this->model->where('is_promotion', 1)->find($id);
    }


    public function suggestedProducts(string $productId, string $categoryId)
    {
        return $this->model->where('product_category_id', $categoryId)
            ->where('status', 1)
            ->where('id', '!=', $productId)
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
    }

    public function homePopularProducts($isPaginated = false)
    {
        $popularProducts = Product::leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->select(
            'products.*',
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
        )
        ->where(function($query) {
            $query->whereNull('order_items.created_at')
                ->orWhere('order_items.created_at', '>=', Carbon::now()->subDays(30));
        })
        ->groupBy('products.id', 'products.name', 'products.price', 'products.quantity', 'products.created_at', 'products.ar_name', 'products.ar_description', 'products.code', 'products.description', 'products.status', 'products.product_category_id')
        ->orderByDesc('total_sold');
        if($isPaginated){
            $popularProducts = $popularProducts->paginate(10);
        }else{
            $popularProducts = $popularProducts->take(10);
            $popularProducts = $popularProducts->get();
        }

        return $popularProducts;
    }

}
