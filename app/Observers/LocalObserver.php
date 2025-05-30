<?php

namespace App\Observers;

use App\Models\Local;
use App\Models\TypeVoucher;

class LocalObserver
{
    /**
     * Handle the Local "created" event.
     */
    public function created(Local $local): void
    {
        $vouchers = TypeVoucher::all();
        foreach ($vouchers as $voucher) {
            $local->localVouchers()->attach($voucher->id, [
                'serie' => $voucher->prefix . str_pad($local->id, 2, '0', STR_PAD_LEFT),
                'correlative' => 0,
            ]);
        }
    }

    /**
     * Handle the Local "updated" event.
     */
    public function updated(Local $local): void
    {
        //
    }

    /**
     * Handle the Local "deleted" event.
     */
    public function deleted(Local $local): void
    {
        //
    }

    /**
     * Handle the Local "restored" event.
     */
    public function restored(Local $local): void
    {
        //
    }

    /**
     * Handle the Local "force deleted" event.
     */
    public function forceDeleted(Local $local): void
    {
        //
    }
}
