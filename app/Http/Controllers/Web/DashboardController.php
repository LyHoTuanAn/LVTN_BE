<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get stats
        $stats = $this->getStats();
        
        // Get revenue data for chart (last 12 months)
        $revenueData = $this->getRevenueData();
        
        // Get user activity data (last 7 days)
        $activityData = $this->getUserActivityData();
        
        return view('dashboard', compact('stats', 'revenueData', 'activityData'));
    }
    
    private function getStats()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Total Users
        $totalUsers = User::count();
        $totalUsersLastMonth = User::where('created_at', '<', $lastMonth)->count();
        $usersChange = $totalUsersLastMonth > 0 
            ? round((($totalUsers - $totalUsersLastMonth) / $totalUsersLastMonth) * 100, 1)
            : 0;
        
        // Active Users (users who made bookings in last 30 days)
        $activeUsers = User::whereHas('bookings', function($query) use ($now) {
            $query->where('created_at', '>=', $now->copy()->subDays(30));
        })->count();
        
        $activeUsersLastPeriod = User::whereHas('bookings', function($query) use ($lastMonth) {
            $query->whereBetween('created_at', [
                $lastMonth->copy()->subDays(30),
                $lastMonth
            ]);
        })->count();
        
        $activeUsersChange = $activeUsersLastPeriod > 0
            ? round((($activeUsers - $activeUsersLastPeriod) / $activeUsersLastPeriod) * 100, 1)
            : 0;
        
        // Total Bookings
        $totalBookings = Booking::count();
        $totalBookingsLastMonth = Booking::where('created_at', '<', $lastMonth)->count();
        $bookingsChange = $totalBookingsLastMonth > 0
            ? round((($totalBookings - $totalBookingsLastMonth) / $totalBookingsLastMonth) * 100, 1)
            : 0;
        
        // Total Revenue
        $totalRevenue = Booking::where('is_paid', true)
            ->sum('total_price');
        
        $totalRevenueLastMonth = Booking::where('is_paid', true)
            ->where('created_at', '<', $lastMonth)
            ->sum('total_price');
        
        $revenueChange = $totalRevenueLastMonth > 0
            ? round((($totalRevenue - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100, 1)
            : 0;
        
        return [
            'total_users' => [
                'value' => number_format($totalUsers),
                'change' => ($usersChange >= 0 ? '+' : '') . $usersChange . '%'
            ],
            'active_users' => [
                'value' => number_format($activeUsers),
                'change' => ($activeUsersChange >= 0 ? '+' : '') . $activeUsersChange . '%'
            ],
            'total_bookings' => [
                'value' => number_format($totalBookings),
                'change' => ($bookingsChange >= 0 ? '+' : '') . $bookingsChange . '%'
            ],
            'revenue' => [
                'value' => '$' . number_format($totalRevenue, 0),
                'change' => ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%'
            ]
        ];
    }
    
    private function getRevenueData()
    {
        $months = [];
        $revenues = [];
        
        // Get revenue for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $revenue = Booking::where('is_paid', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_price');
            
            $months[] = $date->format('M');
            $revenues[] = round($revenue, 2);
        }
        
        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }
    
    private function getUserActivityData()
    {
        $dates = [];
        $logins = [];
        $transactions = [];
        $apiCalls = [];
        
        // Get data for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            
            $dates[] = $dateKey;
            
            // Count unique users who made bookings (as proxy for logins)
            $loginCount = Booking::whereDate('created_at', $dateKey)
                ->distinct('user_id')
                ->count('user_id');
            $logins[] = $loginCount;
            
            // Count transactions (completed bookings)
            $transactionCount = Booking::whereDate('created_at', $dateKey)
                ->where('is_paid', true)
                ->count();
            $transactions[] = $transactionCount;
            
            // Count reviews as proxy for API calls
            $reviewCount = Review::whereDate('created_at', $dateKey)->count();
            $apiCalls[] = $reviewCount * 5; // Multiply for visual effect
        }
        
        return [
            'labels' => $dates,
            'datasets' => [
                'logins' => $logins,
                'transactions' => $transactions,
                'api_calls' => $apiCalls
            ]
        ];
    }
}
