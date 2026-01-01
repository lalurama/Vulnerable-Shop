<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Store;

class ProductController extends Controller
{
############################################################################################
    /**
     * Landing Page - Display all products
     */
    public function landing()
    {
        $products = Product::all();

        return view('landing', compact('products'));
    }

    /**
     * Product Detail Page
     */
    public function detail(Request $request)
    {
        $productId = $request->query('productId');

        if (!$productId) {
            return redirect()->route('landing');
        }

        $product = Product::find($productId);

        if (!$product) {
            return redirect()->route('landing')->with('error', 'Product not found');
        }

        // Get stores for stock check dropdown
        $stores = Store::all();

        // Get next product ID
        $nextProduct = Product::where('id', '>', $productId)->first();

        return view('product', compact('product', 'stores', 'nextProduct'));
    }

    public function nextProduct(Request $request)
    {
        $currentProductId = $request->query('currentProductId');
        $path = $request->query('path');

        // Validate current product exists
        if ($currentProductId) {
            $currentProduct = Product::find($currentProductId);
            if (!$currentProduct) {
                return redirect()->route('landing')
                    ->with('error', 'Product not found');
            }
        }

        // Validate path exists
        if (!$path) {
            return redirect()->route('landing')
                ->with('error', 'Invalid redirect path');
        }

        // Redirect ke path
        return redirect($path);
    }
    ###########################################################################################
    public function index()
    {
        $products = Product::all();
        $stores = Store::all();

        return view('products.index', compact('products', 'stores'));
    }

    public function checkStock(Request $request)
    {
        $request->validate([
            'stockApi' => 'required'
        ]);

        $stockApiUrl = $request->input(key: 'stockApi');

        try {
            // Make request to the stock API with retry mechanism
            $response = Http::retry(3, 100)->get($stockApiUrl);

            if ($response->successful()) {
                $stockLevel = $response->body();

                // Check if response is numeric
                if (is_numeric(trim($stockLevel))) {
                    return response($stockLevel . ' units', 200);
                }

                return response($stockLevel, 200);
            }

            return response('Could not fetch stock levels!', 500);
        } catch (\Exception $e) {
            return response('Could not fetch stock levels!', 500);
        }
    }
}
