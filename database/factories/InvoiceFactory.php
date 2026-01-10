<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => 'FV/'.now()->format('Y').'/'.$this->faker->unique()->numberBetween(1000, 9999),
            'type' => $this->faker->randomElement(InvoiceType::cases()),
            'currency' => Currency::PLN,
            'status' => InvoiceStatus::SENT,
            'buyer_id' => null,
            'seller_id' => null,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'bank_account_number' => $this->faker->iban('PL'),
            'paid_date' => null,
            'created_at' => now(),
        ];
    }
}
