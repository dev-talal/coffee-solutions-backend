<?php

namespace App\Http\Controllers\Api\Region;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\RegionResource;
use App\Http\Resources\CompactRegionResource;
use App\Http\Resources\CompactCityResource;
use App\Http\Requests\RegionRequest;
use App\Services\RegionService;

class RegionController extends Controller
{
    use ApiResponseTrait;
    protected $regions;
    public function __construct(RegionService $regions)
    {
        $this->regions = $regions;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search');
        if(empty($searchFor)) {
            $regions = $this->regions;
            $regions = $type == 'paginated' ? 
            $regions->paginate(10) :
            $regions->all();
        } else {
            $regions = $this->regions->search($searchFor);
        }
        
        return $this->successCollection($regions, RegionResource::class, 'Regions retrieved successfully', 200);   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionRequest $request)
    {
        $region = $request->validated();
        $region['added_by'] = auth()->user()->id;
        $region = $this->regions->create($region);

        return $this->successResource($region, RegionResource::class, 'Region created successfully', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $region = $this->regions->find($id);
        if (!$region) {
            return $this->error('Region not found', 404);
        }
        return $this->successResource($region, RegionResource::class, 'Region retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegionRequest $request, string $id)
    {
        $region = $this->regions->find($id);
        if (!$region) {
            return $this->error('Region not found', 404);
        }
        $region = $request->validated();
        $region = $this->regions->update($region, $id);
        return $this->successResource($region, RegionResource::class, 'Region updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $region = $this->regions->find($id);
        if (!$region) {
            return $this->error('Region not found', 404);
        }
        $this->regions->delete($id);
        return $this->success(null, 'Region deleted successfully', 200);
    }

    /**
     * Get cities by region
     */
    public function getCities(string $regionId)
    {
        $region = $this->regions->find($regionId);
        if (!$region) {
            return $this->error('Region not found', 404);
        }
        $cities = $this->regions->getCitiesByRegion($regionId);

        return $this->successData([
            'cities' => CompactCityResource::collection($cities)->response()->getData(true),
            'region' => new CompactRegionResource($region),
        ], 'Data retrieved successfully');        
    }
}
