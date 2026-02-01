<?php

namespace App\Models;

use App\Enums\EntityType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property EntityType $type
 * @property string $name
 */
class Entity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['name'];

    protected static function booted(): void
    {
        static::deleting(function ($entity) {
            $entity->invoices()->delete();
        });
    }

    protected function casts(): array
    {
        return [
            'type' => EntityType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this
            ->hasMany(Invoice::class, 'buyer_id')
            ->orWhere('seller_id', $this->id);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->type == EntityType::INDIVIDUAL) {
                    return trim("{$this->first_name} {$this->last_name}");
                }

                return $this->company_name ?? '';
            }
        );
    }
}
