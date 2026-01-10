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
        $types = [
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::COMPANY,
            EntityType::INDIVIDUAL,
            EntityType::INDIVIDUAL,
            EntityType::INDIVIDUAL,
        ];

        $type = $types[array_rand($types)];

        $data = [
            'type' => $type,
            'user_id' => User::inRandomOrder()->first()?->id,
            'first_name' => $type === EntityType::INDIVIDUAL ? $this->faker->firstName : null,
            'last_name' => $type === EntityType::INDIVIDUAL ? $this->faker->lastName : null,
            'email' => $type === EntityType::INDIVIDUAL
                ? $this->faker->unique()->safeEmail
                : $this->faker->unique()->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->optional()->secondaryAddress,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'state' => $this->faker->optional()->state,
            'country' => $this->faker->country,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($type === EntityType::COMPANY) {
            $companyPrefixes = ['Tech', 'Soft', 'Cloud', 'Dev', 'Net', 'Code', 'Data', 'AI', 'Logic', 'Digital'];
            $companySuffixes = ['Solutions', 'Labs', 'Systems', 'Studios', 'Works', 'Agency', 'Group', 'Services'];

            $data['company_name'] = $this->faker->randomElement($companyPrefixes)
                .' '
                .$this->faker->randomElement($companySuffixes);

            $data['tax_id'] = $this->faker->numerify('##########');
        }

        return $data;
    }
}
