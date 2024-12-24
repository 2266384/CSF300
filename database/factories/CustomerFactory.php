<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The Model the factory relates to
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'primary_title' => fake()->title(),
            'primary_forename' => fake()->firstName(),
            'primary_surname' => fake()->lastName(),
        ];
    }

    /**
     * If we want to add a Date of Birth to the primary
     * @return Factory
     */
    public function primaryDoB()
    {
        return $this->state(fn (array $attributes) => [
            'primary_dob' => fake()->dateTimeBetween('1900-01-01', '2006-12-31')
                ->format('Y-m-d'),
        ]);
    }

    /**
     * If we want to add a Secondary name
     * @return Factory
     */
    public function secondary()
    {
        return $this->state(fn (array $attributes) => [
            'secondary_title' => fake()->title(),
            'secondary_forename' => fake()->firstName(),
            'secondary_surname' => fake()->lastName(),
        ]);
    }

    /**
     * If we want to add a date of birth to the secondary
     * @return Factory
     */
    public function secondaryDoB()
    {
        return $this->state(fn (array $attributes) => [
            'secondary_dob' => fake()->dateTimeBetween('1900-01-01', '2006-12-31')
                ->format('Y-m-d'),
        ]);
    }

}
