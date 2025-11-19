<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WidgetResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price !== null ? (string) $this->price : null,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toIso8601String() ?? null,
            'updated_at' => $this->updated_at?->toIso8601String() ?? null,
        ];
    }
}

