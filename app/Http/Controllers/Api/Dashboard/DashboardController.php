<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\ProductService;
use App\Http\Resources\ProductResource;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use DB;

class DashboardController extends Controller
{
    use ApiResponseTrait;
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getPopularProducts(Request $request)
    {
        $popularProducts = $this->productService->homePopularProducts(true);
        return $this->successCollection($popularProducts, ProductResource::class, 'Popular products retrieved successfully');
    }

    public function customSalesGraph(Request $request)
    {
       $dates = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $startDate = Carbon::parse($dates['start_date'] ?? Carbon::now()->startOfMonth())->startOfDay();
        $endDate   = Carbon::parse($dates['end_date'] ?? Carbon::now()->endOfMonth())->endOfDay();

        $salesGraph = $this->graph($startDate, $endDate);
        return $this->success($salesGraph, 'Sales graph retrieved successfully');
    }

    public function todayPerformance()
    {
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd   = Carbon::now()->endOfDay();

        $yesterdayStart = Carbon::now()->subDay()->startOfDay();
        $yesterdayEnd   = Carbon::now()->subDay()->endOfDay();

        $sales = Order::selectRaw("
                SUM(CASE WHEN created_at BETWEEN ? AND ? THEN total ELSE 0 END) as today_sales,
                SUM(CASE WHEN created_at BETWEEN ? AND ? THEN total ELSE 0 END) as yesterday_sales
            ", [
                $todayStart, $todayEnd,
                $yesterdayStart, $yesterdayEnd
            ])
            ->first();

        $todaySales = $sales->today_sales;
        $yesterdaySales = $sales->yesterday_sales;

        // Avoid division by zero
        if ($yesterdaySales == 0) {
            $percentageChange = $todaySales > 0 ? 100 : 0;
        } else {
            $percentageChange = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        }

        return [
            'today' => $todaySales,
            'yesterday' => $yesterdaySales,
            'change' => round($percentageChange, 2)
        ];
    }

    public function graph($startDate, $endDate)
    {
        return DB::table('orders')
        ->selectRaw('DATE(created_at) as date, SUM(total) as total_sales')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    public function totalCustomers($startDate, $endDate)
    {
        return User::whereHas('roles', function($query){
            $query->where('name', 'customer');
        })->whereBetween('created_at', [$startDate, $endDate])->count();
    }

    public function totalOrders($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])->count();
    }

    public function totalSales($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
    }

    public function getCardsData(Request $request)
    {
        $dates = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $startDate = Carbon::parse($dates['start_date'] ?? Carbon::now()->startOfMonth())->startOfDay();
        $endDate   = Carbon::parse($dates['end_date'] ?? Carbon::now()->endOfMonth())->endOfDay();
        $data = [
            'total_customers' => $this->totalCustomers($startDate, $endDate),
            'total_orders' => $this->totalOrders($startDate, $endDate),
            'total_sales' => $this->totalSales($startDate, $endDate),
        ];

        return $this->success($data, 'Cards data retrieved successfully');
        
    }

}
