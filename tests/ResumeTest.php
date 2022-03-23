<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResumeTest extends TestCase
{
    public function testIfResumeRouteIsReturningSuccessfully()
    {
        $this
            ->json('GET', '/api/resume/2022/03')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'totalOfIncomes',
                'totalOfExpenses',
                'totalBalance',
                'totalByCategories'
            ]);
    }
}
