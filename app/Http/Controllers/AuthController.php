<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $employee = Employee::where('username', $credentials['username'])->first();

        if (!$employee || !Hash::check($credentials['password'], $employee->password)) {
            return back()->withErrors([
                'username' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
            ])->onlyInput('username');
        }

        if ($employee->status !== 'active') {
            return back()->withErrors([
                'username' => 'Tài khoản này hiện đang bị khóa hoặc ngưng hoạt động.',
            ])->onlyInput('username');
        }

        Auth::login($employee, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Đăng nhập thành công! Chào mừng ' . ($employee->profile->name ?? $employee->username));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất an toàn.');
    }

    public function profile()
    {
        $employee = Auth::user()->load('profile');
        return view('auth.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $employee = Auth::user();
        $profile = $employee->profile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employee_profiles,email,' . ($profile->id ?? 0),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if ($profile) {
            $profile->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar' => $validated['avatar'] ?? $profile->avatar,
            ]);
        }

        if (!empty($validated['password'])) {
            $employee->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return back()->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }
}
