<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use App\Services\CommonService;

class ProductControllerX extends Controller
{
    use ApiResponseTrait;
    protected $products;

    public function __construct(ProductService $products, CommonService $commonService)
    {
        $this->products = $products;
        $this->commonService = $commonService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search', '');
        $priceFrom = $request->query('min', '');
        $priceTo = $request->query('max', '');
        $sortBy = $request->query('sort', '');
        $isPromotion = $request->query('is_promotion', 0);
        $categoryId = $request->query('category_id', null);
        if(empty($searchFor) && empty($priceFrom) && empty($priceTo) && empty($sortBy)){
            if($categoryId != null){
                $products = $this->products->getCategoryProducts($categoryId);
            }else{
                $products = $this->products;
                $products = $type == 'paginated' ? 
                $products->paginateProducts(10) :
                $products->allProducts();
            }
        } else {
            $products = $this->products->search($searchFor, $isPromotion ,$priceFrom, $priceTo, $sortBy);
        }

        return $this->successCollection($products, ProductResource::class, 'Products retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = $request->validated();
        $product['added_by'] = auth()->user()->id;
        $product = $this->products->create($product);

        return $this->successResource($product, ProductResource::class, 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->products->findProduct($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        return $this->successResource($product, ProductResource::class, 'Product retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = $this->products->findProduct($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        $product = $request->validated();
        $product = $this->products->update($product, $id);
        return $this->successResource($product, ProductResource::class, 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->products->findProduct($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }
        $this->products->delete($id);
        $product->images()->delete();
        // $product->productPrices()->delete();
        return $this->success(null,'Product deleted successfully'); 
    }

    /**
     * Upload image for product
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);

        $path = $this->commonService->uploadFile($request->image, 'products');
        return $this->success(['image' => $path], 'Image uploaded successfully');
    }

    /**
     * Delete image for product
     */

    public function deleteImage(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);

        $this->commonService->deleteFile($request->image);
        return $this->success(null, 'Image deleted successfully');
    }
    
}
