{{-- 
    View: Quản lý sản phẩm (Products Index).
    Giao diện bao gồm:
    1. Thanh tìm kiếm & bộ lọc đa tiêu chí (Tên sản phẩm, Danh mục, Trạng thái bán)
    2. Nút Thêm sản phẩm mới
    3. Bảng dữ liệu hiển thị ảnh thu nhỏ, tên, danh mục, đơn giá, trạng thái và các nút hành động (Xem, Sửa, Xóa)
    4. Phân trang sản phẩm (Pagination)
--}}
@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page_title', 'Danh sách sản phẩm')

@section('content')
{{-- ================= THANH TÌM KIẾM VÀ BỘ LỌC DỮ LIỆU (FILTER BAR) ================= --}}
<form action="{{ route('products.index') }}" method="GET" class="filter-bar">
    {{-- Ô tìm kiếm theo tên sản phẩm --}}
    <div class="search-input">
        <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm sản phẩm theo tên..." value="{{ request('search') }}">
    </div>

    {{-- Bộ lọc theo danh mục sản phẩm --}}
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

    {{-- Bộ lọc theo trạng thái (Đang bán / Ngừng bán) --}}
    <div class="select-control">
        <select name="status" class="form-control">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Đang bán" {{ request('status') === 'Đang bán' ? 'selected' : '' }}>Đang bán</option>
            <option value="Ngừng bán" {{ request('status') === 'Ngừng bán' ? 'selected' : '' }}>Ngừng bán</option>
        </select>
    </div>

    {{-- Nút áp dụng bộ lọc --}}
    <button type="submit" class="btn btn-secondary">
        <span>Lọc dữ liệu</span>
    </button>

    {{-- Nút reset bộ lọc nếu đang có điều kiện lọc được chọn --}}
    @if(request()->anyFilled(['search', 'category_id', 'status']))
        <a href="{{ route('products.index') }}" class="btn btn-secondary" style="color: var(--danger);">
            <span>Xóa lọc</span>
        </a>
    @endif

    {{-- Nút thêm sản phẩm mới dẫn tới trang Create --}}
    <div style="margin-left: auto;">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <span>➕ Thêm sản phẩm</span>
        </a>
    </div>
</form>

{{-- ================= BẢNG DANH SÁCH SẢN PHẨM ================= --}}
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
                {{-- Lặp qua từng sản phẩm trong trang hiện tại --}}
                @forelse($products as $product)
                    <tr>
                        {{-- Cột hình ảnh minh họa --}}
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-thumb">
                            @else
                                <div class="empty-thumb">📦</div>
                            @endif
                        </td>
                        {{-- Tên sản phẩm có liên kết tới trang chi tiết --}}
                        <td>
                            <a href="{{ route('products.show', $product) }}" style="font-weight: 700; color: var(--primary);">
                                {{ $product->name }}
                            </a>
                        </td>
                        {{-- Tên danh mục --}}
                        <td>
                            <span class="badge badge-info">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                        </td>
                        {{-- Đơn giá bán VND --}}
                        <td style="font-weight: 700; color: var(--text-main);">
                            {{ number_format($product->price) }} đ
                        </td>
                        {{-- Huy hiệu trạng thái bán --}}
                        <td>
                            @if($product->status === 'Đang bán')
                                <span class="badge badge-success">Đang bán</span>
                            @else
                                <span class="badge badge-danger">Ngừng bán</span>
                            @endif
                        </td>
                        {{-- Ngày đăng sản phẩm --}}
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $product->created_at->format('d/m/Y') }}
                        </td>
                        {{-- Các nút hành động: Xem, Sửa, Xóa --}}
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

    {{-- Phân trang (Pagination) --}}
    @if($products->hasPages())
        <div style="padding: 20px; display: flex; justify-content: flex-end;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
