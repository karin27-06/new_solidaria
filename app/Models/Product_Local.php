<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product_Local extends Model
{
    use HasFactory;

    protected $table = 'product_locals';
    protected $fillable = [
        'product_id',
        'local_id',
        'StockFraction',
        'StockBox',
        'stock_min',
        'stock_max',
    ];

    protected $casts = [
        'StockFraction' => 'integer',
        'StockBox' => 'integer',
        'stock_min' => 'integer',
        'stock_max' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id', 'id');
    }

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale', 'product_local_id', 'sale_id')
            ->using(ProductSale::class)
            ->withPivot('quantity_box', 'quantity_fraccion', 'price_box', 'price_fraccion', 'total')
            ->withTimestamps();
    }

    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(Guide::class, 'guide_products', 'product_local_id', 'guide_id')
            ->using(guide_products::class)
            ->withPivot('quantity_box', 'quantity_fraction')
            ->withTimestamps();
    }

    public function product_zones(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product_Zone::class,
            Product::class,
            'id',
            'product_id',
            'product_id',
            'id'
        );
    }
}
