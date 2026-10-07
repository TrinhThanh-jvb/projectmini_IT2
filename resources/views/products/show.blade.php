@extends('layouts.app')

@section('title', 'Chi tiết sản phẩm: ' . $product->name)
@section('page_title', 'Chi tiết sản phẩm')

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">{{ $product->name }}</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">✏️ Sửa sản phẩm</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
        </div>
    </div>

    <div style="padding: 28px;">
        <div style="display: flex; flex-wrap: wrap; gap: 32px; align-items: flex-start;">
            <!-- Image Area -->
            <div style="width: 280px; flex-shrink: 0;">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 260px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                @else
                    <div style="width: 100%; height: 260px; background: #f1f5f9; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 64px; color: #94a3b8; border: 1px solid var(--border-color);">
                        📦
                    </div>
                @endif
            </div>

            <!-- Details Area -->
            <div style="flex: 1; min-width: 280px;">
                <div style="margin-bottom: 16px;">
                    <span class="badge badge-info" style="font-size: 13px;">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                    @if($product->status === 'Đang bán')
                        <span class="badge badge-success" style="font-size: 13px;">Đang bán</span>
                    @else
                        <span class="badge badge-danger" style="font-size: 13px;">Ngừng bán</span>
                    @endif
                </div>

                <div style="font-size: 30px; font-weight: 800; color: var(--primary); margin-bottom: 20px;">
                    {{ number_format($product->price) }} VNĐ
                </div>

                <div style="background: #f8fafc; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 24px;">
                    <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">Mô tả sản phẩm</h3>
                    <p style="white-space: pre-line; color: var(--text-muted); font-size: 14px;">
                        {{ $product->description ?: 'Chưa có mô tả chi tiết cho sản phẩm này.' }}
                    </p>
                </div>

                <div style="font-size: 13px; color: var(--text-muted);">
                    <div>📅 Ngày tạo: <strong>{{ $product->created_at->format('d/m/Y H:i:s') }}</strong></div>
                    <div>🔄 Cập nhật lần cuối: <strong>{{ $product->updated_at->format('d/m/Y H:i:s') }}</strong></div>
                    <div>🛍️ Tổng lượt mua trong đơn hàng: <strong>{{ $product->orderItems->sum('quantity') }} lượt</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
