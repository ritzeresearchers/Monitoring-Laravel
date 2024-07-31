<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WorkCategory;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $user = User::where('email', 'customer@yahoo.com')->first();
        $businessUser = User::where('email', 'business@gmail.com')->first();
        $business = Business::with(['owners' => static function($query) use ($businessUser) {
            $query->where('user_id', $businessUser->id);
        }])->first();

        $payload = [
            'poster_id'                  => $user->id,
            'title'                      => $this->faker->jobTitle,
            'description'                => $this->faker->sentence,
            'target_completion_datetime' => null,
            'target_job_done'            => config('constants.targetJobDone.immediate'),
            'job_type'                   => config('constants.jobType.residential'),
            'location'                   => 'Canberra, 2600 ACT',
            'service_id'                 => WorkCategory::first()->id,
            'other_details'              => 'otehr details',
        ];

        $randomize = rand(1 ,3);
        if ($randomize === 1) {
            return array_merge($payload, [
                'hired_business_id' => null,
                'hired_datetime'    => null,
                'status'            => config('constants.jobStatus.active'),
            ]);
        } elseif ($randomize === 2) {
            return array_merge($payload, [
                'hired_business_id' => $business->id,
                'hired_datetime'    => now(),
                'status'            => config('constants.jobStatus.inProgress'),
            ]);
        }

        return array_merge($payload, [
            'hired_business_id' =>  $business->id,
            'hired_datetime'    => now(),
            'status'            => config('constants.jobStatus.finished'),
        ]);
    }
}
