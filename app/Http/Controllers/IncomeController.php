<?php

namespace App\Http\Controllers;

use App\Models\Income;

class IncomeController extends BaseController
{
    public function __construct(Income $model)
    {
        parent::__construct($model);
    }
}
