<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    private Expense $expense;
    private Income $income;

    public function __construct(Expense $expense, Income $income)
    {
        $this->expense = $expense;
        $this->income = $income;
    }

    public function resumeByMonth(string $year, string $month)
    {
        try {
            $totalOfIncomesInThisMonth = $this->getTotalInThisMonth($this->income, $year, $month);
            $totalOfExpensesInThisMonth = $this->getTotalInThisMonth($this->expense, $year, $month);
            $totalBalanceByCategory = $this->getTotalOfBalanceByCategory($year, $month);

            $endingBalanceInTheMonth = $totalOfIncomesInThisMonth - $totalOfExpensesInThisMonth;

            $data = [
                'totalOfIncomes' => $totalOfIncomesInThisMonth,
                'totalOfExpenses' => $totalOfExpensesInThisMonth,
                'totalBalance' => $endingBalanceInTheMonth,
                'totalByCategories' => $totalBalanceByCategory,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function getTotalInThisMonth(object $model, string $year, string $month)
    {
        $firstDayOfMonth = date("{$year}-{$month}-01");
        $lastDayOfMonth = date("{$year}-{$month}-t");

        if (
            !$this->validateDate($firstDayOfMonth) ||
            !$this->validateDate($lastDayOfMonth)
        ) {
            throw new \Exception('Invalid Date');
        }

        return $model
            ->query()
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->sum('value');
    }

    private function getTotalOfBalanceByCategory(string $year, string $month)
    {
        $firstDayOfMonth = date("{$year}-{$month}-01");
        $lastDayOfMonth = date("{$year}-{$month}-t");

        if (
            !$this->validateDate($firstDayOfMonth) ||
            !$this->validateDate($lastDayOfMonth)
        ) {
            throw new \Exception('Invalid Date');
        }

        $query = "select c.id, c.name, (
            select if(isnull(sum(e.value)), 0, sum(e.value))
            from expenses as e
            where e.date between ? and ?
            and c.id = e.category_id
        ) as total
        from categories as c
        group by c.id, c.name
        order by c.id;";

        return DB::select($query, [$firstDayOfMonth, $lastDayOfMonth]);
    }

    private function validateDate(string $date)
    {
        [$year, $month, $day] = explode('-', $date);

        return checkdate($month, $day, $year);
    }
}
