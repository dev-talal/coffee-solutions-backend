<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\WareHouse;
use App\Models\StaffWarehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Calculate product discount for a given user.
 *
 * @param float $price
 * @param User|null $user
 * @return array
 */
function calculateDiscount(float $price, User $user = null): array
{
    $discountPercent = 0;
    $discountAmount = 0;

    if ($user && $user->customerCategory) {
        $discountPercent = $user->customerCategory->discount;
        $discountAmount = $price * $discountPercent / 100;
    }

    return [
        'discount_percent' => $discountPercent,
        'discount_amount' => $discountAmount,
        'final_price' => $price - $discountAmount,
    ];
}

function isLiked(?User $user, int $productId): bool
{
    return $user
        ? Wishlist::where('user_id', $user->id)->where('product_id', $productId)->exists()
        : false;
}

function getLikedProducts(User $user = null): array
{
    if (!$user) {
        return [];
    }

    return Wishlist::where('user_id', $user->id)
        ->pluck('product_id')
        ->toArray();
}

function totalRevenue($warehouses = [])
{
   $revenue = totalDebit($warehouses) - totalCredit($warehouses);
   return $revenue;
}


function todayRevenue($warehouses = [])
{
    $today = Carbon::today()->toDateString();
    $revenue = totalDebit($warehouses, $today, $today) - totalCredit($warehouses, $today, $today);
    return $revenue;
}

function totalDebit( $warehouses = [], $startDate = null, $endDate = null)
{
    $query = Transaction::where('type', 'Debit')->whereIn('warehouse_id', $warehouses);
    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ]);
    }
    $totalDebitAmount = $query->sum('amount');
    // Log::info("Total Debit Amount {$totalDebitAmount}");
    return $totalDebitAmount;
}

function totalCredit($warehouses = [], $startDate = null, $endDate = null)
{
    $query = Transaction::where('type', 'Credit')->whereIn('warehouse_id', $warehouses);
    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ]);
    }
    $totalCreditAmount = $query->sum('amount');
    // Log::info("Total Credit Amount {$totalCreditAmount}");
    return $totalCreditAmount;
}

function staffWareHouse(User $user = null): array
{
    if (!$user) {
        return [];
    }

    if ($user->hasRole('admin') || $user->hasRole('Admin')) {
        return WareHouse::withTrashed()->pluck('id')->toArray();
    }

    return StaffWarehouse::where('user_id', $user->id)
        ->pluck('warehouse_id')
        ->toArray();
}

function wareHouseStaff(WareHouse $warehouse = null): array
{
     if (!$warehouse) {
        return [];
    }
    
    return StaffWarehouse::where('warehouse_id', $warehouse->id)
        ->pluck('user_id')
        ->toArray();
}