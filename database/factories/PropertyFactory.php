<?php

namespace Database\Factories;

use App\Imports\PropertiesImport;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{

    /**
     * The Model the factory relates to
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uprn' => $this->faker->unique()->randomNumber(9, true),
            'house_number' => fake()->buildingNumber(),
            'street' => fake()->streetName(),
            'postcode' => fake()->postcode(),
        ];
    }


    /**
     * Function for importing Property Data from the defined CSV file
     *
     * @return void
     */
    public function importcsv() {
            $filepath = public_path('CSV PAF.csv');
            //$filepath = public_path('properties.csv');
            Excel::import(new PropertiesImport, $filepath);
    }

}
