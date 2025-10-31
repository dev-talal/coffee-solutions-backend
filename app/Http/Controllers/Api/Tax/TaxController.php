<?php

namespace App\Http\Controllers\Api\Tax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Http\Resources\TaxResource;
use App\Traits\ApiResponseTrait;
use App\Services\TaxService;

class TaxController extends Controller
{
    use ApiResponseTrait;
    protected $taxService;
    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('type', 'paginated');
        $searchFor = $request->query('search', '');
        if(empty($searchFor)) {
            $taxes = $this->taxService;
            $taxes = $perPage == 'paginated' ? 
            $taxes->paginate(10) :
            $taxes->all();
        }else{
            $taxes = $this->taxService->search($searchFor);
        }
        return $this->successCollection($taxes, TaxResource::class, 'Taxes retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request)
    {
        $tax = $request->validated();
        $tax['added_by'] = auth()->user()->id;
        $tax = $this->taxService->create($tax);
        return $this->successResource($tax, TaxResource::class, 'Tax created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tax = $this->taxService->find($id);
        if (!$tax) {
            return $this->error('Tax not found', 404);
        }
        return $this->successResource($tax, TaxResource::class, 'Tax retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, string $id)
    {
        $tax = $this->taxService->find($id);
        if (!$tax) {
            return $this->error('Tax not found', 404);
        }
        $tax = $request->validated();
        $tax = $this->taxService->update($tax, $id);
        return $this->successResource($tax, TaxResource::class, 'Tax updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tax = $this->taxService->find($id);
        if (!$tax) {
            return $this->error('Tax not found', 404);
        }
        $this->taxService->delete($id);
        return $this->success(null, 'Tax deleted successfully');
    }

    /**
     * Get taxes for customers (for mobile app)
     */
    public function getTaxesForCustomer(Request $request)
    {
        $taxes = $this->taxService->all();
        return $this->successCollection($taxes, TaxResource::class, 'Taxes retrieved successfully');
    }
}
