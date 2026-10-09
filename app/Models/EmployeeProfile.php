<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model EmployeeProfile: Chứa thông tin hồ sơ chi tiết của nhân viên.
 * 
 * Bảng này tách biệt với bảng employees để chuẩn hóa dữ liệu tài khoản và thông tin cá nhân.
 */
class EmployeeProfile extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'employee_profiles';

    /**
     * Các thuộc tính được phép gán giá trị hàng loạt (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id', // Khóa ngoại liên kết tới bảng employees
        'name',        // Họ và tên đầy đủ của nhân viên
        'email',       // Địa chỉ email liên hệ
        'phone',       // Số điện thoại liên hệ
        'address',     // Địa chỉ nơi ở/làm việc
        'avatar',      // Đường dẫn file ảnh đại diện được lưu trữ
    ];

    /**
     * Mối quan hệ đảo ngược 1-1 (Inverse One-to-One):
     * Hồ sơ này thuộc về một tài khoản Employee cụ thể.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
