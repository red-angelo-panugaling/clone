<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\DashboardChartData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request, DashboardChartData $chartData): View
    {
        if (! $request->user()->isAdmin()) {
            return view('User.dashboard');
        }

        return view('Admin.dashboard', [
            'dashboardStats' => [
                'employees' => Employee::active()->count(),
                'users' => User::withoutLinkedEmployees()->count(),
                'activeUsers' => User::withoutLinkedEmployees()->where('status', true)->count(),
                'inactiveUsers' => User::withoutLinkedEmployees()->where('status', false)->count(),
            ],
            'charts' => $chartData->forDashboard(),
        ]);
    }
}
