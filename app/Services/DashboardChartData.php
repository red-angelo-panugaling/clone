<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardChartData
{
    /**
     * @return array{
     *     hiresByMonth: array{labels: list<string>, values: list<int>},
     *     hiresByFiveYears: array{labels: list<string>, values: list<int>},
     *     hiresByYear: array{labels: list<string>, values: list<int>},
     *     employeesByOffice: array{labels: list<string>, values: list<int>},
     *     userAccountsByStatus: array{labels: list<string>, values: list<int>}
     * }
     */
    public function forDashboard(): array
    {
        $driver = DB::connection()->getDriverName();
        $monthExpression = match ($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT(start_date, '%Y-%m')",
            'pgsql' => "TO_CHAR(start_date, 'YYYY-MM')",
            default => "strftime('%Y-%m', start_date)",
        };

        $firstMonth = now()->startOfMonth()->subMonths(59);
        $monthlyHires = Employee::active()
            ->whereBetween('start_date', [$firstMonth->toDateString(), now()->toDateString()])
            ->selectRaw("{$monthExpression} as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $fiveYearHireLabels = [];
        $fiveYearHireValues = [];

        for ($monthOffset = 0; $monthOffset < 60; $monthOffset++) {
            $month = $firstMonth->copy()->addMonthsNoOverflow($monthOffset);
            $period = $month->format('Y-m');

            $fiveYearHireLabels[] = $month->format('M Y');
            $fiveYearHireValues[] = (int) $monthlyHires->get($period, 0);
        }

        $yearExpression = match ($driver) {
            'mysql', 'mariadb' => 'YEAR(start_date)',
            'pgsql' => 'EXTRACT(YEAR FROM start_date)',
            default => "strftime('%Y', start_date)",
        };
        $yearlyHires = Employee::active()
            ->whereNotNull('start_date')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->selectRaw("{$yearExpression} as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $hireLabels = [];
        $hireValues = [];
        if ($yearlyHires->isNotEmpty()) {
            $firstYear = (int) $yearlyHires->keys()->first();
            $currentYear = now()->year;

            for ($year = $firstYear; $year <= $currentYear; $year++) {
                $hireLabels[] = (string) $year;
                $hireValues[] = (int) $yearlyHires->get($year, 0);
            }
        }

        $officeCounts = Employee::active()
            ->select('office')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('office')
            ->orderByDesc('total')
            ->orderBy('office')
            ->get();

        $topOffices = $officeCounts->take(7);
        $officeLabels = $topOffices->pluck('office')
            ->map(fn (?string $office): string => filled($office) ? $office : 'Unassigned')
            ->values()
            ->all();
        $officeValues = $topOffices->pluck('total')->map(fn (int|string $total): int => (int) $total)->values()->all();
        $otherOfficeCount = (int) $officeCounts->skip(7)->sum('total');

        if ($otherOfficeCount > 0) {
            $officeLabels[] = 'Other';
            $officeValues[] = $otherOfficeCount;
        }

        return [
            'hiresByMonth' => [
                'labels' => array_slice($fiveYearHireLabels, -12),
                'values' => array_slice($fiveYearHireValues, -12),
            ],
            'hiresByFiveYears' => [
                'labels' => $fiveYearHireLabels,
                'values' => $fiveYearHireValues,
            ],
            'hiresByYear' => [
                'labels' => $hireLabels,
                'values' => $hireValues,
            ],
            'employeesByOffice' => [
                'labels' => $officeLabels,
                'values' => $officeValues,
            ],
            'userAccountsByStatus' => [
                'labels' => ['Active', 'Inactive'],
                'values' => [
                    User::withoutLinkedEmployees()->where('status', true)->count(),
                    User::withoutLinkedEmployees()->where('status', false)->count(),
                ],
            ],
        ];
    }
}
