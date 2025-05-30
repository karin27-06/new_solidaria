<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Local extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'zone_id',
        'address',
        'series',
        'series_note',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];


    // Relación con movements
    public function movements()
    {
        return $this->hasMany(Movement::class, 'idLocal');
    }
    public function guidesOrigin(): HasMany
    {
        return $this->hasMany(Guide::class, 'origin_local_id', 'id');
    }
    public function guidesDestination(): HasMany
    {
        return $this->hasMany(Guide::class, 'destination_local_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'local_id', 'id');
    }
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'local_id', 'id');
    }
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }
    public function localVouchers(): BelongsToMany
    {
        return $this->belongsToMany(TypeVoucher::class, 'local_voucher', 'local_id', 'voucher_id')
            ->withPivot('serie', 'correlative')
            ->withTimestamps();
    }
}
