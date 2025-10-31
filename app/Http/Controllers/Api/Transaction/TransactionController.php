<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $warehouses = staffWareHouse(auth()->user());
        $transactions = Transaction::whereIn('warehouse_id', $warehouses)->orderBy('id', 'desc')->paginate(10);
        $totalRevenue = totalRevenue($warehouses);
        $todayRevenue = todayRevenue($warehouses);
        $data = [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'transactions' => TransactionResource::collection($transactions)->response()->getData(true),
        ];

        return $this->successData($data, 'Transactions retrieved successfully');

    }
}