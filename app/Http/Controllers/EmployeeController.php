<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeProfile;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('profile')
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:employees,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:manager,sales_staff',
            'status' => 'required|in:active,inactive',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employee_profiles,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'name.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng.',
        ]);

        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);

            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            EmployeeProfile::create([
                'employee_id' => $employee->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar' => $avatarPath,
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Thêm nhân viên mới thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Employee $employee)
    {
        $employee->load('profile');
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $profile = $employee->profile;

        $validated = $request->validate([
            'role' => 'required|in:manager,sales_staff',
            'status' => 'required|in:active,inactive',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employee_profiles,email,' . ($profile->id ?? 0),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'role' => $validated['role'],
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $employee->update($updateData);

            $avatarPath = $profile->avatar ?? null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            if ($profile) {
                $profile->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'avatar' => $avatarPath,
                ]);
            }

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Cập nhật thông tin nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        // Không cho phép tự xóa chính mình
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Xóa nhân viên thành công!');
    }
}
