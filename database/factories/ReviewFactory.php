<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Review;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use function config;

class ReviewFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Review::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $r = rand(1, 2);
        $user = User::inRandomOrder()->first();

        return [
            'user_id'     => $user->id,
            'job_id'      => $r !== 1 ? Job::inRandomOrder()->finished()->first()->id : null,
            'business_id' => $r === 1 ? Business::first()->id : null,
            'rating'      => rand(1, 5),
            'content'     => $this->faker->paragraph,
        ];
    }
}
