<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = PrismaService::getDashboardStats();
        $recentActivity = PrismaService::getRecentActivity(null, 10);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }
}
