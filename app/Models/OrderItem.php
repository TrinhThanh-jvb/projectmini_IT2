<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model OrderItem: Đại diện cho từng mục hàng cụ thể trong đơn hàng.
 * 
 * Đóng vai trò là bảng trung gian liên kết giữa Order và Product (Many-to-Many).
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'order_items';

    /**
     * Các trường cho phép gán giá trị hàng loạt (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',   // Khóa ngoại tham chiếu đến đơn hàng cha (orders)
        'product_id', // Khóa ngoại tham chiếu đến sản phẩm được mua (products)
        'quantity',   // Số lượng sản phẩm khách đặt mua
        'price',      // Giá bán tại thời điểm tạo đơn (lưu snapshot giá để không bị ảnh hưởng nếu sản phẩm đổi giá sau này)
    ];

    /**
     * Mối quan hệ nhiều-1 (BelongsTo): Chi tiết này thuộc về một đơn hàng cụ thể.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Mối quan hệ nhiều-1 (BelongsTo): Chi tiết này tham chiếu tới một sản phẩm.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
