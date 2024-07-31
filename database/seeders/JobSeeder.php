<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Service;
use App\Models\WorkCategory;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Job;

class JobSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        Service::create([
            'name'             => 'test',
            'work_category_id' => WorkCategory::first()->id,
        ]);

        for ($x = 0; $x < 10; $x++) {
            $user = User::where('user_type', 'customer')->inRandomOrder()->first();
            $businessUser = User::where('user_type', 'business')->inRandomOrder()->first();
            $business = $businessUser->businesses()->first();

            for ($y = 0; $y < 9; $y++) {
                $jobPayload = [
                    'poster_id'                  => $user->id,
                    'title'                      => "This is a job title {$y}",
                    'description'                => "This is a job description {$y}",
                    'hired_business_id'          => null,
                    'hired_datetime'             => null,
                    'service_id'                 => Service::first()->id,
                    'category_id'                => WorkCategory::first()->id,
                    'location_id'                => Location::first()->id,
                    'other_details'              => 'otehr details',
                    'job_type'                   => config('constants.jobType.residential'),
                    'target_job_done'            => config('constants.targetJobDone.immediate'),
                    'target_completion_datetime' => null,
                    'status'                     => config('constants.jobStatus.active'),
                ];
                if ($y <= 3) {
                    $job = Job::create($jobPayload);
                    $job->lead()->create([
                        'job_id'      => $job->id,
                        'business_id' => $business->id,
                    ]);
                } elseif ($y < 4) {
                    $jobPayload['hired_business_id'] = $business->id;
                    $jobPayload['hired_datetime'] = now();

                    $job = Job::create($jobPayload);
                    $job->lead()->create([
                        'job_id'      => $job->id,
                        'business_id' => $business->id,
                        'is_accepted' => 1,
                        'has_quoted'  => 1,
                    ]);

                    $job->quotes()->create([
                        'job_id'      => $job->id,
                        'business_id' => $business->id,
                        'lead_id'     => $job->lead->id,
                        'rate_type'   => config('constants.rateType.flat'),
                        'cost'        => 23,
                        'is_accepted' => 1,
                    ]);
                } else {
                    $jobPayload['hired_business_id'] = $business->id;
                    $jobPayload['hired_datetime'] = now();
                    $jobPayload['status'] = config('constants.jobStatus.finished');

                    $job = Job::create($jobPayload);
                    $job->lead()->create([
                        'job_id'      => $job->id,
                        'business_id' => $business->id,
                        'is_accepted' => 1,
                        'has_quoted'  => 1,
                    ]);

                    $job->quotes()->create([
                        'job_id'      => $job->id,
                        'business_id' => $business->id,
                        'lead_id'     => $job->lead->id,
                        'rate_type'   => config('constants.rateType.flat'),
                        'cost'        => 23,
                        'is_accepted' => 1,
                    ]);
                }
            }
        }
    }
}
