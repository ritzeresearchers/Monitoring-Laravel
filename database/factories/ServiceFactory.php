<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Service::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => 'Services 1',
            'description' => 'Services desc1',
        ];
    }
}
