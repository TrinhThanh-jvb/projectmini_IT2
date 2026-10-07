@extends('layouts.app')

@section('title', 'Bảng điều khiển')
@section('page_title', 'Bảng điều khiển hệ thống (Dashboard)')

@section('content')
<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div>
            <div class="stat-title">Tổng sản phẩm</div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="stat-icon-wrapper bg-purple">📱</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Danh mục</div>
            <div class="stat-value">{{ number_format($totalCategories) }}</div>
        </div>
        <div class="stat-icon-wrapper bg-blue">📂</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">
                {{ auth()->user()->isManager() ? 'Tổng đơn toàn hệ thống' : 'Đơn hàng của tôi' }}
            </div>
            <div class="stat-value">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="stat-icon-wrapper bg-green">🛒</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Doanh thu hoàn tất</div>
            <div class="stat-value" style="font-size: 20px; color: var(--success);">
                {{ number_format($totalRevenue) }} đ
            </div>
        </div>
        <div class="stat-icon-wrapper bg-orange">💰</div>
    </div>
</div>

<!-- Order Status Quick Summary -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Tình trạng xử lý đơn hàng</h2>
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
            <span>➕ Tạo đơn hàng mới</span>
        </a>
    </div>
    <div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="background: var(--warning-light); padding: 16px; border-radius: var(--radius-md); border-left: 4px solid var(--warning);">
            <div style="font-size: 13px; color: #b45309; font-weight: 600;">Chờ xác nhận (Pending)</div>
            <div style="font-size: 24px; font-weight: 800; color: #92400e;">{{ $pendingCount }}</div>
        </div>

        <div style="background: var(--primary-light); padding: 16px; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
            <div style="font-size: 13px; color: #4338ca; font-weight: 600;">Đang xử lý (Processing)</div>
            <div style="font-size: 24px; font-weight: 800; color: #3730a3;">{{ $processingCount }}</div>
        </div>

        <div style="background: var(--success-light); padding: 16px; border-radius: var(--radius-md); border-left: 4px solid var(--success);">
            <div style="font-size: 13px; color: #047857; font-weight: 600;">Đã hoàn thành (Completed)</div>
            <div style="font-size: 24px; font-weight: 800; color: #065f46;">{{ $completedCount }}</div>
        </div>

        <div style="background: var(--danger-light); padding: 16px; border-radius: var(--radius-md); border-left: 4px solid var(--danger);">
            <div style="font-size: 13px; color: #b91c1c; font-weight: 600;">Đã hủy (Cancelled)</div>
            <div style="font-size: 24px; font-weight: 800; color: #991b1b;">{{ $cancelledCount }}</div>
        </div>
    </div>
</div>

<!-- Architecture & Flow Knowledge (Requirement 8 & 10) -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Sơ đồ luồng hệ thống & Quy trình Deploy (BRSE Kiến thức cốt lõi)</h2>
    </div>
    <div style="padding: 24px;">
        <h3 style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">
            1. Luồng xử lý Request trong Laravel MVC (System Flow)
        </h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
            Ví dụ thao tác thêm sản phẩm hoặc duyệt đơn hàng:
        </p>
        <div class="flow-container">
            <span class="flow-step">👤 Người dùng (Browser)</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">📝 Gửi Form / URL</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🚏 Route (web.php)</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">⚙️ Controller</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">📦 Model (Eloquent)</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🗄️ MySQL Database</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">📤 Response</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🖥️ Blade View (HTML)</span>
        </div>

        <h3 style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-top: 24px; margin-bottom: 8px;">
            2. Luồng triển khai Web Server thực tế (Deploy Flow)
        </h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
            Khi người dùng truy cập tên miền dự án từ Internet:
        </p>
        <div class="flow-container">
            <span class="flow-step">🌐 Tên miền (Domain)</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">📡 Phân giải DNS</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🚀 Web Server Nginx</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">⚡ PHP-FPM FastCGI</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🔥 Laravel Framework</span>
            <span class="flow-arrow">➔</span>
            <span class="flow-step">🗄️ MySQL Server</span>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Đơn hàng gần đây</h2>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Xem tất cả đơn hàng</a>
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
