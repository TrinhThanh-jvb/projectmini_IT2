<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Order: Đại diện cho một đơn đặt hàng của khách hàng.
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'orders';

    /**
     * Các trường được phép gán giá trị hàng loạt (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',    // Khóa ngoại trỏ đến nhân viên phụ trách tạo đơn hàng
        'customer_name',  // Tên khách hàng đặt mua
        'customer_phone', // Số điện thoại liên lạc của khách hàng
        'total_amount',   // Tổng giá trị bằng tiền của toàn bộ đơn hàng
        'status',         // Trạng thái đơn hàng: 'Pending', 'Processing', 'Completed', 'Cancelled'
    ];

    /**
     * Mối quan hệ nhiều-1 (BelongsTo):
     * Đơn hàng được tạo bởi một nhân viên bán hàng (Employee).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Mối quan hệ 1-nhiều (One-to-Many):
     * Một đơn hàng chứa nhiều dòng chi tiết sản phẩm đã mua (OrderItem).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Mối quan hệ nhiều-nhiều (Many-to-Many):
     * Một đơn hàng bao gồm nhiều sản phẩm (Product) thông qua bảng trung gian order_items.
     * Lấy kèm thông tin số lượng (quantity) và giá bán lúc đặt (price).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
