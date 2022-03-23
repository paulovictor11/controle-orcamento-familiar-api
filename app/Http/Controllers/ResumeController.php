<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    public function resumeByMonth(string $year, string $month)
    {
        try {
            $totalOfIncomesInThisMonth = $this->getTotalInThisMonth('incomes', $year, $month);
            $totalOfExpensesInThisMonth = $this->getTotalInThisMonth('expenses', $year, $month);
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

    private function getTotalInThisMonth(string $table, string $year, string $month)
    {
        $firstDayOfMonth = date("{$year}-{$month}-01");
        $lastDayOfMonth = date("{$year}-{$month}-t");

        return DB::table($table)
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->sum('value');
    }

    private function getTotalOfBalanceByCategory(string $year, string $month)
    {
        $firstDayOfMonth = date("{$year}-{$month}-01");
        $lastDayOfMonth = date("{$year}-{$month}-t");

        return DB::select("
            select c.id, c.name, (
                select if(isnull(sum(e.value)), 0, sum(e.value))
                from expenses as e
                where e.date between ? and ?
                and c.id = e.category_id
            ) as total
            from categories as c
            group by c.id, c.name
            order by c.id;
        ", [$firstDayOfMonth, $lastDayOfMonth]);
    }
}
