@extends('layouts.app')

@section('title', 'Thêm sản phẩm mới')
@section('page_title', 'Thêm sản phẩm mới')

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Nhập thông tin sản phẩm</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
    </div>

    <div style="padding: 24px;">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Tên sản phẩm <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="VD: iPhone 15 Pro Max..." required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Danh mục <span style="color: red;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">Đơn giá (VNĐ) <span style="color: red;">*</span></label>
                    <input type="number" id="price" name="price" step="1000" min="0" class="form-control" value="{{ old('price') }}" placeholder="VD: 25000000" required>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái kinh doanh <span style="color: red;">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Đang bán" {{ old('status', 'Đang bán') === 'Đang bán' ? 'selected' : '' }}>Đang bán</option>
                        <option value="Ngừng bán" {{ old('status') === 'Ngừng bán' ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Hình ảnh sản phẩm</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small style="color: var(--text-muted); font-size: 12px;">Định dạng: JPG, PNG, WEBP. Tối đa 2MB.</small>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Mô tả sản phẩm</label>
                <textarea id="description" name="description" rows="5" class="form-control" placeholder="Mô tả chi tiết cấu hình, tính năng, đặc điểm sản phẩm...">{{ old('description') }}</textarea>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <span>💾 Lưu sản phẩm</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
