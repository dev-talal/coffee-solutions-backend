<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Region\RegionController;
use App\Http\Controllers\Api\City\CityController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Controllers\Api\CustomerCategory\CustomerCategoryController;
use App\Http\Controllers\Api\Permission\PermissionController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\Warehourse\WarehouseController;
use App\Http\Controllers\Api\Product\ProductCategoryController;
use App\Http\Controllers\Api\Product\ProductControllerX as ProductController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Tax\TaxController;
use App\Http\Controllers\Api\Customer\CustomerSettingController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\AppCustomer\Home\HomeController;
use App\Http\Controllers\Api\Promotion\PromotionController;
use App\Http\Controllers\Api\AppCustomer\Wishlist\WishlistController;
use App\Http\Controllers\Api\AppCustomer\Cart\CartController;
use App\Http\Controllers\Api\AppCustomer\Order\OrderController;
use App\Http\Controllers\Api\AppCustomer\Transaction\TransactionController;
use App\Http\Controllers\Api\Transaction\TransactionController as DashboardTransactionController;
use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Controllers\Api\Order\OrderController as DashboardOrderController;
use App\Http\Controllers\Api\Driver\DriverController;
use App\Http\Controllers\Api\Product\ProductUnitController;
use App\Http\Controllers\Api\Chat\ChatController;
use App\Http\Controllers\BiometricAuthController;
use App\Http\Controllers\Api\AppCustomer\DeliverAddress\DeliveryAddressController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {
    // Authentication routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/biometric/challenge', [BiometricAuthController::class, 'challenge']);
    Route::post('/biometric/verify', [BiometricAuthController::class, 'verify']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('child/categories', [ProductCategoryController::class, 'getChildCategories']);
        Route::get('dashboard/orders', [DashboardOrderController::class, 'getOrders']);
        Route::get('order/details/{id}', [DashboardOrderController::class, 'getOrderDetails']);
        Route::post('order/assign/driver', [DashboardOrderController::class, 'assignDriver']);
        Route::post('order/transfer', [DashboardOrderController::class, 'transferOrder']);
        Route::post('update/order/status', [DashboardOrderController::class, 'updateOrderStatus']);
        Route::get('chat/rooms', [ChatController::class, 'getRooms']);
        Route::get('chat/customer/messages', [ChatController::class, 'getCustomerChat']);
        Route::post('chat/media/upload', [ChatController::class, 'uploadChatMedia']);
        Route::apiResource('chat', ChatController::class);
        Route::prefix('auth')->group(function () {
            Route::get('me',[AuthController::class, 'me']);
            Route::delete('logout', [AuthController::class, 'logout']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
            Route::post('update-profile', [AuthController::class, 'updateProfile']);
        });
        // Route::middleware(['can:is-admin'])->group(function () {
        // specific routes for admin only
        // });

        //apis for dashboards
        Route::middleware('module.permission:product units')->apiResource('product/units', ProductUnitController::class);
        Route::middleware('module.permission:drivers')->get('drivers/by/order/{orderId}', [DriverController::class, 'getDriversByOrder']);
        Route::middleware('module.permission:drivers')->apiResource('drivers', DriverController::class);
        Route::middleware('module.permission:regions')->apiResource('regions', RegionController::class);
        Route::middleware('module.permission:cities')->apiResource('cities', CityController::class);
        Route::middleware('module.permission:customer categories')->apiResource('customer/categories', CustomerCategoryController::class);
        Route::apiResource('permissions', PermissionController::class); 
        Route::middleware('module.permission:staff')->apiResource('staff', StaffController::class);
        Route::middleware('module.permission:roles')->apiResource('roles', RoleController::class);
        Route::middleware('module.permission:warehouse')->apiResource('warehouse', WarehouseController::class);
        Route::middleware('module.permission:product categories')->apiResource('product/categories', ProductCategoryController::class)->names('product.categories');
        Route::middleware('module.permission:taxes')->apiResource('taxes', TaxController::class);
        Route::middleware('module.permission:customers')->apiResource('customers', CustomerController::class);
        Route::middleware('module.permission:product')->apiResource('products', ProductController::class);
        Route::middleware('module.permission:promotions')->apiResource('promotions', PromotionController::class);
        Route::prefix('dashboard/transactions')->middleware('module.permission:transactions')->group(function(){
            Route::get('/', [DashboardTransactionController::class, 'index']);
        });

        Route::prefix('dashboard')->middleware('module.permission:dashboard')->group(function(){
            Route::get('popular/products', [DashboardController::class, 'getPopularProducts']);
            Route::post('custom/sales/grapgh', [DashboardController::class, 'customSalesGraph']);
            Route::get('today/performence', [DashboardController::class, 'todayPerformance']);
            Route::get('customer/{id}/cart', [CustomerController::class, 'getCustomerCart']);
            Route::post('cards/data', [DashboardController::class, 'getCardsData']);
        });
        
        // Customer settings (fetch all data for customers)
        Route::prefix('fetch')->group(function(){
            Route::get('region/{id}/cities', [RegionController::class, 'getCities']);
            Route::get('warehouses', [CustomerSettingController::class, 'getWarehouses']);
            Route::get('regions', [CustomerSettingController::class, 'getRegions']);
            Route::get('region/{id}/cities/warehouses', [CustomerSettingController::class, 'getCities']);
            Route::get('warehouse/{id}/customer-care-users', [CustomerSettingController::class, 'getCustomerCareUsers'])->name('customer.care.users');
            Route::get('customer/categories', [CustomerSettingController::class, 'getCustomerCategories']); 
        });

        Route::post('upload/image', [ProductController::class, 'uploadImage']);
        Route::post('delete/image', [ProductController::class, 'deleteImage']);

        //apis for customers or mobile app
        Route::prefix('customer')->middleware(['can:is-customer'])->group(function () {
            Route::get('transactions', [TransactionController::class, 'index']);
            Route::post('sync/cart', [CartController::class, 'syncCart']);
            Route::prefix('wishlist')->group(function () {
                Route::get('/', [WishlistController::class, 'index']);
                Route::post('add', [WishlistController::class, 'addWishlist']);
                Route::delete('remove/{productId}', [WishlistController::class, 'deleteWishlist']);
            });

            Route::prefix('order')->group(function(){
                Route::post('place', [OrderController::class, 'placeOrder']);
                Route::get('/', [OrderController::class, 'orderHistory']);
                Route::post('cancel', [OrderController::class, 'cancelOrder']);
            });

            Route::prefix('delivery-address')->group(function(){
                Route::get('/', [DeliveryAddressController::class, 'index']);
                Route::post('/', [DeliveryAddressController::class, 'store']);
                Route::get('edit/{id}', [DeliveryAddressController::class, 'edit']);
                Route::post('update/{id}', [DeliveryAddressController::class, 'update']);
                Route::delete('delete/{id}', [DeliveryAddressController::class, 'destroy']);
            });
        });
    });

    Route::prefix('customer')->middleware('optional.auth')->group(function () {
       Route::get('search/products', [HomeController::class, 'globalSearch']);
       Route::get('home', [HomeController::class, 'index']);
       Route::get('product/categories', [ProductCategoryController::class, 'index']);
       Route::get('category/{id}/products', [HomeController::class, 'getCategoryProducts']);
       Route::get('products', [ProductController::class, 'index']);
       Route::get('product/{id}/details', [HomeController::class, 'productDetails']);
       Route::get('suggested/products/{id}', [HomeController::class, 'suggestedProducts']);
       Route::get('taxes', [TaxController::class, 'getTaxesForCustomer']);

       Route::prefix('cart')->group(function(){
        Route::get('/', [CartController::class, 'index']);
        Route::post('add/product', [CartController::class, 'store']);
        Route::delete('remove/product/{cartId}', [CartController::class, 'destroy']);
        Route::get('count', [CartController::class, 'cartCount']);
       });
    });
});
