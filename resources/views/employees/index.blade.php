{{-- 
    View: Danh sách nhân viên (Employees Index).
    Chỉ dành riêng cho tài khoản Quản lý (Manager).
    Hiển thị thông tin các tài khoản nhân viên, phân quyền vai trò, trạng thái,
    số lượng đơn hàng đã chốt và các nút thao tác Sửa/Xóa.
--}}
@extends('layouts.app')

@section('title', 'Quản lý nhân viên')
@section('page_title', 'Quản lý tài khoản & Nhân viên')

@section('content')
<div class="panel">
    {{-- Header bảng: Tiêu đề tổng số nhân viên và nút dẫn sang trang Thêm mới --}}
    <div class="panel-header">
        <h2 class="panel-title">Danh sách nhân viên ({{ $employees->total() }})</h2>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <span>➕ Thêm nhân viên mới</span>
        </a>
    </div>

    {{-- Bảng hiển thị danh sách dữ liệu nhân viên --}}
    <div class="table-responsive">
        <table class="app-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Avatar</th>
                    <th>Họ và tên</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Tổng đơn</th>
                    <th style="text-align: right; width: 140px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                {{-- Lặp qua từng nhân viên đã được phân trang --}}
                @forelse($employees as $emp)
                    <tr>
                        {{-- Cột ảnh đại diện --}}
                        <td>
                            @if($emp->profile && $emp->profile->avatar)
                                <img src="{{ asset('storage/' . $emp->profile->avatar) }}" alt="Avatar" class="product-thumb" style="border-radius: 50%;">
                            @else
                                <div class="empty-thumb" style="border-radius: 50%; font-size: 13px; font-weight: 700;">
                                    {{ strtoupper(substr($emp->username, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        {{-- Tên nhân viên --}}
                        <td style="font-weight: 700;">
                            {{ $emp->profile->name ?? 'Chưa cập nhật' }}
                        </td>
                        {{-- Tên đăng nhập --}}
                        <td><code>{{ $emp->username }}</code></td>
                        {{-- Email và SĐT --}}
                        <td>{{ $emp->profile->email ?? 'N/A' }}</td>
                        <td>{{ $emp->profile->phone ?? 'N/A' }}</td>
                        {{-- Huy hiệu vai trò --}}
                        <td>
                            @if($emp->isManager())
                                <span class="badge badge-info">Quản lý (Manager)</span>
                            @else
                                <span class="badge badge-secondary">Nhân viên bán hàng</span>
                            @endif
                        </td>
                        {{-- Trạng thái hoạt động --}}
                        <td>
                            @if($emp->status === 'active')
                                <span class="badge badge-success">Hoạt động</span>
                            @else
                                <span class="badge badge-danger">Đã khóa</span>
                            @endif
                        </td>
                        {{-- Số lượng đơn hàng phụ trách --}}
                        <td style="font-weight: 700; color: var(--primary);">
                            {{ $emp->orders_count }} đơn
                        </td>
                        {{-- Nút thao tác --}}
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                {{-- Nút chuyển tới form chỉnh sửa --}}
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-secondary btn-sm" title="Chỉnh sửa">
                                    ✏️
                                </a>
                                {{-- Nút xóa (Ẩn đi đối với tài khoản đang đăng nhập để tránh tự xóa chính mình) --}}
                                @if($emp->id !== auth()->id())
                                    <form action="{{ route('employees.destroy', $emp) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên: {{ $emp->username }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Chưa có dữ liệu nhân viên.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang (Pagination) --}}
    @if($employees->hasPages())
        <div style="padding: 20px; display: flex; justify-content: flex-end;">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection
