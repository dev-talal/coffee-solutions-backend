<?php

namespace App\Http\Controllers\Api\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\CityResource;
use App\Http\Requests\CityRequest;
use App\Services\CityService;

class CityController extends Controller
{
    use ApiResponseTrait;
    protected $cities;
    public function __construct(CityService $cities)
    {
        $this->cities = $cities;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $regionId = $request->query('region_id', null);
        $searchFor = $request->query('search');
        if(empty($searchFor)) {
            $cities = $this->cities->getCitiesByRegion($regionId, $type);
        }else{
            $cities = $this->cities->search($searchFor, $regionId);
        }
        return $this->successCollection($cities, CityResource::class, 'Cities retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        $city = $request->validated();
        $city['added_by'] = auth()->user()->id;
        $city = $this->cities->create($city);

        return $this->successResource($city, CityResource::class, 'City created successfully', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $city = $this->cities->find($id);
        if (!$city) {
            return $this->error('City not found', 404);
        }
        return $this->successResource($city, CityResource::class, 'City retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, string $id)
    {
        $city = $request->validated();
        $city = $this->cities->update($city, $id);

        return $this->successResource($city, CityResource::class, 'City updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = $this->cities->find($id);
        if (!$city) {
            return $this->error('City not found', 404);
        }
        $this->cities->delete($id);

        return $this->success(null, 'City deleted successfully', 200);
    }
}
