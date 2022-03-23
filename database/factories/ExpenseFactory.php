<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'description' => $this->faker->text(50),
            'value' => $this->faker->randomNumber(),
            'date' => date_format($this->faker->dateTime(), 'Y-m-d'),
            'category_id' => $this->faker->numberBetween(1, 8),
        ];
    }
}
