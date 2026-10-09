<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes - Định tuyến ứng dụng Web Mini Sales Management
|--------------------------------------------------------------------------
| File này định nghĩa toàn bộ đường dẫn (URL endpoints), các bộ lọc (middleware)
| và bộ điều khiển (Controllers) phụ trách xử lý tương ứng cho hệ thống.
|
*/

// =========================================================================
// 1. NHÓM ĐỊNH TUYẾN DÀNH CHO KHÁCH (GUEST)
// Người dùng chưa đăng nhập mới có thể truy cập các trang này.
// Nếu đã đăng nhập, middleware 'guest' sẽ tự động chuyển hướng về trang chủ.
// =========================================================================
Route::middleware('guest')->group(function () {
    // Hiển thị giao diện form đăng nhập (GET)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // Xử lý thông tin gửi lên từ form đăng nhập (POST)
    Route::post('/login', [AuthController::class, 'login']);
});

// =========================================================================
// 2. NHÓM ĐỊNH TUYẾN YÊU CẦU ĐÃ ĐĂNG NHẬP (AUTHENTICATED)
// Tất cả các route bên trong nhóm này bắt buộc phải qua middleware 'auth'.
// Nếu chưa đăng nhập, hệ thống tự động redirect về trang 'login'.
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // Đăng xuất khỏi hệ thống và hủy phiên làm việc (POST)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Màn hình bảng điều khiển trung tâm (Dashboard thống kê)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // --- Quản lý thông tin cá nhân (Profile) ---
    // Xem trang hồ sơ cá nhân của nhân viên đang đăng nhập
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    // Cập nhật thông tin cá nhân (Tên, email, số điện thoại, ảnh đại diện, đổi mật khẩu)
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // --- Quản lý sản phẩm (Products Management) ---
    // Sử dụng Resource Controller tự động sinh các route CRUD:
    // index (GET), create (GET), store (POST), show (GET), edit (GET), update (PUT/PATCH), destroy (DELETE)
    Route::resource('products', ProductController::class);

    // --- Quản lý danh mục (Categories Management) ---
    // Loại trừ 'create', 'show', 'edit' vì thêm/sửa danh mục được thực hiện ngay trong trang danh sách (Modal/Form inline)
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // --- Quản lý đơn hàng (Orders Management) ---
    // Đơn hàng được tạo mới (create, store), xem danh sách (index), xem chi tiết (show), hủy/xóa (destroy)
    // Ngoại trừ edit & update tổng thể để tránh sửa đổi dữ liệu đơn hàng tùy tiện sau khi chốt
    Route::resource('orders', OrderController::class)->except(['edit', 'update']);
    
    // Route cập nhật nhanh trạng thái đơn hàng (Pending -> Processing -> Completed -> Cancelled)
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // =====================================================================
    // 3. NHÓM ĐỊNH TUYẾN DÀNH RIÊNG CHO QUẢN LÝ (MANAGER ONLY)
    // Sử dụng middleware custom 'manager' (CheckManager) để kiểm tra role.
    // Chỉ tài khoản có role === 'manager' mới có thể truy cập Quản lý nhân viên.
    // =====================================================================
    Route::middleware('manager')->group(function () {
        // Quản lý tài khoản và hồ sơ nhân viên (CRUD: index, create, store, edit, update, destroy)
        // Ngoại trừ show (thông tin xem trong bảng hoặc trang profile)
        Route::resource('employees', EmployeeController::class)->except(['show']);
    });
});
