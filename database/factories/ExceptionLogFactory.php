<?php

namespace Binarcode\LaravelDeveloper\Database\Factories;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExceptionLogFactory extends Factory
{
    protected $model = ExceptionLog::class;

    public function definition()
    {
        return [
            'name' => $this->faker->text(10),
            'payload' => $this->faker->text(100),
            'exception' => $this->faker->text(100),
        ];
    }
}

