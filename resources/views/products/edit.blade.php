@extends('layouts.app')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page_title', 'Chỉnh sửa sản phẩm: ' . $product->name)

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Cập nhật thông tin</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
    </div>

    <div style="padding: 24px;">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Tên sản phẩm <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Danh mục <span style="color: red;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">Đơn giá (VNĐ) <span style="color: red;">*</span></label>
                    <input type="number" id="price" name="price" step="1000" min="0" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái kinh doanh <span style="color: red;">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Đang bán" {{ old('status', $product->status) === 'Đang bán' ? 'selected' : '' }}>Đang bán</option>
                        <option value="Ngừng bán" {{ old('status', $product->status) === 'Ngừng bán' ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Hình ảnh sản phẩm</label>
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    @endif
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>
                <small style="color: var(--text-muted); font-size: 12px;">Tải lên ảnh mới nếu muốn thay đổi.</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Mô tả sản phẩm</label>
                <textarea id="description" name="description" rows="5" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾 Cập nhật</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
