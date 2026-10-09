<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model Employee: Đại diện cho tài khoản nhân viên trong hệ thống.
 * 
 * Kế thừa từ Illuminate\Foundation\Auth\User để tích hợp sẵn với cơ chế xác thực (Authentication) của Laravel.
 */
class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'employees';

    /**
     * Các thuộc tính được phép gán giá trị hàng loạt (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'username', // Tên đăng nhập của nhân viên
        'password', // Mật khẩu tài khoản (đã được băm bcrypt/argon2)
        'role',     // Vai trò: 'manager' (Quản lý) hoặc 'sales_staff' (Nhân viên bán hàng)
        'status',   // Trạng thái tài khoản: 'active' (Hoạt động) hoặc 'inactive' (Khóa)
    ];

    /**
     * Các thuộc tính cần ẩn đi khi chuyển đổi model sang mảng hoặc JSON.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',       // Ẩn mật khẩu nhằm đảm bảo an toàn bảo mật
        'remember_token', // Token ghi nhớ đăng nhập
    ];

    /**
     * Ép kiểu tự động cho các thuộc tính (Attribute Casting).
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed', // Tự động mã hóa băm mật khẩu khi được gán giá trị
    ];

    /**
     * Mối quan hệ 1-1 (One-to-One): Một Employee có một hồ sơ cá nhân EmployeeProfile.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class, 'employee_id');
    }

    /**
     * Mối quan hệ 1-Nhiều (One-to-Many): Một Employee có thể tạo nhiều đơn hàng Order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id');
    }

    /**
     * Kiểm tra xem nhân viên này có phải là Quản lý hay không.
     * 
     * @return bool True nếu là 'manager', ngược lại False
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Kiểm tra xem nhân viên này có phải là Nhân viên bán hàng hay không.
     * 
     * @return bool True nếu là 'sales_staff', ngược lại False
     */
    public function isSalesStaff(): bool
    {
        return $this->role === 'sales_staff';
    }
}
