<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $this->logo_url,
            'banner' => $this->banner_url,
            'business_type' => $this->business_type,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'tax_id' => $this->tax_id,
            'verified_business' => $this->verified_business,
            'website_url' => $this->website_url,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'owner' => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
            ],
            
            'currency_config' => [
                'id' => $this->currency_config?->id,
                'default_currency' => $this->currency_config?->default_currency,
                'display_currencies' => $this->currency_config?->display_currencies,
                'use_custom_rates' => $this->currency_config?->use_custom_rates,
                'auto_update_rates' => $this->currency_config?->auto_update_rates,
                'last_rate_update' => $this->currency_config?->last_rate_update,
            ],
            
            'statistics' => [
                'products_count' => $this->whenLoaded('products_count', fn() => $this->products_count),
                'contacts_count' => $this->whenLoaded('contacts_count', fn() => $this->contacts_count),
                'payment_methods_count' => $this->whenLoaded('payment_methods_count', fn() => $this->payment_methods_count),
            ],
        ];
    }
}
