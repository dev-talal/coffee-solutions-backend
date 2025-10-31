<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ProductUnitResource;
use App\Models\ProductUnit;

class ProductUnitController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated');
        $productUnits = ProductUnit::select('*');
        if($type != 'paginated') {
            $productUnits = $productUnits->all();
        }else{
            $productUnits = $productUnits->paginate(10);
        }
        return $this->successCollection($productUnits, ProductUnitResource::class, 'Product units retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $unitName = $request->validate([
            'name' => 'required|string|max:255',
            'ar_name' => 'required|string|max:255',
        ]);

        $productUnit = ProductUnit::create($unitName);
        return $this->successResource($productUnit, ProductUnitResource::class, 'Product unit created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productUnit = ProductUnit::find($id);
        if (!$productUnit) {
            return $this->error('Product unit not found', 404);
        }
        return $this->successResource($productUnit, ProductUnitResource::class, 'Product unit retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unitName = $request->validate([
            'name' => 'required|string|max:255',
            'ar_name' => 'required|string|max:255',
        ]);

        $productUnit = ProductUnit::find($id);
        if (!$productUnit) {
            return $this->error('Product unit not found', 404);
        }
        $productUnit->name = $unitName['name'];
        $productUnit->ar_name = $unitName['ar_name'];
        $productUnit->save();
        return $this->successResource($productUnit, ProductUnitResource::class, 'Product unit updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productUnit = ProductUnit::find($id);
        if (!$productUnit) {
            return $this->error('Product unit not found', 404);
        }
        $productUnit->delete();
        return $this->success(null, 'Product unit deleted successfully');
    }
}
