<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'tracking_code' => $this->tracking_code,
            'carrier' => $this->carrier,
            'status' => $this->status,
            
            // From address
            'from_address' => [
                'name' => $this->from_name,
                'street1' => $this->from_street1,
                'street2' => $this->from_street2,
                'city' => $this->from_city,
                'state' => $this->from_state,
                'zip' => $this->from_zip,
                'country' => $this->from_country,
                'phone' => $this->from_phone,
                'email' => $this->from_email,
                'formatted' => $this->from_address,
            ],
            
            // To address
            'to_address' => [
                'name' => $this->to_name,
                'street1' => $this->to_street1,
                'street2' => $this->to_street2,
                'city' => $this->to_city,
                'state' => $this->to_state,
                'zip' => $this->to_zip,
                'country' => $this->to_country,
                'phone' => $this->to_phone,
                'email' => $this->to_email,
                'formatted' => $this->to_address,
            ],
            
            // Package details
            'package' => [
                'weight' => (float) $this->weight,
                'length' => (float) $this->length,
                'width' => (float) $this->width,
                'height' => (float) $this->height,
            ],
            
            // Label information
            'label' => [
                'url' => $this->label_url,
                'tracking_url' => $this->tracking_url,
                'postage_label_url' => $this->postage_label_url,
            ],
            
            // Rate information
            'rate_amount' => $this->rate_amount ? (float) $this->rate_amount : null,
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Status helpers
            'is_purchased' => $this->isPurchased(),
            'is_voided' => $this->isVoided(),
        ];
    }
}
