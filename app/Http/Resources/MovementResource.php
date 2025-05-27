<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tasaIgv = 0.18;
        $totalSubtotal = 0;
        $totalIgv = 0;
        $totalTotal = 0;

        // Check if product_movements relationship exists and is iterable
        if ($this->product_movements && is_iterable($this->product_movements)) {
            foreach ($this->product_movements as $movement) {
                $totalPrice = $movement->total_price ?? 0;

                if ($this->igv_status == 1) {
                    // IGV included in total_price
                    $subtotal = $totalPrice / (1 + $tasaIgv);
                    $igv = $totalPrice - $subtotal;
                    $total = $totalPrice;
                } else {
                    // IGV not included
                    $subtotal = $totalPrice;
                    $igv = $subtotal * $tasaIgv;
                    $total = $subtotal + $igv;
                }

                $totalSubtotal += $subtotal;
                $totalIgv += $igv;
                $totalTotal += $total;
            }
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'issue_date' => $this->issue_date->format('Y-m-d'),
            'credit_date' => $this->credit_date ? $this->credit_date->format('Y-m-d') : null,
            'supplier_id' => $this->supplier_id,
            'user_id' => $this->user_id,
            'type_movement_id' => $this->type_movement_id,
            'status' => $this->status,
            'statustext' => match ($this->status) {
                0 => 'Eliminado',
                1 => 'Activo',
                2 => 'Anulado',
                3 => 'Finalizado',
                default => 'Desconocido',
            },
            'igv_status' => $this->igv_status,
            'payment_type' => $this->payment_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'subtotal' => number_format($totalSubtotal, 2, '.', ''),
            'igv' => number_format($totalIgv, 2, '.', ''),
            'total' => number_format($totalTotal, 2, '.', ''),
            'supplier' => [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'typemovement' => [
                'id' => $this->typemovement->id,
                'name' => $this->typemovement->name,
            ],
        ];
    }
}