<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(InvoiceStatus::cases());

        return [
            'user_id' => User::factory(),
            'number' => 'INV-'.$this->faker->unique()->numerify('#####'),
            'type' => $this->faker->randomElement(InvoiceType::cases()),
            'currency' => $this->faker->randomElement(Currency::cases()),
            'status' => $status,
            'buyer_id' => Entity::factory(),
            'seller_id' => Entity::factory(),
            'issue_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'paid_date' => $status === InvoiceStatus::PAID ? $this->faker->dateTimeBetween('now', '+1 year') : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
