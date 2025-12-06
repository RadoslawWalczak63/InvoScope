<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 500);
        $quantity = $this->faker->numberBetween(1, 10);
        $taxRate = $this->faker->randomElement([0, 0.05, 0.08, 0.23]);

        $total = $price * $quantity;
        $taxAmount = $total * $taxRate;
        $discount = $this->faker->randomFloat(2, 0, $total * 0.1);

        return [
            'invoice_id' => Invoice::factory(),
            'name' => $this->faker->words(3, true),
            'price' => $price,
            'quantity' => $quantity,
            'description' => $this->faker->sentence(),
            'tax' => ($taxRate * 100).'%',
            'tax_amount' => $taxAmount,
            'discount' => $discount,
            'unit' => $this->faker->randomElement(['pcs', 'hrs', 'kg', 'm']),
            'sku' => strtoupper($this->faker->bothify('??-####')),
        ];
    }
}
