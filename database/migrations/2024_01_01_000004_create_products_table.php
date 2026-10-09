<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng products (Sản phẩm kinh doanh).
 * 
 * Lưu trữ danh sách sản phẩm, giá bán, mô tả, hình ảnh và trạng thái bán.
 * Khóa ngoại category_id liên kết với bảng categories.
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng products.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();                                                             // Khóa chính sản phẩm
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Khóa ngoại trỏ đến categories
            $table->string('name');                                                   // Tên sản phẩm
            $table->decimal('price', 15, 2);                                          // Đơn giá sản phẩm (tối đa 15 chữ số, 2 số thập phân)
            $table->text('description')->nullable();                                  // Mô tả chi tiết sản phẩm
            $table->string('image')->nullable();                                      // Đường dẫn ảnh minh họa trong thư mục storage
            $table->string('status')->default('Đang bán');                            // Trạng thái kinh doanh: 'Đang bán' | 'Ngừng bán'
            $table->timestamps();                                                     // created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng products).
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
