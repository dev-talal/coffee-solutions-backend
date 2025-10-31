<?php

namespace App\Http\Controllers\Api\AppCustomer\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\CartService;
use App\Http\Resources\CartResource;

class CartController extends Controller
{
    use ApiResponseTrait;   
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = auth()->check() ? auth()->id() : $request->query('guest_id');
        $isGuest = auth()->check() ? false : true;
        if(!$userId) {
            return $this->error('Session id is required', 400);
        }
        $carts = $this->cartService->paginate($userId, $isGuest);
        return $this->successCollection($carts, CartResource::class, 'Cart items retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:0',
            'is_box' => 'nullable',
        ];

        // If guest → require session_id
        if (!auth()->check()) {
            $rules['guest_id'] = 'required';
        } else {
            $rules['guest_id'] = 'nullable';
        }

        $cart = $request->validate($rules);

        if (auth()->check()) {
            $cart['user_id'] = auth()->id();
        }else{
            $cart['session_id'] = $cart['guest_id'];
        }

        unset($cart['guest_id']);
        $createdCart = $this->cartService->create($cart);

        return $this->successResource($createdCart, CartResource::class, 'Product saved to cart successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cartItem = $this->cartService->find($id);
        if (!$cartItem) {
            return $this->error('cart not found', 404);
        }
        $this->cartService->delete($id);
        return $this->success(null, 'cart deleted successfully');
    }

    public function syncCart(Request $request)
    {
        $request->validate([
            'guest_id' => 'required',
        ]);

        $this->cartService->syncCart($request->guest_id);
        return $this->success(null, 'cart synced successfully');
    }

    public function cartCount(Request $request)
    {
        $userId = auth()->check() ? auth()->id() : $request->query('guest_id');
        if(!$userId) {
            return $this->error('Session id is required', 400);
        }
        $isGuest = auth()->check() ? false : true;
        $cartCount = $this->cartService->count($userId, $isGuest);
        return $this->success(['cart_count' => $cartCount], 'Cart count retrieved successfully');
    }
}
