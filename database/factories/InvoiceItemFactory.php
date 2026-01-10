<?php

namespace Database\Factories;

use App\Enums\InvoiceItemUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $services = [
            ['Web application development', 160, 220],
            ['Backend API development', 180, 260],
            ['Bug fixing and maintenance', 120, 170],
            ['Technical consulting', 200, 300],
            ['DevOps & deployment setup', 180, 260],
            ['UI/UX improvements', 130, 190],
            ['Performance optimization', 170, 240],
            ['Monthly maintenance package', 400, 1200],
            ['Hosting & monitoring', 100, 400],
            ['Code review', 150, 230],
        ];

        [$name, $min, $max] = $this->faker->randomElement($services);

        $price = $this->faker->randomFloat(2, $min, $max);

        $quantity = str_contains(strtolower($name), 'package') || str_contains(strtolower($name), 'hosting')
            ? 1
            : $this->faker->numberBetween(2, 20);

        $taxRate = 0.23;

        return [
            'invoice_id' => null,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'description' => $this->faker->sentence(),
            'tax' => '23%',
            'tax_amount' => $price * $quantity * $taxRate,
            'discount' => 0,
            'unit' => InvoiceItemUnit::HOUR,
            'sku' => strtoupper($this->faker->bothify('DEV-####')),
        ];
    }
}
