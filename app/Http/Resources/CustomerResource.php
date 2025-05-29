<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'address' => $this->address,
            'phone' => $this->phone,
            'birthdate' => $this->birthdate ? $this->birthdate->format('d-m-Y') : null,
            'client_type_id' => $this->clientType->id,
            'client_type' => $this->clientType->name,
        ];
    }
}
