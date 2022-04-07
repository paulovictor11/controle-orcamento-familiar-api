<?php

use App\Models\Expense;
use App\Models\User;
use Firebase\JWT\JWT;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExpenseTest extends TestCase
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

    public function testIfExpenseRouteIndexIsReturningSuccessfully()
    {
        $this->json('GET', '/api/expenses', [], $this->headers)
            ->seeStatusCode(200);
    }

    public function testIfExpenseRouteStoreIsSavingDataAndReturningSuccessfully()
    {
        $expense = Expense::factory()->make();
        $expense['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/expenses', $expense->toArray(), $this->headers)
            ->seeStatusCode(201)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'expenses saved successfuly']);
    }

    public function testIfExpenseRouteShowIsReturningSuccessfullyOneInstanceOfModel()
    {
        $expense = Expense::factory()->create();
        $this
            ->json('GET', '/api/expenses/' . $expense['id'], [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['id', 'description', 'value', 'date', 'category_id']);
    }

    public function testIfExpenseRouteUpdateIsUpdatingAndReturningSuccessfully()
    {
        $expense = Expense::factory()->create();
        $expense['value'] = 2;

        $this
            ->json('PUT', '/api/expenses/' . $expense['id'], $expense->toArray(), $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['message', 'data'])
            ->seeJson(['message' => 'expenses updated successfuly']);
    }

    public function testIfExpenseRouteDestroyIsRemovingInstaceAndReturningSuccessfully()
    {
        $expense = Expense::factory()->create();
        $this
            ->json('DELETE', '/api/expenses/' . $expense['id'], [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'expenses removed successfuly']);
    }

    public function testIfExpenseRouteStoreIsBlockingRepeatedInstanceInTheSameMonth()
    {
        $expense = Expense::factory()->make();
        $expense['date'] = '2022-08-23';

        $this
            ->json('POST', '/api/expenses', $expense->toArray(), $this->headers);

        $this
            ->json('POST', '/api/expenses', $expense->toArray(), $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'expenses already saved in this month']);
    }
}
