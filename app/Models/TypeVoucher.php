<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'prefix',
        'name',
    ];
    protected $casts = [
        'code' => 'integer',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'type_voucher_id', 'id');
    }
    public function localVouchers(): BelongsToMany
    {
        return $this->belongsToMany(Local::class, 'local_voucher', 'voucher_id', 'local_id')
            ->withPivot('serie', 'correlative')
            ->withTimestamps();
    }
}
