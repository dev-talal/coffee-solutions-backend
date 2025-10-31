<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tax;
use App\Models\WareHouse;
use App\Models\Transaction;
use App\Models\UserDeliveryAddress;
use App\Models\CustomerCreditHistory;
use Illuminate\Support\Facades\Log;
use App\Services\ClickPayService;
use Exception;

class OrderService
{
    protected $clickPayService;
    public function __construct(ClickPayService $clickPayService)
    {
        $this->clickPayService = $clickPayService;
    }
    function placeOrder(string $userId, string $paymentMethod, int $warehouseId, int $addressId)
    {
        return DB::transaction(function () use ($userId, $paymentMethod, $warehouseId, $addressId) {

            $warehouse = WareHouse::find($warehouseId);
            if (!$warehouse) {
                throw new Exception('Warehouse not found.');
            }

            $orderItems = $this->getOrderItems($userId);
            $subtotal = $orderItems['subtotal'];
            $tax = $orderItems['tax'];
            $orderItemsData = $orderItems['order_items'];

            $taxes = $this->getAllTaxes();

            foreach ($taxes as $sTax) {
                $taxRate = $sTax->rate;
                $taxAmount = $subtotal * $taxRate / 100;
                $tax += $taxAmount;
            }
            $total = $subtotal;
            $subtotal = $total - $tax;
            $walletUsed = 0;
            $paymentLink = null;
            if ($paymentMethod == 'wallet') {
                $walletBalance = auth()->user()->credit_limit;

                if ($walletBalance < $total) {
                    throw new Exception('Insufficient wallet balance.');
                }

                auth()->user()->decrement('credit_limit', $total);
                $this->saveCustomerCreditHistory($userId, $total, 'debit');
            }else{
                $paymentInfo = $this->generatePaymentLink($total);

                $paymentLink = [
                    'redirect_url' => $paymentInfo['redirect_url'],
                    'tran_ref' => $paymentInfo['tran_ref'],
                ];
            }

            $deliveryAddress = UserDeliveryAddress::find($addressId);

            $order = Order::create([
                'user_id' => $userId,
                'sub_total' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod == 'wallet' ? 'paid' : 'initiated',
                'warehouse_id' => $warehouseId,
                'is_linked' => $deliveryAddress->is_link,
                'short_address' => $deliveryAddress->short_address,
                'building_number' => $deliveryAddress->building_number,
                'secondary_number' => $deliveryAddress->secondary_number,
                'postal_code' => $deliveryAddress->postal_code,
                'city' => $deliveryAddress->city,
                'country' => $deliveryAddress->country,
                'address_link' => $deliveryAddress->address_link,
                'ar_short_address' => $deliveryAddress->ar_short_address,
                'ar_building_number' => $deliveryAddress->ar_building_number,
                'ar_secondary_number' => $deliveryAddress->ar_secondary_number,
                'ar_postal_code' => $deliveryAddress->ar_postal_code,
                'ar_city' => $deliveryAddress->ar_city,
                'ar_country' => $deliveryAddress->ar_country,
                'payment_ref_id' => isset($paymentInfo['tran_ref']) ? $paymentInfo['tran_ref'] : null,
            ]);

            if($order) {
                if($paymentMethod == 'wallet'){
                    Cart::where('user_id', $userId)->delete();
                    $this->updateStock($orderItemsData, $order->id);
                    $this->saveTransaction($userId, $order->id, $paymentMethod, $total, $warehouseId, 'Debit', 'Place Order');
                }
            }

            return $paymentLink;
            // return $order->load('items');
        });
    }

    public function getOrderItems($userId)
    {
        $cartItems = Cart::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            throw new Exception('Cart is empty.');
        }
        $orderItemsData = [];
        $subtotal = 0;
        $tax = 0;

        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            if(!$product){
                throw new Exception("One of the products is not found.");
            }

            if($product->quantity == 0){
                throw new Exception("Product {$product->name} is out of stock.");
            }

