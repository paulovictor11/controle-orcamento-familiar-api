<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    private Income $model;

    public function __construct(Income $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            if (isset($request->description)) {
                $description = $request->description;
                $incomes = $this->model->where('description', 'like', "%{$description}%")->get();
            } else {
                $incomes = $this->model->all();
            }

            return response()->json($incomes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'description' => 'required',
                'value'       => 'required',
                'date'        => 'required',
            ]);

            $isIncomeAlreadySavedInThisMonth = $this->checkIfIncomeIsAlreadySavedInThisMonth($request);

            if ($isIncomeAlreadySavedInThisMonth) {
                throw new \Exception('Income already saved in this month');
            }

            $income = $this->model->create($request->all());

            return response()->json([
                'message' => 'Income saved successfuly',
                'data'    => $income,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->model->findOrFail($id));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $this->validate($request, [
                'description' => 'required',
                'value'       => 'required',
                'date'        => 'required',
            ]);

            $income = $this->model->findOrFail($id);
            $income->fill($request->all());
            $income->save();

            return response()->json([
                'message' => 'Income updated successfuly',
                'data'    => $income,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $income = $this->model->destroy($id);

            if ($income === 0) {
                throw new \Exception("Can't remove income. Not found in our system.");
            }

            return response()->json([
                'message' => 'Income removed successfuly'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getIncomesByMonth(string $year, string $month)
    {
        try {
            $firstDayOfMonth = date("{$year}-{$month}-01");
            $lastDayOfMonth = date("{$year}-{$month}-t");

            $incomes = $this->model->query()
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->get();


            return response()->json($incomes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function checkIfIncomeIsAlreadySavedInThisMonth(Request $request)
    {
        [, $month,] = explode('-', $request->date);

        $firstDayOfMonth = date("Y-{$month}-01");
        $lastDayOfMonth = date("Y-{$month}-t");

        return $this->model->query()
            ->where('description', $request->description)
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->first();
    }
}
