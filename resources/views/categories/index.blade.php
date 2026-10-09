{{-- 
    View: Quản lý danh mục sản phẩm (Categories Index).
    Bao gồm 2 phần:
    1. Form thêm mới danh mục ở cột bên trái
    2. Bảng danh sách các danh mục ở cột bên phải, hỗ trợ chỉnh sửa tên trực tiếp (Inline edit) và xóa có kiểm tra ràng buộc.
--}}
@extends('layouts.app')

@section('title', 'Quản lý danh mục')
@section('page_title', 'Quản lý danh mục sản phẩm')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: flex-start;">
    {{-- ================= CỘT TRÁI: FORM THÊM MỚI DANH MỤC ================= --}}
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Thêm danh mục mới</h2>
        </div>
        <div style="padding: 24px;">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Tên danh mục <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="VD: Son, Nước hoa, Phấn má, Cushion, ..." required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <span>➕ Tạo danh mục</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ================= CỘT PHẢI: BẢNG DANH SÁCH DANH MỤC ================= --}}
    <div class="panel" style="grid-column: span 2;">
        <div class="panel-header">
            <h2 class="panel-title">Danh sách danh mục hiện có ({{ $categories->count() }})</h2>
        </div>
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên danh mục</th>
                        <th>Số lượng sản phẩm</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: right; width: 180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Lặp qua từng bản ghi danh mục --}}
                    @forelse($categories as $category)
                        <tr>
                            <td>#{{ $category->id }}</td>
                            <td>
                                {{-- Form chỉnh sửa tên trực tiếp trên từng dòng (Inline Form Update) --}}
                                <form id="form-edit-{{ $category->id }}" action="{{ route('categories.update', $category) }}" method="POST" style="display: flex; gap: 8px;">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" class="form-control" style="padding: 6px 10px; font-size: 13px; max-width: 220px;" required>
                                    <button type="submit" class="btn btn-secondary btn-sm" title="Lưu tên mới">Lưu</button>
                                </form>
                            </td>
                            <td>
                                {{-- Số lượng sản phẩm liên kết lấy từ products_count --}}
                                <span class="badge badge-info">{{ $category->products_count }} sản phẩm</span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px;">
                                {{ $category->created_at->format('d/m/Y') }}
                            </td>
                            <td style="text-align: right;">
                                {{-- Form xóa danh mục có hộp thoại xác nhận JavaScript --}}
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục: {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                Chưa có danh mục nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
