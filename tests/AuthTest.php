<?php

use App\Models\User;

class AuthTest extends TestCase
{
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
}
