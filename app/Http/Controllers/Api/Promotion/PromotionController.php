<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\PromotionRequest;
use App\Services\PromotionService;
use App\Http\Resources\ProductResource;

class PromotionController extends Controller
{
    use ApiResponseTrait;
    protected $promotionService;
    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchFor = $request->query('search');
        if(empty($searchFor)) {
            $promotions = $this->promotionService->paginatedPromotions();
        }else{
            $promotions = $this->promotionService->search($searchFor);
        }
        return $this->successCollection($promotions, ProductResource::class, 'Promotions retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromotionRequest $request)
    {
        $promotion = $request->validated();
        $promotion['added_by'] = auth()->user()->id;
        $promotion['is_promotion'] = 1;
        $promotion = $this->promotionService->create($promotion);

        return $this->successResource($promotion, ProductResource::class, 'Promotion created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = $this->promotionService->findPromotion($id);
        if (!$promotion) {
            return $this->error('Promotion not found', 404);
        }
        return $this->successResource($promotion, ProductResource::class, 'Promotion retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromotionRequest $request, string $id)
    {
        $promotion = $this->promotionService->findPromotion($id);
        if (!$promotion) {
            return $this->error('Promotion not found', 404);
        }
        $promotion = $request->validated();
        $promotion = $this->promotionService->update($promotion, $id);
        return $this->successResource($promotion, ProductResource::class, 'Promotion updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = $this->promotionService->findPromotion($id);
        if (!$promotion) {
            return $this->error('Promotion not found', 404);
        }
        $promotion->images()->delete();
        $promotion->promotionProducts()->delete();
        $this->promotionService->delete($id);
        return $this->success(null, 'Promotion deleted successfully');
    }
}
