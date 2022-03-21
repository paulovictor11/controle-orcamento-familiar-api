<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Income::all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'description' => 'required',
                'value'       => 'required',
                'date'        => 'required',
            ]);

            [, $month,] = explode('-', $request->date);

            $firstDayOfMonth = date("Y-{$month}-01");
            $lastDayOfMonth = date("Y-{$month}-t");

            $isIncomeAlreadySavedInThisMonth = DB::table('incomes')
                ->where('description', $request->description)
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->first();

            if ($isIncomeAlreadySavedInThisMonth) {
                throw new \Exception('Income already saved this month');
            }

            $income = Income::create($request->all());

            return response()->json([
                'message' => 'Income saved successfuly',
                'data'    => $income,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(int $id)
    {
        try {
            return response()->json(Income::findOrFail($id));
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

            $income = Income::findOrFail($id);
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

    public function destroy(int $id)
    {
        try {
            $income = Income::destroy($id);

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
}
