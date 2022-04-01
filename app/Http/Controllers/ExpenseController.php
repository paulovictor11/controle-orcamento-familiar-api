<?php

namespace App\Http\Controllers;

use App\Models\Expense;

class ExpenseController extends BaseController
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }
}
