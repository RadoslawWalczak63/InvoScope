<?php

namespace App\Http\Resources;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InvoiceItem
 */
class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'price' => (float) $this->price,
            'total' => (float) $this->total,
            'tax' => $this->tax,
            'tax_amount' => (float) $this->tax_amount,
            'discount' => (float) $this->discount,
            'net_total' => (float) $this->net_total,
        ];
    }
}
