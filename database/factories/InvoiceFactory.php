<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => 'INV-'.$this->faker->unique()->numerify('#####'),
            'type' => $this->faker->randomElement(InvoiceType::cases()),
            'status' => $this->faker->randomElement(InvoiceStatus::cases()),
            'buyer_id' => Entity::factory(),
            'seller_id' => Entity::factory(),
            'issue_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
