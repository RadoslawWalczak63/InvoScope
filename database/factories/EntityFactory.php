<?php

namespace Database\Factories;

use App\Enums\EntityType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(EntityType::cases());

        $data = [
            'type' => $type,
            'user_id' => User::inRandomOrder()->first()?->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->optional()->buildingNumber,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'state' => $this->faker->optional()->state, // @phpstan-ignore-line
            'country' => $this->faker->country,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($type === EntityType::COMPANY) {
            $data['company_name'] = $this->faker->company;
            $data['tax_id'] = $this->faker->numerify('##########');
        }

        return $data;
    }
}
