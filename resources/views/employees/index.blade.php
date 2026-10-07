@extends('layouts.app')

@section('title', 'Quản lý nhân viên')
@section('page_title', 'Quản lý tài khoản & Nhân viên')

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Danh sách nhân viên ({{ $employees->total() }})</h2>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <span>➕ Thêm nhân viên mới</span>
        </a>
    </div>

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
                @forelse($employees as $emp)
                    <tr>
                        <td>
                            @if($emp->profile && $emp->profile->avatar)
                                <img src="{{ asset('storage/' . $emp->profile->avatar) }}" alt="Avatar" class="product-thumb" style="border-radius: 50%;">
                            @else
                                <div class="empty-thumb" style="border-radius: 50%; font-size: 13px; font-weight: 700;">
                                    {{ strtoupper(substr($emp->username, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: 700;">
                            {{ $emp->profile->name ?? 'Chưa cập nhật' }}
                        </td>
                        <td><code>{{ $emp->username }}</code></td>
                        <td>{{ $emp->profile->email ?? 'N/A' }}</td>
                        <td>{{ $emp->profile->phone ?? 'N/A' }}</td>
                        <td>
                            @if($emp->isManager())
                                <span class="badge badge-info">Quản lý (Manager)</span>
                            @else
                                <span class="badge badge-secondary">Nhân viên bán hàng</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->status === 'active')
                                <span class="badge badge-success">Hoạt động</span>
                            @else
                                <span class="badge badge-danger">Đã khóa</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--primary);">
                            {{ $emp->orders_count }} đơn
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-secondary btn-sm" title="Chỉnh sửa">
                                    ✏️
                                </a>
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

    @if($employees->hasPages())
        <div style="padding: 20px; display: flex; justify-content: flex-end;">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection
