<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $query = Product::query();

        $query->when($request->input('search'), function($query, $isLike) {
            $query->where('name', 'like', "%{$isLike}%")
                ->orWhere('sku', 'like', "%{$isLike}%");
        });

        $query->when($request->input('subcategory_id'), function($query, $subcategory_id){
            $query->where('subcategory_id', '=', $subcategory_id);
        });

        $query->when($request->input('sort_by'), function($query, $column) use ($request) {
            $sort_direct = $request->input('sort_direction', 'asc');

            $query->orderBy($column, $sort_direct);
        }, function($query) {
            $query->orderBy('created_at', 'desc');
        });

        $user = $request->user();

        $products = $query->with([ 'subcategory', 'images'])->paginate(2)->withQueryString();

        if ($user->role === 'admin'){
            return Inertia::render('products/Index', [
                'products' => $products,
                'filter' => $request->only(['sort_by', 'search', 'subcategory_id', 'sort_direction']),
            ]);
        } else {
            return Inertia::render('products/StoreFront', [
                'products' => $products,
                'filter' => $request->only(['sort_by', 'search', 'subcategory_id', 'sort_direction']),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('products/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate all incoming data
        $validatedData = $request->validate([
            // --- REQUIRED fields ---
            'name'          => 'required|string',
            'price'         => 'required|numeric|min:1.00',
            'color_code'    => 'required',
            'image_url'     => 'required|image|max:5120',
            'subcategory_id'   => 'required',

            // --- NULLABLE (optional) fields ---
            'description'   => 'nullable|string|min:30|max:500',
            'sku'           => 'nullable|string|unique:products',
            'sale_price'      => 'nullable|numeric|min:0',
            'sale_start_date' => 'nullable|date',
            'sale_end_date'   => 'nullable|date|after_or_equal:sale_start_date',
        ]);

        $path = $validatedData['image_url']->store('upload', 'public');

        $productData = $validatedData;
        
        $productData['image_url'] = $path;

        Product::create($productData);

        return redirect('/products')->with('success', 'product has been added');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return Inertia::render('products/Show', ['product' => $product->load([ 'subcategory', 'images'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('products/Edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            // --- REQUIRED fields ---
                'name'          => 'required|string',
                'price'         => 'required|numeric|min:1.00',
                'color_code'    => 'required',
                // make image optional on update
                'image_url'     => 'nullable|image|max:5120',
                'subcategory_id'   => 'required|integer|exists:categories,id',

                // --- NULLABLE (optional) fields ---
                'description'   => 'nullable|string|min:30|max:500',
                // ignore current product id for unique check
                'sku'           => ['nullable','string', Rule::unique('products')->ignore($product->id)],
                'sale_price'      => 'nullable|numeric|min:0',
                'sale_start_date' => 'nullable|date',
                'sale_end_date'   => 'nullable|date|after_or_equal:sale_start_date',
        ]);

        if ($request->hasFile('image_url')) {
            // delete old file if it exists in storage (and is not a remote URL)
            if ($product->image_url && ! str_starts_with($product->image_url, 'http')) {
                Storage::disk('public')->delete($product->image_url);
            }

            $path = $request->file('image_url')->store('upload', 'public');
            $validatedData['image_url'] = $path;
        } else {
            // ensure we don't overwrite existing image_url when no file provided
            unset($validatedData['image_url']);
        }

        // Update the product with validated data
        $product->update($validatedData);

        return redirect('/products')->with('success', 'Product updated.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image_url && ! str_starts_with($product->image_url, 'http')) {
            Storage::disk('public')->delete($product->image_url);
        }
        $product->destroy($product->id);
        return redirect('/products')->with('success', 'delete successfully');
    }
}
