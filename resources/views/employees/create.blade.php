@extends('layouts.app')

@section('title', 'Thêm nhân viên mới')
@section('page_title', 'Thêm nhân viên mới')

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Tạo tài khoản và thông tin nhân viên</h2>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
    </div>

    <div style="padding: 24px;">
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--primary);">1. Thông tin tài khoản</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="form-label">Tên đăng nhập (Username) <span style="color: red;">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="VD: sales_nam..." required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu khởi tạo <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Vai trò (Role) <span style="color: red;">*</span></label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="sales_staff" {{ old('role') === 'sales_staff' ? 'selected' : '' }}>Nhân viên bán hàng (Sales Staff)</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Quản lý (Manager)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái <span style="color: red;">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Hoạt động (Active)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Khóa (Inactive)</option>
                    </select>
                </div>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--border-color);">

            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--primary);">2. Thông tin hồ sơ (Profile)</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên nhân viên <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="VD: Nguyễn Văn B" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Địa chỉ Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="VD: vanb@minisales.vn" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="VD: 0988776655">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" placeholder="VD: Cầu Giấy, Hà Nội">
                </div>
            </div>

            <div class="form-group">
                <label for="avatar" class="form-label">Ảnh đại diện (Avatar)</label>
                <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾 Tạo nhân viên</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
