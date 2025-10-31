<?php

namespace App\Http\Controllers\Api\Warehourse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WareHouseService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\WareHouseRequest;
use App\Http\Resources\CompactWareHouseResource;
use App\Http\Resources\WareHouseResource;

class WarehouseController extends Controller
{
    use ApiResponseTrait;
    protected $warehouseRepository;
    public function __construct(WareHouseService $warehouse)
    {
        $this->warehouse = $warehouse;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search');
        if(empty($searchFor)) {
            $warehouses = $this->warehouse;
            $warehouses = $type == 'paginated' ? 
            $warehouses->paginate(10) :
            $warehouses->all();
        }else{
            $warehouses = $this->warehouse->search($searchFor);
        }
        return $this->successCollection($warehouses, WareHouseResource::class, 'Warehouses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WareHouseRequest $request)
    {
        $warehouse = $request->validated();
        $warehouse['added_by'] = auth()->user()->id;
        $warehouse = $this->warehouse->create($warehouse);

        return $this->successResource($warehouse, WareHouseResource::class, 'Warehouse created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $warehouse = $this->warehouse->find($id);
        if (!$warehouse) {
            return $this->error('Warehouse not found', 404);
        }
        return $this->successResource($warehouse, WareHouseResource::class, 'Warehouse retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WareHouseRequest $request, string $id)
    {
        $warehouse = $this->warehouse->find($id);
        if (!$warehouse) {
            return $this->error('Warehouse not found', 404);
        }
        $warehouse = $request->validated();
        $warehouse = $this->warehouse->update($warehouse, $id);
        return $this->successResource($warehouse, WareHouseResource::class, 'Warehouse updated successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = $this->warehouse->find($id);
        if (!$warehouse) {
            return $this->error('Warehouse not found', 404);
        }
        $this->warehouse->delete($id);
        return $this->success(null, 'Warehouse deleted successfully');
    }

    /**
     * Get warehouses by region
     */
    public function getWarehousesByRegion(string $regionId)
    {
        $warehouses = $this->warehouse->getWarehousesByRegion($regionId);
        return $this->successCollection($warehouses, CompactWareHouseResource::class, 'Warehouses retrieved successfully');
    }
}
