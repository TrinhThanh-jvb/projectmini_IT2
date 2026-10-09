<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Employee;

/**
 * Controller DashboardController: Tổng hợp dữ liệu thống kê báo cáo cho màn hình chính (Dashboard).
 * 
 * Logic phân quyền hiển thị dữ liệu:
 * - Quản lý (Manager): Thấy số liệu thống kê của toàn bộ hệ thống (tất cả đơn hàng, toàn bộ doanh thu, nhân viên).
 * - Nhân viên bán hàng (Sales Staff): Chỉ thấy số liệu thống kê về đơn hàng và doanh thu do chính mình tạo ra.
 */
class DashboardController extends Controller
{
    /**
     * Thu thập các chỉ số KPI và danh sách đơn hàng gần đây để hiển thị lên Dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Thống kê chung về kho hàng (Số lượng sản phẩm và danh mục)
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        // 2. Phân vùng dữ liệu (Scope) theo vai trò người dùng
        if ($user->isManager()) {
            // Dành cho Quản lý: Đếm tổng số đơn toàn hệ thống
            $totalOrders = Order::count();
            // Tổng doanh thu từ các đơn hàng có trạng thái "Completed" trên toàn bộ cửa hàng
            $totalRevenue = Order::where('status', 'Completed')->sum('total_amount');
            // 5 đơn hàng mới nhất trên toàn hệ thống (kèm thông tin nhân viên và sản phẩm)
            $recentOrders = Order::with(['employee.profile', 'orderItems.product'])
                ->latest()
                ->take(5)
                ->get();
            // Khởi tạo query gốc để tính số lượng theo từng trạng thái
            $ordersQuery = Order::query();
        } else {
            // Dành cho Nhân viên bán hàng: Chỉ đếm các đơn do chính nhân viên này tạo
            $totalOrders = Order::where('employee_id', $user->id)->count();
            // Doanh thu chỉ tính trên các đơn hoàn thành của nhân viên này
            $totalRevenue = Order::where('employee_id', $user->id)
                ->where('status', 'Completed')
                ->sum('total_amount');
            // 5 đơn hàng mới nhất của chính nhân viên này
            $recentOrders = Order::with(['employee.profile', 'orderItems.product'])
                ->where('employee_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            // Query gốc chỉ lọc theo employee_id của user hiện tại
            $ordersQuery = Order::where('employee_id', $user->id);
        }

        // 3. Đếm số lượng đơn hàng theo từng trạng thái xử lý (Pending, Processing, Completed, Cancelled)
        // Dùng clone để tái sử dụng query điều kiện mà không làm thay đổi truy vấn ban đầu
        $pendingCount = (clone $ordersQuery)->where('status', 'Pending')->count();
        $processingCount = (clone $ordersQuery)->where('status', 'Processing')->count();
        $completedCount = (clone $ordersQuery)->where('status', 'Completed')->count();
        $cancelledCount = (clone $ordersQuery)->where('status', 'Cancelled')->count();

        // 4. Tổng số lượng nhân viên trong hệ thống
        $totalEmployees = Employee::count();

        // Truyền các biến dữ liệu sang View dashboard.index để hiển thị giao diện
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
