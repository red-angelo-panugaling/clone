<?php

namespace App\Http\Controllers;

use App\Services\DashboardChartData;
use Illuminate\View\View;

class ChartsController extends Controller
{
    public function index(DashboardChartData $chartData): View
    {
        return view('pages.charts', [
            'charts' => $chartData->forDashboard(),
        ]);
    }
}
