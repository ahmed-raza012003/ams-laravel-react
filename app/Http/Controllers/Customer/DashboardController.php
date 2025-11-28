<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $stats = PrismaService::getDashboardStats($userId);
        $recentActivity = PrismaService::getRecentActivity($userId, 10);

        return Inertia::render('Customer/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }
}
