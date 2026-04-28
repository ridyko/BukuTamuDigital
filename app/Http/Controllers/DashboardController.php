<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        // Statistik hari ini
        $stats = [
            'total_today'       => Visitor::whereDate('check_in_at', $today)->count(),
            'active_now'        => Visitor::active()->count(),
            'checked_out_today' => Visitor::whereDate('check_in_at', $today)->checkedOut()->count(),
            'auto_checkout'     => Visitor::whereDate('check_in_at', $today)
                                    ->where('checkout_method', 'auto')->count(),
            'total_this_month'  => Visitor::whereMonth('check_in_at', now()->month)
                                    ->whereYear('check_in_at', now()->year)->count(),
        ];

        // Tamu aktif saat ini
        $activeVisitors = Visitor::active()
            ->with('host')
            ->orderBy('check_in_at', 'desc')
            ->take(10)
            ->get();

        // Kunjungan hari ini (terbaru)
        $recentVisitors = Visitor::whereDate('check_in_at', $today)
            ->with('host')
            ->orderBy('check_in_at', 'desc')
            ->take(8)
            ->get();

        // Log aktivitas terbaru
        $recentLogs = ActivityLog::with(['visitor', 'performer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Data chart kunjungan 7 hari terakhir
        $chartData = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->isoFormat('ddd, D MMM'),
                'count' => Visitor::whereDate('check_in_at', $date)->count(),
            ];
        });

        // Statistik per tujuan (top 5 host)
        $topHosts = User::where('role', 'staff')
            ->withCount(['visitsAsHost as visits_count' => function ($q) use ($today) {
                $q->whereDate('check_in_at', $today);
            }])
            ->orderBy('visits_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'activeVisitors',
            'recentVisitors',
            'recentLogs',
            'chartData',
            'topHosts'
        ));
    }
}
