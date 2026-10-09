{{-- 
    View: Tạo mới đơn đặt hàng (Orders Create).
    Bao gồm:
    1. Thông tin khách hàng (Tên, SĐT, Trạng thái đơn ban đầu)
    2. Bảng sản phẩm động (Dynamic Items Table): Cho phép thêm/bớt nhiều sản phẩm bằng Javascript
    3. Tự động tính toán thành tiền từng dòng và tổng cộng tiền đơn hàng (grandTotal) theo thời gian thực
--}}
@extends('layouts.app')

@section('title', 'Tạo đơn hàng mới')
@section('page_title', 'Tạo đơn hàng mới')

@section('content')
<div class="panel">
    {{-- Thanh tiêu đề khung và nút quay lại --}}
    <div class="panel-header">
        <h2 class="panel-title">Thông tin đơn hàng</h2>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
    </div>

    <div style="padding: 24px;">
        {{-- Form tạo đơn hàng gửi request POST tới OrderController::store --}}
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            {{-- ================= THÔNG TIN CHUNG CỦA ĐƠN HÀNG ================= --}}
            <div class="form-grid">
                {{-- Tên người mua hàng --}}
                <div class="form-group">
                    <label for="customer_name" class="form-label">Tên khách hàng <span style="color: red;">*</span></label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="VD: Nguyễn Văn A..." required>
                </div>

                {{-- Số điện thoại khách hàng --}}
                <div class="form-group">
                    <label for="customer_phone" class="form-label">Số điện thoại khách hàng <span style="color: red;">*</span></label>
                    <input type="text" id="customer_phone" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" placeholder="VD: 0912345678..." required>
                </div>

                {{-- Tên nhân viên phụ trách (Tự động gán user hiện tại) --}}
                <div class="form-group">
                    <label class="form-label">Nhân viên phụ trách</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->profile->name ?? auth()->user()->username }} (Bạn)" disabled style="background: #f1f5f9;">
                </div>

                {{-- Trạng thái đơn hàng khởi tạo --}}
                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái khởi tạo <span style="color: red;">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending (Chờ xác nhận)</option>
                        <option value="Processing" {{ old('status') === 'Processing' ? 'selected' : '' }}>Processing (Đang chuẩn bị)</option>
                        <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed (Đã giao xong)</option>
                    </select>
                </div>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--border-color);">

            {{-- ================= DANH SÁCH CHI TIẾT SẢN PHẨM ================= --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 700;">Danh sách sản phẩm trong đơn</h3>
                {{-- Nút bấm thêm dòng sản phẩm mới thông qua JS --}}
                <button type="button" id="btnAddItem" class="btn btn-secondary btn-sm">
                    ➕ Thêm sản phẩm
                </button>
            </div>

            {{-- Bảng chi tiết từng dòng mặt hàng --}}
            <div class="table-responsive">
                <table class="app-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="width: 180px;">Đơn giá</th>
                            <th style="width: 130px;">Số lượng</th>
                            <th style="width: 200px;">Thành tiền</th>
                            <th style="width: 60px; text-align: center;">Xóa</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- Dòng mặt hàng mặc định đầu tiên --}}
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_id]" class="form-control product-select" required onchange="calculateRow(this)">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-price="{{ $prod->price }}">
                                            {{ $prod->name }} ({{ number_format($prod->price) }} đ)
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="row-price" style="font-weight: 600;">0 đ</span>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control item-qty" value="1" min="1" required onchange="calculateRow(this)" oninput="calculateRow(this)">
                            </td>
                            <td>
                                <span class="row-subtotal" style="font-weight: 700; color: var(--primary);">0 đ</span>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Khối hiển thị tổng thành tiền của cả đơn hàng --}}
            <div style="margin-top: 24px; padding: 20px; background: #f8fafc; border-radius: var(--radius-md); display: flex; justify-content: flex-end; align-items: center; gap: 20px;">
                <span style="font-size: 16px; font-weight: 600; color: var(--text-muted);">Tổng cộng tiền đơn hàng:</span>
                <span id="grandTotal" style="font-size: 26px; font-weight: 800; color: var(--primary);">0 đ</span>
            </div>

            {{-- Các nút thao tác gửi form --}}
            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <span>🚀 Tạo đơn hàng</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Đoạn script JavaScript xử lý tính toán tiền và thêm/xóa dòng sản phẩm linh hoạt --}}
<script>
    let itemIndex = 1;

    // Hàm định dạng số tiền sang chuẩn tiền tệ Việt Nam (VNĐ)
    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
    }

    // Hàm tính toán giá trị thành tiền cho 1 dòng (Đơn giá * Số lượng)
    function calculateRow(element) {
        const row = element.closest('.item-row');
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.item-qty');
        const priceSpan = row.querySelector('.row-price');
        const subtotalSpan = row.querySelector('.row-subtotal');

        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
        const qty = parseInt(qtyInput.value) || 0;
        const subtotal = price * qty;

        priceSpan.innerText = formatNumber(price);
        subtotalSpan.innerText = formatNumber(subtotal);
        row.dataset.subtotal = subtotal;

        // Cập nhật lại tổng tiền cả đơn sau khi dòng thay đổi
        updateGrandTotal();
    }

    // Hàm cộng dồn thành tiền của toàn bộ các dòng vào grandTotal
    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            total += parseFloat(row.dataset.subtotal || 0);
        });
        document.getElementById('grandTotal').innerText = formatNumber(total);
    }

    // Hàm xóa dòng sản phẩm (đảm bảo giữ lại tối thiểu 1 dòng)
    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) {
            alert('Đơn hàng cần có ít nhất 1 sản phẩm.');
            return;
        }
        btn.closest('.item-row').remove();
        updateGrandTotal();
    }

    // Bắt sự kiện click nút Thêm sản phẩm để chèn thêm 1 dòng HTML mới
    document.getElementById('btnAddItem').addEventListener('click', function() {
        const tbody = document.getElementById('itemsBody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.dataset.subtotal = '0';
        newRow.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control product-select" required onchange="calculateRow(this)">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" data-price="{{ $prod->price }}">
                            {{ $prod->name }} (${formatNumber({{ $prod->price }})})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <span class="row-price" style="font-weight: 600;">0 đ</span>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1" min="1" required onchange="calculateRow(this)" oninput="calculateRow(this)">
            </td>
            <td>
                <span class="row-subtotal" style="font-weight: 700; color: var(--primary);">0 đ</span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">🗑️</button>
            </td>
        `;
        tbody.appendChild(newRow);
        itemIndex++;
    });
</script>
@endpush
@endsection
