<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title'   => $this->faker->sentence(),
            'photo'   => 'https://homepages.cae.wisc.edu/~ece533/images/boat.png',
            'content' => $this->faker->sentence(),
            'user_id' => $this->faker->randomDigit,
        ];
    }
}
