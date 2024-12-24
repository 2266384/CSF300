<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Telephone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Telephone>
 */
class TelephoneFactory extends Factory
{

    /**
     * The Model the factory relates to
     *
     * @var string
     */
    protected $model = Telephone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->randomElement(Customer::all()->pluck('id')),
            'std' => fake()->numerify('07###'),
            'number' => fake()->numerify('######'),
            'type' => 'Mobile',
        ];
    }

    /**
     * Create a landline number
     * @return Factory
     */
    public function landline()
    {
        return $this->state(fn (array $attributes) => [
            'std' => fake()->numerify('01###'),
            'number' => fake()->numerify('######'),
            'type' => 'Home',
        ]);
    }

}
