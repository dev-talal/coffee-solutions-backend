<?php

namespace App\Http\Controllers\Api\AppCustomer\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Events\NewOrder;

class OrderController extends Controller
{
    use ApiResponseTrait;
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:wallet,card',
            'address_id' => 'required|integer|exists:user_delivery_addresses,id',
        ]);

         try {
            $paymentLink = $this->orderService->placeOrder(
                auth()->id(),
                $request->payment_method,
                auth()->user()->warehouse_id,
                $request->address_id
            );
            // $staffIds = wareHouseStaff($order->warehouse);
            
            // if(count($staffIds) > 0) {
            //     $orderPayload = new OrderResource($order);
            //     event(new NewOrder($orderPayload, $staffIds));
            // }

            return $this->success($paymentLink, 'Order placed successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }


    public function orderHistory(Request $request)
    {
        $status = $request->query('status', 'all');
        $orders = $this->orderService->getOrders(auth()->user()->id, $status);
        return $this->successCollection($orders, OrderResource::class, 'Orders retrieved successfully');
    }

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        try {
            $this->orderService->cancelOrder(auth()->user()->id, $request->order_id);
            return $this->success(null, 'Order cancelled successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
