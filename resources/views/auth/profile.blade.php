{{-- 
    View: Quản lý hồ sơ cá nhân (Profile Page).
    Cho phép nhân viên đang đăng nhập xem thông tin tài khoản cố định và cập nhật
    thông tin liên hệ, ảnh đại diện, cũng như thay đổi mật khẩu đăng nhập.
--}}
@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')
@section('page_title', 'Hồ sơ cá nhân')

@section('content')
<div class="panel">
    {{-- Thanh tiêu đề khung kèm huy hiệu vai trò của nhân viên --}}
    <div class="panel-header">
        <h2 class="panel-title">Thông tin tài khoản & Hồ sơ nhân viên</h2>
        <span class="badge {{ $employee->isManager() ? 'badge-info' : 'badge-success' }}">
            {{ $employee->isManager() ? 'Vai trò: Quản lý' : 'Vai trò: Nhân viên bán hàng' }}
        </span>
    </div>

    <div style="padding: 24px;">
        {{-- Form cập nhật hồ sơ với method PUT và enctype multipart để hỗ trợ upload ảnh --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                {{-- 1. Thông tin tài khoản hệ thống (Chỉ đọc - Read only) --}}
                <div class="form-group">
                    <label class="form-label">Tên đăng nhập (Username)</label>
                    <input type="text" class="form-control" value="{{ $employee->username }}" disabled style="background: #f1f5f9;">
                    <small style="color: var(--text-muted); font-size: 12px;">Username cố định của hệ thống.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái tài khoản</label>
                    <input type="text" class="form-control" value="{{ $employee->status === 'active' ? 'Đang hoạt động' : 'Đã khóa' }}" disabled style="background: #f1f5f9;">
                </div>

                {{-- 2. Thông tin hồ sơ cá nhân (Cho phép chỉnh sửa) --}}
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $employee->profile->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Địa chỉ Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $employee->profile->email ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $employee->profile->phone ?? '') }}">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $employee->profile->address ?? '') }}">
                </div>
            </div>

            {{-- Khu vực tải lên và xem trước ảnh đại diện --}}
            <div class="form-group" style="margin-top: 10px;">
                <label for="avatar" class="form-label">Ảnh đại diện (Avatar)</label>
                <div style="display: flex; align-items: center; gap: 16px;">
                    @if($employee->profile && $employee->profile->avatar)
                        <img src="{{ asset('storage/' . $employee->profile->avatar) }}" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                    @endif
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                </div>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--border-color);">

            {{-- 3. Khu vực đổi mật khẩu mới (Nếu không nhập thì giữ nguyên mật khẩu cũ) --}}
            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px;">Đổi mật khẩu (Bỏ trống nếu không đổi)</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự">
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>

            {{-- Nút lưu các thay đổi --}}
            <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <span>💾 Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
