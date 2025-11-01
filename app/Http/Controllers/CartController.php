<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Pest\Laravel\json;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)->with(['product', 'product.images', 'user'])->get();
        return response()->json($carts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'quantity' => 'required|min:1',
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $validated['product_id'];

        $product = Product::findorFail($productId);

        $sub_total = $validated['quantity'] * $product->price;

        $existingCart = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCart) {
            $existingCart->quantity += $validated['quantity'];
            $existingCart->sub_total = $existingCart->quantity * $product->price;
            $existingCart->save();
        } else {
            Cart::create([
                'quantity' => $validated['quantity'],
                'sub_total' => $sub_total,
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }
        if ($request->wantsJson()) {
            // 1. If it was an Axios/Pinia request, send JSON
            return response()->json([
                'message' => 'Add to cart successfully.'
            ]);
        }

        // 2. Otherwise (it was an Inertia router request), do the Inertia redirect
        return redirect()->back()->with('success', 'Add to cart successfully.');
    }

    public function showAll(Request $request)
    {
        $user_id = $request->user()->id;
        $carts = Cart::where('user_id', $user_id)->with(['product', 'product.images'])->get();

        return Inertia::render('cart/Show', [
            'carts' => $carts
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $userId = $request->user()->id;
        $validated = $request->validate([
            'quantity' => 'required',
        ]);

        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $cart->update([
            'quantity' => $validated['quantity']
        ]);

        return redirect()->back()->with('success', 'updated quantity successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->destroy($cart->id);
    }
}
