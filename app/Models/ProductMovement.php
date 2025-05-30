<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMovement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'fraction_quantity',
        'total_price',
        'unit_price',
        'fraction_price',
        'batch',
        'expiry_date',
        'movement_id',
        'status',
        'quantity_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'fraction_quantity' => 'integer',
        'total_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'fraction_price' => 'decimal:2',
        'expiry_date' => 'date',
        'status' => 'integer',
        'quantity_type' => 'integer',
    ];

    /**
     * Get the product that owns the product movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the movement that owns the product movement.
     */
    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class, 'movement_id');
    }
}