<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (isset($request->description)) {
                $description = $request->description;
                $expenses = Expense::where('description', 'like', "%{$description}%")->get();
            } else {
                $expenses = Expense::all();
            }

            return response()->json($expenses);
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

            $isExpenseAlreadySavedInThisMonth = $this->checkIfExpenseIsAlreadySavedInThisMonth($request);

            if ($isExpenseAlreadySavedInThisMonth) {
                throw new \Exception('Expense already saved in this month');
            }

            $expense = Expense::create($request->all());

            return response()->json([
                'message' => 'Expense saved successfuly',
                'data'    => $expense,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json(Expense::findOrFail($id));
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

            $expense = Expense::findOrFail($id);
            $expense->fill($request->all());
            $expense->save();

            return response()->json([
                'message' => 'Expense updated successfuly',
                'data'    => $expense,
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
            $expense = Expense::destroy($id);

            if ($expense === 0) {
                throw new \Exception("Unable to remove expense. Not found in our system.");
            }

            return response()->json([
                'message' => 'Expense removed successfuly'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getExpensesByMonth(string $year, string $month)
    {
        try {
            $firstDayOfMonth = date("{$year}-{$month}-01");
            $lastDayOfMonth = date("{$year}-{$month}-t");

            $expenses = DB::table('expenses')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->get();


            return response()->json($expenses);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function checkIfExpenseIsAlreadySavedInThisMonth(Request $request)
    {
        [, $month,] = explode('-', $request->date);

        $firstDayOfMonth = date("Y-{$month}-01");
        $lastDayOfMonth = date("Y-{$month}-t");

        return DB::table('expenses')
            ->where('description', $request->description)
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->first();
    }
}
