<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (is_null($request->bearerToken())) {
            return response()->json(['message' => 'Required Token.'], 401);
        }

        $token = $request->bearerToken();
        $data = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

        $user = $data->user;
        $user = User::find($user->id);

        if (is_null($user)) {
            return response()->json(['message' => 'Invalid Token.'], 401);
        }

        return $next($request);
    }
}
