<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware CheckManager: Bộ lọc kiểm tra quyền Quản trị viên (Manager).
 * 
 * Middleware này chịu trách nhiệm chặn các yêu cầu truy cập trái phép vào
 * những chức năng nhạy cảm như Quản lý nhân sự (Employees Management).
 */
class CheckManager
{
    /**
     * Xử lý request đi vào trước khi đến Controller.
     *
     * @param  \Illuminate\Http\Request  $request Đối tượng request HTTP hiện tại
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next Closure chuyển tiếp request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa (auth()->check())
        // 2. Kiểm tra vai trò của người dùng có phải là 'manager' hay không
        if (!auth()->check() || auth()->user()->role !== 'manager') {
            // Nếu không thỏa mãn, trả về mã lỗi HTTP 403 Forbidden kèm thông báo
            abort(403, 'Bạn không có quyền truy cập chức năng này (chỉ dành cho Quản lý).');
        }

        // Cho phép request tiếp tục đi tới Controller tiếp theo nếu thỏa mãn điều kiện
        return $next($request);
    }
}
