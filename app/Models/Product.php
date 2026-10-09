<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Product: Đại diện cho sản phẩm hàng hóa được kinh doanh trong hệ thống.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'products';

    /**
     * Các thuộc tính được phép gán giá trị hàng loạt (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id', // Khóa ngoại tham chiếu đến bảng categories
        'name',        // Tên sản phẩm
        'price',       // Đơn giá bán của sản phẩm
        'description', // Mô tả chi tiết tính năng, thông số sản phẩm
        'image',       // Đường dẫn hình ảnh đại diện sản phẩm trong storage
        'status',      // Trạng thái kinh doanh: 'Đang bán' hoặc 'Ngừng bán'
    ];

    /**
     * Mối quan hệ nhiều-1 (BelongsTo):
     * Mỗi sản phẩm thuộc về duy nhất một danh mục (Category).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Mối quan hệ 1-nhiều (One-to-Many):
     * Một sản phẩm có thể xuất hiện trong nhiều chi tiết đơn hàng (OrderItem).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    /**
     * Mối quan hệ nhiều-nhiều (Many-to-Many):
     * Một sản phẩm có thể có mặt trong nhiều đơn hàng (Order) thông qua bảng trung gian order_items.
     * Kèm theo các trường phụ trên bảng trung gian: quantity (số lượng) và price (đơn giá tại thời điểm mua).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
