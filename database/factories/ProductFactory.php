<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $state_fraction = $this->faker->boolean();
        $fraction = $state_fraction ? $this->faker->randomElement([12, 15, 30, 50, 100, 150]) : 1;

        return [
            'name' => substr($this->faker->name, 0, 50),
            'composition'  => substr($this->faker->text, 0, 10),
            'presentation'  => substr($this->faker->text, 0, 10),
            'form_farm'  => substr($this->faker->text, 0, 10),
            'barcode'  => $this->faker->numerify('8#######'),
            'laboratory_id' => $this->faker->numberBetween(1, 100),
            'category_id' => $this->faker->numberBetween(1, 300),
            'fraction'    => $fraction,
            'state_fraction'    => $state_fraction,
            'state_igv'    => $this->faker->boolean(),
            'state'    => $this->faker->boolean(),
        ];
    }
}
