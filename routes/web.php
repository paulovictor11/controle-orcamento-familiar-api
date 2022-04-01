<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('api/login', 'AuthController@login');
$router->post('api/register', 'AuthController@register');

$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    $router->get('me', 'AuthController@me');

    $router->get('resume/{year}/{month}', 'ResumeController@resumeByMonth');

    $router->group(['prefix' => 'incomes'], function () use ($router) {
        $router->get('', 'IncomeController@index');
        $router->post('', 'IncomeController@store');

        $router->get('{id}', 'IncomeController@show');
        $router->put('{id}', 'IncomeController@update');
        $router->delete('{id}', 'IncomeController@destroy');

        $router->get('{year}/{month}', 'IncomeController@getByMonth');
    });

    $router->group(['prefix' => 'expenses'], function () use ($router) {
        $router->get('', 'ExpenseController@index');
        $router->post('', 'ExpenseController@store');

        $router->get('{id}', 'ExpenseController@show');
        $router->put('{id}', 'ExpenseController@update');
        $router->delete('{id}', 'ExpenseController@destroy');

        $router->get('{year}/{month}', 'ExpenseController@getByMonth');
    });
});
