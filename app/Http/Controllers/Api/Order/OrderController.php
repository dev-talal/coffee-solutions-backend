<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderTransferHistory;
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;

class OrderController extends Controller
{
    use ApiResponseTrait;
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function getOrders(Request $request)
    {
        $assignedWarehouses = staffWareHouse(auth()->user());
        $orders = Order::whereIn('warehouse_id', $assignedWarehouses)->orderBy('id', 'desc')->paginate(10);
        return $this->successCollection($orders, OrderResource::class, 'Orders retrieved successfully');
    }

    public function getOrderDetails(Request $request)
    {
        $order = Order::find($request->id);
        if (!$order) {
            return $this->error('Order not found', 404);
        }
        return $this->successResource($order, OrderResource::class, 'Order retrieved successfully');
    }

    public function transferOrder(Request $request)
    {
        $validated = $request->validate([
            'transfer_to' => 'required|integer|exists:ware_houses,id',
            'order_id'    => 'required|integer|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if ($order->warehouse_id == $validated['transfer_to']) {
            return $this->error('Order cannot be transferred to the same warehouse.', 400);
        }

        DB::transaction(function () use ($order, $validated) {
            $transferFrom = $order->warehouse_id;

            $order->update([
                'warehouse_id' => $validated['transfer_to'],
            ]);

            OrderTransferHistory::create([
                'order_id'      => $order->id,
                'user_id'       => auth()->id(),
                'transfer_from' => $transferFrom,
                'transfer_to'   => $validated['transfer_to'],
            ]);
        });

        return $this->success(null, 'Order transferred successfully');
    }

    public function assignDriver(Request $request)
    {
        $validated = $request->validate([
            'order_id'    => 'required|integer|exists:orders,id',
            'driver_id'   => 'required|integer|exists:drivers,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $order->driver_id = $validated['driver_id'];
        $order->save();

        return $this->success(null, 'Order assigned successfully');
    }

    public function updateOrderStatus(Request $request)
    {
        $validated = $request->validate([
            'order_id'    => 'required|integer|exists:orders,id',
            'status'      => 'required|in:cancelled,dispatched,delivered,pending',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        if($request->status == 'cancelled'){
            try {
                $this->orderService->cancelOrder($order->user_id, $order->id);
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 422);
            }
        }else{
            $order->status = $validated['status'];
            $order->save();   
        }

        return $this->success(null, 'Order status updated successfully');
    }

}
