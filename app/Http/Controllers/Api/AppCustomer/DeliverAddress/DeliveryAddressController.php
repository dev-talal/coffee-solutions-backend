<?php

namespace App\Http\Controllers\Api\AppCustomer\DeliverAddress;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\DeliveryAddressService;
use App\Http\Resources\DeliveryAddressResource;
use App\Http\Requests\DeliveryAddressRequest;

class DeliveryAddressController extends Controller
{
    use ApiResponseTrait;
    protected $deliveryAddressService;
    public function __construct(DeliveryAddressService $deliveryAddressService)
    {
        $this->deliveryAddressService = $deliveryAddressService;
    }

    public function index(Request $request)
    {
        $deliveryAddresses = $this->deliveryAddressService->all();
        return $this->successCollection($deliveryAddresses, DeliveryAddressResource::class, 'Delivery addresses retrieved successfully');
    }

    public function store(DeliveryAddressRequest $request)
    {
        $deliveryAddresses = $request->validated();
        $deliveryAddresses['user_id'] = auth()->user()->id;
        $deliveryAddress = $this->deliveryAddressService->create($deliveryAddresses);
        return $this->successResource($deliveryAddress, DeliveryAddressResource::class, 'Delivery address created successfully');
    }

    public function edit($id)
    {
        $deliveryAddress = $this->deliveryAddressService->find($id);
        return $this->successResource($deliveryAddress, DeliveryAddressResource::class, 'Delivery address retrieved successfully');
    }

    public function update(DeliveryAddressRequest $request, $id)
    {
        $deliveryAddresses = $request->validated();
        $deliveryAddress = $this->deliveryAddressService->update($deliveryAddresses, $id);
        return $this->successResource($deliveryAddress, DeliveryAddressResource::class, 'Delivery address updated successfully');
    }

    public function destroy($id)
    {
        $this->deliveryAddressService->delete($id);
        return $this->success(null, 'Delivery address deleted successfully');
    }
}
