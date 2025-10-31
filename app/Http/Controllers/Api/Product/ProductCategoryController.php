<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ProductCategory;
use App\Services\ProductCategoryService;
use App\Http\Resources\CompactProductCategoryResource;
use App\Http\Requests\ProductCategoryRequest;

class ProductCategoryController extends Controller
{
    use ApiResponseTrait;
    protected $productCategoryService;
    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search');
        if(empty($searchFor)) {
            $productCategories = $this->productCategoryService;
            $productCategories = $type == 'paginated' ? 
            $productCategories->paginate(10) :
            $productCategories->all();
        }else{
            $productCategories = $this->productCategoryService->search($searchFor);
        }
        return $this->successCollection($productCategories, ProductCategory::class, 'Product Categories retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request)
    {
        $productCategory = $request->validated();
        $productCount = $this->productCategoryService->getProductCount($productCategory['parent_id'] ?? 0);
        if($productCount > 0){
            return $this->error('You have to change the parent, because this parent have some products', 400);
        }
        $productCategory['added_by'] = auth()->user()->id;
        $productCategory = $this->productCategoryService->create($productCategory);

        return $this->successResource($productCategory, ProductCategory::class, 'Product Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productCategory = $this->productCategoryService->find($id);
        if (!$productCategory) {
            return $this->error('Product Category not found', 404);
        }
        return $this->successResource($productCategory, ProductCategory::class, 'Product Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, string $id)
    {
        $productCategory = $this->productCategoryService->find($id);
        if (!$productCategory) {
            return $this->error('Product Category not found', 404);
        }
        $productCategory = $request->validated();
        $productCategory = $this->productCategoryService->update($productCategory, $id);
        return $this->successResource($productCategory, ProductCategory::class, 'Product Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productCategory = $this->productCategoryService->find($id);
        if (!$productCategory) {
            return $this->error('Product Category not found', 404);
        }
        $this->productCategoryService->delete($id);
        return $this->success(null, 'Product Category deleted successfully');
    }

     public function getChildCategories(Request $request)
    {
        $childCategories = $this->productCategoryService->getAllChildCategories();
        return $this->successCollection($childCategories, CompactProductCategoryResource::class, 'Product categories retrieved successfully');
    }
}
