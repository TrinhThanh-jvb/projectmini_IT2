<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng employees (Tài khoản nhân viên).
 * 
 * Bảng này lưu trữ thông tin đăng nhập, phân quyền vai trò (Role-Based Access Control)
 * và trạng thái hoạt động của nhân viên.
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng employees.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();                                    // Khóa chính tự tăng (BIGINT UNSIGNED)
            $table->string('username')->unique();            // Tên đăng nhập là duy nhất
            $table->string('password');                      // Mật khẩu đã mã hóa băm (Hash)
            $table->string('role')->default('sales_staff');  // Vai trò: 'manager' (Quản lý) | 'sales_staff' (Nhân viên bán hàng)
            $table->string('status')->default('active');     // Trạng thái: 'active' (Hoạt động) | 'inactive' (Tạm khóa)
            $table->rememberToken();                         // Chuỗi token phục vụ tính năng "Ghi nhớ đăng nhập" (Remember me)
            $table->timestamps();                            // Tự động tạo 2 cột created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng employees).
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
