<?php

class AuthErrorTest extends TestCase
{
    public function testIfLoginRouterReturnErrorWithNoCredentialsProvided()
    {
        $this
            ->json('POST', '/api/login', [])
            ->seeStatusCode(400)
            ->seeJsonStructure(['message', 'code']);
    }

    public function testIfLoginRouterReturnErrorInvalidCredentialsAreProvided()
    {
        $credentials = [
            'email' => 'test',
            'password' => '12345'
        ];

        $this
            ->json('POST', '/api/login', $credentials)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message', 'code']);
    }

    public function testIfLoginRouterReturnErrorWithNonexistentUser()
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => '12345'
        ];

        $this
            ->json('POST', '/api/login', $credentials)
            ->seeStatusCode(401)
            ->seeJsonStructure(['message', 'code'])
            ->seeJson(['message' => 'Invalid Credentials']);
    }

    public function testIfRegisterRouterReturnErrorWithNoCredentialsProvided()
    {
        $this
            ->json('POST', '/api/register', [])
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfRegisterRouterReturnErrorInvalidCredentialsAreProvided()
    {
        $credentials = [
            'email' => 'test',
            'password' => '12345'
        ];

        $this
            ->json('POST', '/api/login', $credentials)
            ->seeStatusCode(400)
            ->seeJsonStructure(['message']);
    }

    public function testIfMeRouterReturnErrorWithTokenIsNotProvided()
    {
        $this
            ->json('GET', '/api/me', [])
            ->seeStatusCode(401)
            ->seeJsonStructure(['message']);
    }

    public function testIfMeRouterReturnErrorInvalidTokenIsProvided()
    {
        $headers = [
            'Authorization' => "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9"
        ];
        $this
            ->json('GET', '/api/me', [], $headers)
            ->seeStatusCode(500)
            ->seeJsonStructure(['message']);
    }
}