            if($item->is_box){
                $totalBoxes = floor($product->quantity / $product->pieces_per_box);
                if ($totalBoxes < $item->quantity) {
                    throw new Exception("Product {$product->name} is out of stock.");
                }
            }else{
                if ($product->quantity < $item->quantity) {
                    throw new Exception("Product {$product->name} is out of stock.");
                }
            }

            $price = calculateDiscount($product->price, auth()->user())['final_price'];
            $quantity = $item->quantity;
            if($item->is_box){
                $price = $price * $product->pieces_per_box;
                $quantity = $item->quantity * $product->pieces_per_box;
            }
            $lineTotal = $price * $item->quantity;

            $subtotal += $lineTotal;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'price' => $price,
                'total' => $lineTotal,
                'is_box' => $item->is_box,
                'stock_quantity' => $quantity,
                'product_name' => $product->name,
                'ar_product_name' => $product->ar_name,
                'product_price' => $product->price,
                'product_image' => $product->images->first()->image,
                'product_pieces_per_box' => $product->pieces_per_box,
                'unit' => $product->productUnit->name ?? '',
                'ar_unit' => $product->uomProductUnit->ar_name ?? '',
                'uom_unit' => $product->uomProductUnit->name ?? '',
                'ar_uom_unit' => $product->uomProductUnit->ar_name ?? '',
            ];
        }
        return [
            'order_items' => $orderItemsData,
            'subtotal' => $subtotal,
            'tax' => $tax,
        ];
    }

    public function generatePaymentLink($totoal)
    {
        return $this->clickPayService->createPayment([
            'name' => auth()->user()->first_name.' '.auth()->user()->last_name,
            'email' => auth()->user()->email,
            'amount' => $totoal,
        ]);
    }

    public function saveCustomerCreditHistory($userId, $amount, $type)
    {
        CustomerCreditHistory::create([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => $type,
        ]);
    }

    public function updateStock($orderItemsData, $orderId)
    {
        foreach ($orderItemsData as $item) {
            $stockQuantity = $item['stock_quantity'];
            unset($item['stock_quantity']);
            $data = array_merge($item, ['order_id' => $orderId]);
            OrderItem::create($data);
            Product::where('id', $data['product_id'])->decrement('quantity', $stockQuantity);
        }
    }

    public function getOrders($userId, $status = 'all')
    {
        $orders = Order::where('user_id', $userId);
        if($status != 'all'){
            $orders = $orders->where('status', $status);
        }
        return $orders->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAllTaxes()
    {
        return Tax::where('status', 1)->get();
    }

    public function saveTransaction( $userId, $orderId, $type, $amount, $warehouse, $transactionType = "Debit", $reason = null)
    {
       Transaction::create([
           'user_id' => $userId,
           'order_id' => $orderId,
           'method' => $type,
           'type' => $transactionType,
           'amount' => $amount,
           'warehouse_id' => $warehouse,
           'status' => 'paid',
           'reason' => $reason,
       ]);
    }

    public function cancelOrder($userId, $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            throw new Exception('Order not found.');
        }

        // if ($order->user_id != $userId) {
        //     throw new Exception('You are not authorized to cancel this order.');
        // }

        // if($order->status != 'pending') {
        //     throw new Exception('Order Can not be cancelled.');
        // }

        $order->status = 'cancelled';
        $order->payment_status = 'refunded';
        $order->save();

        $orderItems = $order->items;
        foreach ($orderItems as $item) {
            $product = Product::find($item->product_id);
            $quantity = $item->quantity;
            if($item->is_box){
                $quantity = $item->quantity * $item->product_pieces_per_box;
            }
            // Log::info('OrderItem::cancelOrder::quantity: ' . $quantity. 'product id'. $product->id);
            Product::where('id', $product->id)->increment('quantity', $quantity);
        }
        if($order->payment_method == 'wallet'){
            auth()->user()->increment('credit_limit', $order->total);
            $this->saveCustomerCreditHistory($userId, $order->total, 'credit');
        }else{
            $this->clickPayService->refund([
                'amount' => $order->total,
                'tran_ref' => $order->payment_ref_id,
            ]);
        }
        $this->saveTransaction($userId, $orderId, 'wallet', $order->total, $order->warehouse_id, 'Credit', 'Cancel Order');
    }
}
