<?php

namespace App\Pipelines\Guides;

use App\Models\Product_Local;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class validateProducts
{
  public function __invoke($guideData, Closure $next)
  {
    $originLocalId = $guideData['origin_local_id'];
    $products = $guideData['products'];
    $productIds = collect($products)->pluck('product_id')->all();
    $stockData = Product_Local::where('local_id', $originLocalId)
      ->whereIn('product_id', $productIds)
      ->get(['product_id', 'StockBox', 'StockFraction'])
      ->keyBy('product_id');

    foreach ($products as $product) {
      $stock = $stockData->get($product['product_id']);
      if (!$stock) {
        throw ValidationException::withMessages([
          "products.{$product['product_id']}" => 'Producto no disponible en este local.',
        ]);
      }
      $requiredBox = $product['quantity_box'] ?? 0;
      $requiredFraction = $product['quantity_fraction'] ?? 0;
      $hasBox = $stock->StockBox >= $requiredBox;
      $hasFraction = $stock->StockFraction >= $requiredFraction;
      if (!$hasBox || !$hasFraction) {
        throw ValidationException::withMessages([
          "products.{$product['product_id']}" => 'Stock insuficiente (caja o fracción).',
        ]);
      }
    }

    Log::info('validateProducts pipeline passed', [
      'local_id' => $originLocalId,
      'products_checked' => count($products),
    ]);

    return $next($guideData);
  }
}
