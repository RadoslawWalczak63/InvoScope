<?php

namespace App\Models;

use App\Enums\Enum\EntityType;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => EntityType::class,
        ];
    }
}
