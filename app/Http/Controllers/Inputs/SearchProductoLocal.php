<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductLocalResource;
use App\Models\Product_Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchProductoLocal extends Controller
{
    public function searchProductL(Request $request)
    {
        $local_id = Auth::user()->local_id ?? 1;
        $search = $request->input('search');
        $products = Product_Local::with(['product', 'product_zones'])
            ->where('local_id', $local_id)
            ->whereHas('product', function ($query) use ($search) {
                $query->where('state', true)
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('barcode', 'like', "$search%")
                            ->orWhere('composition', 'like', "%$search%");
                        if (is_numeric($search)) {
                            $q->orWhere('id', $search);
                        }
                    });
            })
            ->paginate(5);
        return response()->json([
            'products' => ProductLocalResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ])->setStatusCode(200, 'OK');
    }
}
