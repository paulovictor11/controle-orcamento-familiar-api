<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition()
    {
        return [
            'description' => $this->faker->text(50),
            'value' => $this->faker->randomNumber(),
            'date' => date_format($this->faker->dateTime(), 'Y-m-d')
        ];
    }
}
