<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeProfile;

/**
 * Controller EmployeeController: Quản lý danh sách và tài khoản nhân viên (Dành riêng cho Quản lý).
 * 
 * Các chức năng chính:
 * - Xem danh sách nhân viên kèm phân trang, thông tin hồ sơ và số lượng đơn hàng đã xử lý
 * - Tạo mới tài khoản nhân viên đồng thời tạo hồ sơ (sử dụng Database Transaction)
 * - Chỉnh sửa thông tin nhân viên, đổi vai trò (role), trạng thái (active/inactive), reset mật khẩu
 * - Xóa tài khoản nhân viên (ngăn chặn hành vi tự xóa chính mình)
 */
class EmployeeController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên trong hệ thống (có phân trang 10 dòng/trang).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Tải kèm quan hệ profile (eager loading) và đếm tổng số đơn hàng đã tạo (withCount)
        $employees = Employee::with('profile')
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Hiển thị form tạo mới tài khoản nhân viên.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Lưu thông tin tài khoản và hồ sơ nhân viên mới vào cơ sở dữ liệu.
     * Sử dụng Transaction để đảm bảo tính toàn vẹn giữa bảng employees và employee_profiles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào
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

        // 2. Bắt đầu Database Transaction
        DB::beginTransaction();
        try {
            // Bước 2.1: Tạo bản ghi tài khoản đăng nhập (Employee)
            $employee = Employee::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']), // Băm mật khẩu an toàn
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);

            // Bước 2.2: Xử lý lưu ảnh đại diện nếu có
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // Bước 2.3: Tạo bản ghi hồ sơ cá nhân (EmployeeProfile) liên kết với employee_id vừa tạo
            EmployeeProfile::create([
                'employee_id' => $employee->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar' => $avatarPath,
            ]);

            // Xác nhận lưu dữ liệu thành công
            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Thêm nhân viên mới thành công!');
        } catch (\Exception $e) {
            // Rollback hủy bỏ mọi thao tác nếu có lỗi phát sinh
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị giao diện chỉnh sửa thông tin nhân viên.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\View\View
     */
    public function edit(Employee $employee)
    {
        $employee->load('profile');
        return view('employees.edit', compact('employee'));
    }

    /**
     * Cập nhật thông tin nhân viên và hồ sơ tương ứng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Employee $employee)
    {
        $profile = $employee->profile;

        // 1. Xác thực dữ liệu
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
            // Bước 2.1: Cập nhật thông tin bảng employees
            $updateData = [
                'role' => $validated['role'],
                'status' => $validated['status'],
            ];

            // Nếu người quản trị nhập mật khẩu mới thì mới tiến hành hash và đổi
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $employee->update($updateData);

            // Bước 2.2: Xử lý cập nhật ảnh đại diện nếu có tải file mới
            $avatarPath = $profile->avatar ?? null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // Bước 2.3: Cập nhật thông tin bảng employee_profiles
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

    /**
     * Xóa tài khoản nhân viên.
     * Chặn không cho người dùng đang đăng nhập tự xóa tài khoản của chính mình.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Employee $employee)
    {
        // Nghiệp vụ bảo vệ: Không cho phép tự xóa chính mình để tránh mất quyền quản trị đột ngột
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        // Thực hiện xóa (các bảng liên kết có cascade sẽ tự động được dọn dẹp)
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Xóa nhân viên thành công!');
    }
}
