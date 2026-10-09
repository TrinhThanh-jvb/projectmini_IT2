<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng employee_profiles (Hồ sơ cá nhân của nhân viên).
 * 
 * Lưu trữ thông tin định danh cá nhân, thông tin liên lạc và hình ảnh đại diện.
 * Quan hệ 1 - 1 với bảng employees.
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng employee_profiles.
     */
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();                                                             // Khóa chính tự tăng
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); // Khóa ngoại trỏ đến bảng employees, xóa nhân viên sẽ tự động xóa hồ sơ
            $table->string('name');                                                   // Họ và tên nhân viên
            $table->string('email')->unique();                                        // Địa chỉ email duy nhất trong hệ thống
            $table->string('phone')->nullable();                                      // Số điện thoại liên lạc (cho phép null)
            $table->string('address')->nullable();                                    // Địa chỉ liên hệ (cho phép null)
            $table->string('avatar')->nullable();                                     // Đường dẫn file ảnh đại diện (cho phép null)
            $table->timestamps();                                                     // created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng employee_profiles).
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
