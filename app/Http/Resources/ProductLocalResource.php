<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLocalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $zone_id = $this->local->zone->id ?? null;
        $zone_price = null;
        foreach ($this->product_zones as $pz) {
            if ($pz->zone_id == $zone_id) {
                $zone_price = $pz;
                break;
            }
        }
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'product_composition' => $this->product->composition,
            'states_fraction' => $this->product->state_fraction,
            'state_igv' => $this->product->state_igv,
            'fraction' => $this->product->fraction,
            'stockBox' => $this->StockBox,
            'stockFraction' => $this->StockFraction,
            'unit_price' => $zone_price->unit_price ?? null,
            'fraction_price' => $zone_price->fraction_price ?? null,
            'quantify_box' => 0,
            'quantify_fraction' => 0,
        ];
    }
}
