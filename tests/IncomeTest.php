<?php

use App\Models\Income;
use App\Models\User;
use Firebase\JWT\JWT;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IncomeTest extends TestCase
{
    private array $headers;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $token = JWT::encode(['user' => $user], env('JWT_SECRET'), 'HS256');

        $this->headers = [
            'Authorization' => "Bearer {$token}"
        ];
    }

    public function testIfIncomeRouteIndexIsReturningSuccessfully()
    {
        $this->json('GET', '/api/incomes', [], $this->headers)
            ->seeStatusCode(200);
    }

    public function testIfIncomeRouteStoreIsSavingDataAndReturningSuccessfully()
    {
        $income = Income::factory()->make();
        $income['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/incomes', $income->toArray(), $this->headers)
            ->seeStatusCode(201)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'incomes saved successfuly']);
    }

    public function testIfIncomeRouteShowIsReturningSuccessfullyOneInstanceOfModel()
    {
        $income = Income::factory()->create();

        $this
            ->json('GET', '/api/incomes/' . $income['id'], [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['id', 'description', 'value', 'date']);
    }

    public function testIfIncomeRouteUpdateIsUpdatingAndReturningSuccessfully()
    {
        $income = Income::factory()->create();
        $income['value'] = 2;

        $this
            ->json('PUT', '/api/incomes/' . $income['id'], $income->toArray(), $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'incomes updated successfuly']);
    }

    public function testIfIncomeRouteDestroyIsRemovingInstaceAndReturningSuccessfully()
    {
        $income = Income::factory()->create();
        $this
            ->json('DELETE', '/api/incomes/' . $income['id'], [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'incomes removed successfuly']);
    }

    public function testIfIncomeRouteStoreIsBlockingRepeatedInstanceInTheSameMonth()
    {
        $income = Income::factory()->make();
        $income['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/incomes', $income->toArray(), $this->headers);

        $this
            ->json('POST', '/api/incomes', $income->toArray(), $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'incomes already saved in this month']);
    }
}
