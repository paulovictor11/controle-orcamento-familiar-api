<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    private Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        try {
            return response()->json($this->model->all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
