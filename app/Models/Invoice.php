<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $issue_date
 * @property InvoiceType $type
 * @property InvoiceStatus $status
 */
class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'type' => InvoiceType::class,
            'status' => InvoiceStatus::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'buyer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
