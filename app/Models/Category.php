<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Category: Đại diện cho danh mục sản phẩm (ví dụ: Điện thoại, Máy tính, Phụ kiện...).
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'categories';

    /**
     * Các trường được phép gán hàng loạt (Mass Assignment).
     * Bắt buộc phải có 'name' để Eloquent cho phép Category::create() và tránh lỗi:
     * SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'name' cannot be null.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Tên của danh mục sản phẩm (Đã khắc phục sau khi debug)
    ];

    /**
     * Mối quan hệ 1-Nhiều (One-to-Many):
     * Một danh mục có thể chứa nhiều sản phẩm (Product).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
