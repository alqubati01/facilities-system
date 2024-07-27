<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
//            'facility_number' => fake()->numberBetween($min = 1000, $max = 2000),
            'date' => fake()->dateTimeBetween('-3 months'),
            'branch_id' => fake()->numberBetween(1, 11),
            'unit_id' => fake()->numberBetween(1, 3),
            'currency_id' => fake()->numberBetween(1, 3),
            'amount' => '300000',
            'amount_in_writing' => 'ثلاثمائة الف فقط',
            'type_id' => fake()->numberBetween(1, 2),
//            'details' => fake()->sentence($nbWords = 6, $variableNbWords = true),
//            'recipient' => fake()->name(),
            'specialization_id' => fake()->numberBetween(1, 4),
            'category_id' => fake()->numberBetween(1, 4),
//            'reason' => fake()->paragraph($nbSentences = 2, $variableNbSentences = true),
//            'neighboring_customers' => fake()->sentence($nbWords = 2, $variableNbWords = true),
            'created_by' => fake()->numberBetween(1, 3),
            'updated_by' => fake()->numberBetween(1, 3),
            'created_at' => fake()->dateTimeBetween('-3 months'),
        ];
    }
}
