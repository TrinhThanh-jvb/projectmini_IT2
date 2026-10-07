<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class, 'employee_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id');
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSalesStaff(): bool
    {
        return $this->role === 'sales_staff';
    }
}
