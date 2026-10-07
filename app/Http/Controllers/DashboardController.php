<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalProducts = Product::count();
        $totalCategories = Category::count();

        // Scope orders by role
        if ($user->isManager()) {
            $totalOrders = Order::count();
            $totalRevenue = Order::where('status', 'Completed')->sum('total_amount');
            $recentOrders = Order::with(['employee.profile', 'orderItems.product'])
                ->latest()
                ->take(5)
                ->get();
            $ordersQuery = Order::query();
        } else {
            $totalOrders = Order::where('employee_id', $user->id)->count();
            $totalRevenue = Order::where('employee_id', $user->id)
                ->where('status', 'Completed')
                ->sum('total_amount');
            $recentOrders = Order::with(['employee.profile', 'orderItems.product'])
                ->where('employee_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            $ordersQuery = Order::where('employee_id', $user->id);
        }

        $pendingCount = (clone $ordersQuery)->where('status', 'Pending')->count();
        $processingCount = (clone $ordersQuery)->where('status', 'Processing')->count();
        $completedCount = (clone $ordersQuery)->where('status', 'Completed')->count();
        $cancelledCount = (clone $ordersQuery)->where('status', 'Cancelled')->count();

        $totalEmployees = Employee::count();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'totalRevenue',
            'totalEmployees',
            'recentOrders',
            'pendingCount',
            'processingCount',
            'completedCount',
            'cancelledCount'
        ));
    }
}
