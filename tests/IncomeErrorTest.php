<?php

use App\Models\User;
use Firebase\JWT\JWT;

class IncomeErrorTest extends TestCase
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

    public function testIfStoreReturnErrorWithNoDataProvided()
    {
        $this
            ->json('POST', '/api/incomes', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfShowReturnErrorWithInvalidId()
    {
        $this
            ->json('GET', '/api/incomes/99999', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfUpdateReturnErrorWithNoDataProvided()
    {
        $this
            ->json('PUT', '/api/incomes/1', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfUpdateReturnErrorWithInvalidId()
    {
        $this
            ->json('GET', '/api/incomes/99999', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfDestroyReturnErrorWithInvalidId()
    {
        $this
            ->json('DELETE', '/api/incomes/99999', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }
}
