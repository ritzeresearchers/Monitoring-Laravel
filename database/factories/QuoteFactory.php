<?php

namespace Database\Factories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Quote::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'job_id'      => rand(1, 2),
            'business_id' => 1,
            'rate_type'   => config('constants.rateType.flat'),
            'cost'        => 23,
        ];
    }
}
