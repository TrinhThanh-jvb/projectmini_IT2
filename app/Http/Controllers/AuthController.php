<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

/**
 * Controller AuthController: Quản lý toàn bộ quy trình xác thực người dùng và hồ sơ cá nhân.
 * 
 * Các chức năng chính:
 * - Hiển thị form đăng nhập và xử lý đăng nhập (Login)
 * - Đăng xuất và hủy session (Logout)
 * - Xem và cập nhật hồ sơ cá nhân (Profile update) bao gồm đổi mật khẩu & đổi avatar
 */
class AuthController extends Controller
{
    /**
     * Hiển thị giao diện màn hình đăng nhập.
     * 
     * Nếu người dùng đã đăng nhập trước đó, sẽ tự động chuyển hướng vào Dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLogin()
    {
        // Kiểm tra phiên đăng nhập đã tồn tại chưa
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Xử lý xác thực tài khoản và đăng nhập vào hệ thống.
     *
     * @param  \Illuminate\Http\Request  $request Chứa username, password và remember token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ của dữ liệu đầu vào (Validation)
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        // 2. Tìm kiếm nhân viên trong database theo username
        $employee = Employee::where('username', $credentials['username'])->first();

        // 3. So khớp mật khẩu: Kiểm tra sự tồn tại của tài khoản và hash mật khẩu
        if (!$employee || !Hash::check($credentials['password'], $employee->password)) {
            return back()->withErrors([
                'username' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
            ])->onlyInput('username');
        }

        // 4. Kiểm tra trạng thái tài khoản: Nếu đã bị khóa (inactive) thì không cho phép đăng nhập
        if ($employee->status !== 'active') {
            return back()->withErrors([
                'username' => 'Tài khoản này hiện đang bị khóa hoặc ngưng hoạt động.',
            ])->onlyInput('username');
        }

        // 5. Tiến hành đăng nhập vào Auth guard với tùy chọn "Ghi nhớ đăng nhập"
        Auth::login($employee, $request->boolean('remember'));

        // Tái tạo ID session để phòng chống tấn công Session Fixation
        $request->session()->regenerate();

        // Chuyển hướng tới trang người dùng định truy cập trước đó (hoặc mặc định về Dashboard)
        return redirect()->intended(route('dashboard'))
            ->with('success', 'Đăng nhập thành công! Chào mừng ' . ($employee->profile->name ?? $employee->username));
    }

    /**
     * Đăng xuất khỏi hệ thống, vô hiệu hóa session và sinh CSRF token mới.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Xóa thông tin xác thực hiện tại khỏi Guard
        Auth::logout();

        // Hủy toàn bộ dữ liệu session hiện hành
        $request->session()->invalidate();

        // Tạo lại CSRF token mới để phòng chống tấn công CSRF
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất an toàn.');
    }

    /**
     * Hiển thị trang hồ sơ cá nhân của nhân viên đang đăng nhập.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        // Lấy thông tin user hiện tại kèm theo thông tin hồ sơ (Eager loading quan hệ profile)
        $employee = Auth::user()->load('profile');
        return view('auth.profile', compact('employee'));
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân (Tên, Email, SĐT, Địa chỉ, Avatar, Đổi mật khẩu).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $employee = Auth::user();
        $profile = $employee->profile;

        // 1. Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Email là duy nhất trong bảng employee_profiles, ngoại trừ id hiện tại
            'email' => 'required|email|max:255|unique:employee_profiles,email,' . ($profile->id ?? 0),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password' => 'nullable|string|min:6|confirmed', // Mật khẩu mới nếu có, yêu cầu trường password_confirmation
        ]);

        // 2. Xử lý upload ảnh đại diện nếu người dùng tải file mới
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // 3. Cập nhật bảng employee_profiles
        if ($profile) {
            $profile->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar' => $validated['avatar'] ?? $profile->avatar,
            ]);
        }

        // 4. Nếu người dùng nhập mật khẩu mới, cập nhật mật khẩu mã hóa trong bảng employees
        if (!empty($validated['password'])) {
            $employee->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }
}
