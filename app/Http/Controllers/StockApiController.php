<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductStock;

class StockApiController extends Controller
{
    /**
     * Mock API endpoint to check stock
     * This simulates the external API: http://stock.weliketoshop.net:8080/product/stock/check
     */
    public function check(Request $request)
    {
        $productId = $request->query('productId');
        $storeId = $request->query('storeId');

        // Validate parameters
        if (!$productId || !$storeId) {
            return response('Invalid parameters', 400);
        }

        // Find stock in database
        $stock = ProductStock::where('product_id', $productId)
            ->where('store_id', $storeId)
            ->first();

        if (!$stock) {
            return response('Product not found', 404);
        }

        // Return only the quantity number (as the original API does)
        return response($stock->quantity, 200);
    }
}
