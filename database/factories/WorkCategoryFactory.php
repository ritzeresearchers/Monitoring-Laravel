<?php

namespace Database\Factories;

use App\Models\WorkCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkCategoryFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = WorkCategory::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name'        => ucfirst($this->faker->words(2)),
            'thumbnail'   => 'https://localpro-assets.s3.eu-west-2.amazonaws.com/carpenter.png',
            'description' => $this->faker->sentence(),
        ];
    }
}
