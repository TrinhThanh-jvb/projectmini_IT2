{{-- 
    View: Chỉnh sửa thông tin nhân viên (Employees Edit).
    Chỉ dành riêng cho Quản lý (Manager).
    Cho phép cập nhật vai trò, trạng thái tài khoản, đổi mật khẩu mới (nếu cần),
    cùng với thông tin hồ sơ liên lạc và ảnh đại diện.
--}}
@extends('layouts.app')

@section('title', 'Chỉnh sửa nhân viên')
@section('page_title', 'Chỉnh sửa nhân viên: ' . $employee->username)

@section('content')
<div class="panel">
    {{-- Header khung và nút quay lại danh sách --}}
    <div class="panel-header">
        <h2 class="panel-title">Cập nhật thông tin nhân viên</h2>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
    </div>

    <div style="padding: 24px;">
        {{-- Form cập nhật gửi request PUT tới EmployeeController::update --}}
        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ================= PHẦN 1: THÔNG TIN TÀI KHOẢN ================= --}}
            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--primary);">1. Thông tin tài khoản</h3>
            <div class="form-grid">
                {{-- Username: Cố định không cho sửa --}}
                <div class="form-group">
                    <label class="form-label">Tên đăng nhập (Username)</label>
                    <input type="text" class="form-control" value="{{ $employee->username }}" disabled style="background: #f1f5f9;">
                    <small style="color: var(--text-muted); font-size: 12px;">Không thể thay đổi username.</small>
                </div>

                {{-- Mật khẩu mới: Nếu không đổi thì bỏ trống --}}
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu mới (Bỏ trống nếu giữ nguyên)</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự">
                </div>

                {{-- Phân quyền vai trò (Role) --}}
                <div class="form-group">
                    <label for="role" class="form-label">Vai trò (Role) <span style="color: red;">*</span></label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="sales_staff" {{ old('role', $employee->role) === 'sales_staff' ? 'selected' : '' }}>Nhân viên bán hàng (Sales Staff)</option>
                        <option value="manager" {{ old('role', $employee->role) === 'manager' ? 'selected' : '' }}>Quản lý (Manager)</option>
                    </select>
                </div>

                {{-- Trạng thái tài khoản (Status) --}}
                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái <span style="color: red;">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Hoạt động (Active)</option>
                        <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Khóa (Inactive)</option>
                    </select>
                </div>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--border-color);">

            {{-- ================= PHẦN 2: THÔNG TIN HỒ SƠ (PROFILE) ================= --}}
            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--primary);">2. Thông tin hồ sơ (Profile)</h3>
            <div class="form-grid">
                {{-- Họ và tên --}}
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên nhân viên <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $employee->profile->name ?? '') }}" required>
                </div>

                {{-- Email liên hệ --}}
                <div class="form-group">
                    <label for="email" class="form-label">Địa chỉ Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $employee->profile->email ?? '') }}" required>
                </div>

                {{-- Số điện thoại --}}
                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $employee->profile->phone ?? '') }}">
                </div>

                {{-- Địa chỉ nơi ở --}}
                <div class="form-group">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $employee->profile->address ?? '') }}">
                </div>
            </div>

            {{-- Ảnh đại diện --}}
            <div class="form-group">
                <label for="avatar" class="form-label">Ảnh đại diện (Avatar)</label>
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                    @if($employee->profile && $employee->profile->avatar)
                        <img src="{{ asset('storage/' . $employee->profile->avatar) }}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                    @endif
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                </div>
            </div>

            {{-- Nút lưu các thay đổi --}}
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾 Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
