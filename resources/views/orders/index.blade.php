{{-- 
    View: Quản lý danh sách đơn hàng (Orders Index).
    Giao diện gồm:
    1. Bộ lọc và tìm kiếm theo tên khách hàng / số điện thoại và trạng thái đơn hàng (Pending, Processing, Completed, Cancelled)
    2. Nút tạo đơn hàng mới
    3. Bảng dữ liệu đơn hàng (Phân quyền: Manager thấy mọi đơn, Sales Staff chỉ thấy đơn của bản thân)
    4. Thao tác xem chi tiết hoặc xóa đơn
--}}
@extends('layouts.app')

@section('title', 'Quản lý đơn hàng')
@section('page_title', 'Danh sách đơn hàng')

@section('content')
{{-- ================= THANH TÌM KIẾM VÀ BỘ LỌC ĐƠN HÀNG ================= --}}
<form action="{{ route('orders.index') }}" method="GET" class="filter-bar">
    {{-- Ô tìm kiếm theo tên hoặc SĐT khách hàng --}}
    <div class="search-input">
        <input type="text" name="search" class="form-control" placeholder="🔍 Tìm theo tên khách hàng hoặc SĐT..." value="{{ request('search') }}">
    </div>

    {{-- Lọc theo trạng thái đơn hàng --}}
    <div class="select-control">
        <select name="status" class="form-control">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
            <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Đang xử lý (Processing)</option>
            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Hoàn thành (Completed)</option>
            <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
        </select>
    </div>

    {{-- Nút lọc dữ liệu --}}
    <button type="submit" class="btn btn-secondary">
        <span>Lọc</span>
    </button>

    {{-- Nút reset bộ lọc nếu có điều kiện tìm kiếm --}}
    @if(request()->anyFilled(['search', 'status']))
        <a href="{{ route('orders.index') }}" class="btn btn-secondary" style="color: var(--danger);">
            <span>Xóa lọc</span>
        </a>
    @endif

    {{-- Nút tạo đơn hàng mới --}}
    <div style="margin-left: auto;">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <span>➕ Tạo đơn hàng mới</span>
        </a>
    </div>
</form>

{{-- ================= BẢNG DANH SÁCH ĐƠN HÀNG ================= --}}
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            {{ auth()->user()->isManager() ? 'Tất cả đơn hàng của nhân viên' : 'Đơn hàng do bạn tạo' }} ({{ $orders->total() }})
        </h2>
    </div>

    <div class="table-responsive">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Nhân viên phụ trách</th>
                    <th>Số loại SP</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th style="text-align: right; width: 160px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                {{-- Lặp qua từng đơn hàng trong trang hiện tại --}}
                @forelse($orders as $order)
                    <tr>
                        {{-- Mã đơn định dạng 4 chữ số (ví dụ: #0001) --}}
                        <td><strong>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td style="font-weight: 600;">{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        {{-- Tên nhân viên phụ trách đơn --}}
                        <td>
                            <span class="badge badge-secondary">
                                {{ $order->employee->profile->name ?? $order->employee->username }}
                            </span>
                        </td>
                        {{-- Số loại sản phẩm có trong đơn hàng --}}
                        <td>{{ $order->orderItems->count() }} sản phẩm</td>
                        {{-- Tổng giá trị đơn hàng --}}
                        <td style="font-weight: 700; color: var(--primary);">
                            {{ number_format($order->total_amount) }} đ
                        </td>
                        {{-- Huy hiệu trạng thái --}}
                        <td>
                            @if($order->status === 'Pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status === 'Processing')
                                <span class="badge badge-info">Processing</span>
                            @elseif($order->status === 'Completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        {{-- Thời điểm tạo đơn --}}
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        {{-- Nút Xem chi tiết & Xóa --}}
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm" title="Xem chi tiết">
                                    👁️ Chi tiết
                                </a>
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #{{ $order->id }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Chưa có đơn hàng nào được ghi nhận.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang (Pagination) --}}
    @if($orders->hasPages())
        <div style="padding: 20px; display: flex; justify-content: flex-end;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
