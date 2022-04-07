<?php

use App\Models\User;
use Firebase\JWT\JWT;

class ResumeErrorTest extends TestCase
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

    public function testIfResumeRouterReturnsErrorWithInvalidYear()
    {
        $this
            ->json('GET', '/api/resume/2022a/03', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'A non well formed numeric value encountered']);
    }

    public function testIfResumeRouterReturnsErrorWithInvalidMonth()
    {
        $this
            ->json('GET', '/api/resume/2022/13', [], $this->headers)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message'])
            ->seeJson(['message' => 'Invalid Date']);
    }
}
