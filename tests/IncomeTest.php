<?php

use App\Models\Income;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IncomeTest extends TestCase
{
    public function testIfIncomeRouteIndexIsReturningSuccessfully()
    {
        $this->json('GET', '/api/incomes')
            ->seeStatusCode(200);
    }

    public function testIfIncomeRouteStoreIsSavingDataAndReturningSuccessfully()
    {
        $income = Income::factory()->make();
        $income['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/incomes', $income->toArray())
            ->seeStatusCode(201)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'incomes saved successfuly']);
    }

    public function testIfIncomeRouteShowIsReturningSuccessfullyOneInstanceOfModel()
    {
        $income = Income::factory()->create();

        $this
            ->json('GET', '/api/incomes/' . $income['id'])
            ->seeStatusCode(200)
            ->seeJsonStructure(['id', 'description', 'value', 'date']);
    }

    public function testIfIncomeRouteUpdateIsUpdatingAndReturningSuccessfully()
    {
        $income = Income::factory()->create();
        $income['value'] = 2;

        $this
            ->json('PUT', '/api/incomes/' . $income['id'], $income->toArray())
            ->seeStatusCode(200)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'incomes updated successfuly']);
    }

    public function testIfIncomeRouteDestroyIsRemovingInstaceAndReturningSuccessfully()
    {
        $income = Income::factory()->create();
        $this
            ->json('DELETE', '/api/incomes/' . $income['id'])
            ->seeStatusCode(200)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'incomes removed successfuly']);
    }

    public function testIfIncomeRouteStoreIsBlockingRepeatedInstanceInTheSameMonth()
    {
        $income = Income::factory()->make();
        $income['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/incomes', $income->toArray());

        $this
            ->json('POST', '/api/incomes', $income->toArray())
            ->seeStatusCode(400)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'incomes already saved in this month']);
    }
}
