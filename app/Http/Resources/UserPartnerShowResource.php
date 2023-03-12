<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPartnerShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'full_address' => @$this->partner->full_address,
            'postal_code' => @$this->partner->postal_code,
            'province' => @$this->partner->province,
            'city' => @$this->partner->city,
            'district' => @$this->partner->district,
        ]);
    }
}
