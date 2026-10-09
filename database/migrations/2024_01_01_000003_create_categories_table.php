<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng categories (Danh mục ngành hàng / sản phẩm).
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng categories.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();             // Khóa chính tự tăng của danh mục
            $table->string('name');   // Tên danh mục (ví dụ: Điện thoại, Laptop...)
            $table->timestamps();     // created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng categories).
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
