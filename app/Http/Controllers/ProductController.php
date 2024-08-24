<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assuming you have a Product model

class ProductController extends Controller
{
    // 1. List all products
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // 2. Show a single product by ID
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // 3. Store a new product
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'string',
            // Add other fields and validation rules as necessary
        ]);

        $product = Product::create($validatedData);
        // dd($product); 
        return response()->json($product, 201);
    }

    // 4. Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'string',
            // Add other fields and validation rules as necessary
        ]);

        $product->update($validatedData);
        // dd($product);

        return response()->json($product);
    }

    // 5. Delete a product by ID
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(null, 204); // Ensure 204 status code is returned
    }


    /**
     * Transform product data before saving.
     */
    public function transformProductData(array $data)
    {
        $data['name'] = ucfirst(strtolower($data['name']));
        return $data;
    }
}
