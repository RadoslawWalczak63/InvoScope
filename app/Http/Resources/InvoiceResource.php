<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Invoice
 *
 * @property Carbon $issue_date
 */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
            'currency' => $this->currency,
            'issue_date' => $this->issue_date->toDateString(),
            'buyer_id' => $this->buyer_id,
            'seller_id' => $this->seller_id,
            'buyer' => new EntityResource($this->whenLoaded('buyer')),
            'seller' => new EntityResource($this->whenLoaded('seller')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'grand_total' => $this->whenLoaded('items', function () {
                return $this->items->sum('net_total');
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
