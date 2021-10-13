<?php

namespace Binarcode\LaravelDeveloper\Database\Factories;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeveloperLogFactory extends Factory
{
    protected $model = DeveloperLog::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->text(10),
            'payload' => $this->faker->text(100),
            'exception' => $this->faker->text(100),
        ];
    }
}

