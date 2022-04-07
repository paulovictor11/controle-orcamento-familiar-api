<?php

use App\Models\User;
use Firebase\JWT\JWT;

class AuthTest extends TestCase
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

    public function testIfCanAuthenticateUser()
    {
        $user = User::factory()->make();

        $credentials = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ];

        $this
            ->json('POST', '/api/register', $credentials);

        unset($credentials['name']);

        $this
            ->json('POST', '/api/login', $credentials)
            ->seeStatusCode(200)
            ->seeJsonStructure(['token', 'user']);
    }

    public function testIfIsAbleToRegisterAUser()
    {
        $user = User::factory()->make();
        $credentials = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ];

        $this
            ->json('POST', '/api/register', $credentials)
            ->seeStatusCode(201)
            ->seeJsonStructure(['message', 'token', 'user']);
    }

    public function testIfRouteMeIsReturningTheAuthenticatedUser()
    {
        $this
            ->json('GET', '/api/me', [], $this->headers)
            ->seeStatusCode(200)
            ->seeJsonStructure(['id', 'name', 'email', 'created_at', 'updated_at']);
    }
}
