<?php

use App\Models\User;
use Firebase\JWT\JWT;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResumeTest extends TestCase
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

    public function testIfResumeRouteIsReturningSuccessfully()
    {
        $this
            ->json('GET', '/api/resume/2022/03', [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'totalOfIncomes',
                'totalOfExpenses',
                'totalBalance',
                'totalByCategories'
            ]);
    }
}
