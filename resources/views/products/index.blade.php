@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page_title', 'Danh sách sản phẩm')

@section('content')
<!-- Filter & Search Bar (Flexbox) -->
<form action="{{ route('products.index') }}" method="GET" class="filter-bar">
    <div class="search-input">
        <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm sản phẩm theo tên..." value="{{ request('search') }}">
    </div>

    <div class="select-control">
        <select name="category_id" class="form-control">
            <option value="">-- Tất cả danh mục --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="select-control">
        <select name="status" class="form-control">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Đang bán" {{ request('status') === 'Đang bán' ? 'selected' : '' }}>Đang bán</option>
            <option value="Ngừng bán" {{ request('status') === 'Ngừng bán' ? 'selected' : '' }}>Ngừng bán</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary">
        <span>Lọc dữ liệu</span>
    </button>

    @if(request()->anyFilled(['search', 'category_id', 'status']))
        <a href="{{ route('products.index') }}" class="btn btn-secondary" style="color: var(--danger);">
            <span>Xóa lọc</span>
        </a>
    @endif

    <div style="margin-left: auto;">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <span>➕ Thêm sản phẩm</span>
        </a>
    </div>
</form>

<!-- Products Panel & Table -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Tất cả sản phẩm ({{ $products->total() }})</h2>
    </div>

    <div class="table-responsive">
        <table class="app-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Đơn giá</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th style="text-align: right; width: 200px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-thumb">
                            @else
                                <div class="empty-thumb">📦</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" style="font-weight: 700; color: var(--primary);">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            {{ number_format($product->price) }} đ
                        </td>
                        <td>
                            @if($product->status === 'Đang bán')
                                <span class="badge badge-success">Đang bán</span>
                            @else
                                <span class="badge badge-danger">Ngừng bán</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $product->created_at->format('d/m/Y') }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm" title="Xem chi tiết">
                                    👁️
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm" title="Chỉnh sửa">
                                    ✏️
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm: {{ $product->name }}?')">
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
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Không tìm thấy sản phẩm nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div style="padding: 20px; display: flex; justify-content: flex-end;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
