{{-- 
    View: Bảng điều khiển trung tâm (Dashboard).
    Hiển thị các thẻ thống kê KPI (Sản phẩm, danh mục, đơn hàng, doanh thu),
    tổng kết trạng thái đơn hàng, sơ đồ luồng kiến trúc (System Flow, Deploy Flow)
    và danh sách các đơn đặt hàng mới nhất.
--}}
@extends('layouts.app')

@section('title', 'Bảng điều khiển')
@section('page_title', 'Bảng điều khiển hệ thống (Dashboard)')

@section('content')
{{-- ================= KHỐI THỐNG KÊ NHANH (STAT CARDS) ================= --}}
<div class="stat-grid">
    {{-- Thẻ 1: Tổng doanh thu từ các đơn hàng hoàn tất --}}
    <div class="stat-card">
        <div>
            <div class="stat-title">Doanh thu hoàn tất</div>
            <div class="stat-value" style="font-size: 22px; color: var(--success); font-weight: 800;">
                {{ number_format($totalRevenue) }} đ
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Đơn hàng Completed</div>
        </div>
        <div class="stat-icon-wrapper bg-green">💰</div>
    </div>

    {{-- Thẻ 2: Tổng số đơn hàng --}}
    <a href="{{ route('orders.index') }}" class="stat-card" style="text-decoration: none; color: inherit;">
        <div>
            <div class="stat-title">
                {{ auth()->user()->isManager() ? 'Tổng đơn toàn hệ thống' : 'Đơn hàng của tôi' }}
            </div>
            <div class="stat-value">{{ number_format($totalOrders) }}</div>
            <div style="font-size: 12px; color: var(--primary); margin-top: 4px; font-weight: 600;">Xem chi tiết ➔</div>
        </div>
        <div class="stat-icon-wrapper bg-orange">🛍️</div>
    </a>

    {{-- Thẻ 3: Tổng số lượng sản phẩm Dior --}}
    <a href="{{ route('products.index') }}" class="stat-card" style="text-decoration: none; color: inherit;">
        <div>
            <div class="stat-title">Sản phẩm Dior</div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div style="font-size: 12px; color: var(--primary); margin-top: 4px; font-weight: 600;">Quản lý kho hàng ➔</div>
        </div>
        <div class="stat-icon-wrapper bg-gold">💄</div>
    </a>

    {{-- Thẻ 4: Danh mục ngành hàng --}}
    <a href="{{ route('categories.index') }}" class="stat-card" style="text-decoration: none; color: inherit;">
        <div>
            <div class="stat-title">Danh mục ngành hàng</div>
            <div class="stat-value">{{ number_format($totalCategories) }}</div>
            <div style="font-size: 12px; color: var(--primary); margin-top: 4px; font-weight: 600;">Xem danh mục ➔</div>
        </div>
        <div class="stat-icon-wrapper bg-rose">🏷️</div>
    </a>
</div>

{{-- ================= TÓM TẮT TRẠNG THÁI ĐƠN HÀNG ================= --}}
<div class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Tình trạng xử lý đơn hàng</h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                Theo dõi tiến độ đơn hàng và xử lý vận đơn
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                <span>➕ Tạo đơn hàng mới</span>
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-secondary btn-sm">
                <span>💄 Thêm sản phẩm</span>
            </a>
        </div>
    </div>
    <div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        {{-- Số đơn Chờ xác nhận --}}
        <div style="background: var(--warning-light); padding: 18px; border-radius: var(--radius-md); border-left: 4px solid var(--warning);">
            <div style="font-size: 13px; color: #b45309; font-weight: 600;">Chờ xác nhận (Pending)</div>
            <div style="font-size: 26px; font-weight: 800; color: #92400e; margin-top: 4px;">{{ $pendingCount }}</div>
        </div>

        {{-- Số đơn Đang xử lý --}}
        <div style="background: var(--primary-light); padding: 18px; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
            <div style="font-size: 13px; color: #be123c; font-weight: 600;">Đang xử lý (Processing)</div>
            <div style="font-size: 26px; font-weight: 800; color: #9f1239; margin-top: 4px;">{{ $processingCount }}</div>
        </div>

        {{-- Số đơn Đã hoàn tất thành công --}}
        <div style="background: var(--success-light); padding: 18px; border-radius: var(--radius-md); border-left: 4px solid var(--success);">
            <div style="font-size: 13px; color: #047857; font-weight: 600;">Đã hoàn thành (Completed)</div>
            <div style="font-size: 26px; font-weight: 800; color: #065f46; margin-top: 4px;">{{ $completedCount }}</div>
        </div>

        {{-- Số đơn Đã hủy --}}
        <div style="background: var(--danger-light); padding: 18px; border-radius: var(--radius-md); border-left: 4px solid var(--danger);">
            <div style="font-size: 13px; color: #b91c1c; font-weight: 600;">Đã hủy (Cancelled)</div>
            <div style="font-size: 26px; font-weight: 800; color: #991b1b; margin-top: 4px;">{{ $cancelledCount }}</div>
        </div>
    </div>
</div>

{{-- ================= BẢNG DANH SÁCH ĐƠN HÀNG GẦN ĐÂY ================= --}}
<div class="panel">
    <div class="panel-header">
        <div>
            <h2 class="panel-title">Đơn hàng gần đây</h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                5 giao dịch đơn hàng mới nhất trong hệ thống
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Xem tất cả đơn hàng ➔</a>
    </div>
    <div class="table-responsive">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Người tạo</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th style="text-align: right;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                {{-- Lặp qua 5 đơn hàng mới nhất --}}
                @forelse($recentOrders as $order)
                    <tr>
                        <td><strong>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ $order->employee->profile->name ?? $order->employee->username }}
                            </span>
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            {{ number_format($order->total_amount) }} đ
                        </td>
                        <td>
                            @if($order->status === 'Pending')
                                <span class="badge badge-warning">Chờ xác nhận</span>
                            @elseif($order->status === 'Processing')
                                <span class="badge badge-info">Đang xử lý</span>
                            @elseif($order->status === 'Completed')
                                <span class="badge badge-success">Hoàn thành</span>
                            @else
                                <span class="badge badge-danger">Đã hủy</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm">Chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 32px;">
                            Chưa có đơn hàng nào được tạo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
