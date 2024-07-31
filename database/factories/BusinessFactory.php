<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'logo'        => 'https://localpro-assets.s3.eu-west-2.amazonaws.com/kiehn.png',
            'name'        => $this->faker->company,
            'description' => $this->faker->sentence,
            'location'    => 'UK',
            'address'     => 'UK London',
            'is_verified' => false,
        ];
    }
}
