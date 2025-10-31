<?php

namespace App\Http\Controllers\Api\AppCustomer\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use App\Services\PromotionService;
use App\Http\Resources\CompactProductCategoryResource;
use App\Http\Resources\CompactProductResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;

class HomeController extends Controller
{
    use ApiResponseTrait;
    protected $productCategoryService, $productService, $promotionService;
    public function __construct(
        ProductCategoryService $productCategoryService,
        ProductService $productService,
        PromotionService $promotionService
        )
    {
        $this->productCategoryService = $productCategoryService;
        $this->productService = $productService;
        $this->promotionService = $promotionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated');
        $userId  = auth()->check() ? auth()->id() : 'guest';
        $version = cache()->get('home_data_version', 1);
        $cacheKey = "home_data_v{$version}_{$userId}";

        // $data = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($type) {
            $productCategories = $this->productCategoryService->getParentCategories($type);
            $recentProducts    = $this->productService->recentProducts();
            $promotions        = $this->promotionService->paginatedPromotions();
            $popularProducts   = $this->productService->homePopularProducts(false);

            $data = [
                'product_categories' => CompactProductCategoryResource::collection($productCategories)->resolve(),
                'recent_products'    => CompactProductResource::collection($recentProducts)->resolve(),
                'promotions'         => CompactProductResource::collection($promotions)->resolve(),
                'popular_products'   => CompactProductResource::collection($popularProducts)->resolve(),
            ];
        // });

        return $this->successData($data, 'Data retrieved successfully');

    }

    /**
     * Display the products for a specific category.
     */
    public function getCategoryProducts(string $id)
    {
        $products = $this->productService->getCategoryProducts($id);
        $childCategories = $this->productCategoryService->getChildren($id);

        return $this->successData([
            'products' => CompactProductResource::collection($products)->resolve(),
            'child_categories' => CompactProductCategoryResource::collection($childCategories)->resolve(),
        ], 'Data retrieved successfully');
    }

    /**
     * Display the products details by id
     */

    public function productDetails(string $id)
    {
        $product = $this->productService->findProduct($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }

        if($product->is_promotion){
            $resourceClass = ProductResource::class;
        }else{
            $resourceClass = CompactProductResource::class;
        }
        return $this->successResource($product, $resourceClass, 'Product retrieved successfully');
    }

    public function suggestedProducts(string $id)
    {
        $product = $this->productService->findProduct($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }

        $suggestedProducts = $this->productService->suggestedProducts($product->id, $product->product_category_id);
        if($product->is_promotion){
            $resourceClass = ProductResource::class;
        }else{
            $resourceClass = CompactProductResource::class;
        }

        return $this->successCollection($suggestedProducts, $resourceClass, 'Suggested products retrieved successfully');
    }

    public function globalSearch(Request $request)
    {
        $search = $request->query('search');
        $products = Product::where('name', 'like', "%{$search}%")->take(10)->get();
        if ($products->isNotEmpty()) {
            return $this->successCollection($products, CompactProductResource::class, 'Products retrieved successfully');
        }
        $categoryIds = ProductCategory::where('name', 'like', "%{$search}%")->pluck('id');
        $products = Product::whereIn('product_category_id', $categoryIds)->take(10)->get();
        return $this->successCollection($products, CompactProductResource::class, 'Products retrieved successfully');

    }
}
