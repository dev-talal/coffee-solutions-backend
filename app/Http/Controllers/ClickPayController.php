<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

class ClickPayController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function handleCallback(Request $request)
    {
        // Log::info('ClickPay Callback:'. $request['tran_ref']);
        if (isset($request['tran_ref'])) {
           $order = Order::withoutGlobalScope('exclude_initiated')->where('payment_ref_id', $request['tran_ref'])->first();
            // Log::info("order lookup complete". json_encode($order));
            if($order){
                Log::info("order found");
                if($request['payment_result']['response_status'] === 'A'){
                    $orderItems = $this->orderService->getOrderItems($order->user_id);
                    $this->orderService->updateStock($orderItems['order_items'], $order->id);
                    $this->orderService->saveTransaction($order->user_id, $order->id, 'card', $order->total, $order->warehouse_id, 'Debit', 'Place Order');
                    Order::withoutGlobalScope('exclude_initiated')->where('payment_ref_id', $request['tran_ref'])->update(['payment_status' => 'paid']);
                    Cart::where('user_id', $order->user_id)->delete();
                }else{
                    Order::withoutGlobalScope('exclude_initiated')->where('payment_ref_id', $request['tran_ref'])->update(['payment_status' => 'unpaid']);
                }
            }
        }

    }
}
