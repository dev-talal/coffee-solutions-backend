<?php

namespace App\Http\Controllers\Api\AppCustomer\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\WishlistService;
use App\Http\Resources\WishlistResource;

class WishlistController extends Controller
{
    use ApiResponseTrait;
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        $wishlists = $this->wishlistService->fetchCustomerWishlists(auth()->user()->id);
        return $this->successCollection($wishlists, WishlistResource::class, 'Wishlists retrieved successfully');
    }

    public function addWishlist(Request $request)
    {
        $wishList = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $wishList['user_id'] = auth()->user()->id;

        // Check if the product is already in the wishlist
        if ($this->wishlistService->checkProductInWishlist($wishList['product_id'], $wishList['user_id'])) {
            return $this->error('Product already in wishlist', 400);
        }
        $wishlist = $this->wishlistService->create($wishList);
        return $this->successResource($wishlist, WishlistResource::class, 'Wishlist created successfully');
    }

    public function deleteWishlist($productId)
    {
        $customerId = auth()->user()->id;
        $this->wishlistService->deleteWishlist($productId, $customerId);
        return $this->success(null, 'Wishlist deleted successfully');
    }
}
