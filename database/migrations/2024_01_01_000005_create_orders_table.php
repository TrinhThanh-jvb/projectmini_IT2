<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng orders (Đơn đặt hàng).
 * 
 * Lưu trữ thông tin đơn hàng, khách hàng, tổng tiền và nhân viên tạo đơn.
 * Khóa ngoại employee_id liên kết với bảng employees.
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng orders.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();                                                             // Khóa chính mã đơn hàng
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); // Khóa ngoại trỏ đến nhân viên phụ trách tạo đơn
            $table->string('customer_name');                                          // Họ tên khách hàng mua hàng
            $table->string('customer_phone');                                         // Số điện thoại khách hàng
            $table->decimal('total_amount', 15, 2)->default(0);                       // Tổng số tiền cần thanh toán của đơn hàng
            $table->string('status')->default('Pending');                             // Trạng thái: 'Pending' | 'Processing' | 'Completed' | 'Cancelled'
            $table->timestamps();                                                     // created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng orders).
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
