<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng order_items (Chi tiết từng món hàng trong đơn hàng).
 * 
 * Bảng trung gian thể hiện mối quan hệ nhiều-nhiều (N-N) giữa orders và products.
 * Chứa thông tin số lượng (quantity) và đơn giá chốt tại thời điểm mua hàng (price).
 */
return new class extends Migration
{
    /**
     * Chạy migration để tạo bảng order_items.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();                                                         // Khóa chính bản ghi dòng chi tiết
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');       // Khóa ngoại trỏ đến đơn hàng cha, xóa đơn sẽ tự xóa các dòng chi tiết
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');   // Khóa ngoại trỏ đến sản phẩm được mua
            $table->integer('quantity')->default(1);                              // Số lượng sản phẩm mua
            $table->decimal('price', 15, 2);                                      // Đơn giá snapshot tại thời điểm tạo đơn
            $table->timestamps();                                                 // created_at và updated_at
        });
    }

    /**
     * Đảo ngược migration (Hủy và xóa bảng order_items).
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
