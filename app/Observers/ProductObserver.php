<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Product_Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $userLogin = Auth::user();
        Product_Record::create([
            'description' => "Producto {$product->name} fue creado por {$userLogin->name}",
        ]);

        Log::info("Producto {$product->name} created");
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $userLogin = Auth::user();
        Product_Record::create([
            'description' => "Producto {$product->name} fue actualizado por {$userLogin->name}",
        ]);
        Log::info("Producto {$product->name} updated");
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        Product_Record::create([
            'description' => "Producto {$product->name} deleted",
        ]);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
