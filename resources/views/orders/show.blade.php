{{-- 
    View: Xem chi tiết đơn hàng (Orders Show).
    Bao gồm 3 khu vực chính:
    1. Thông tin giao dịch (Khách hàng, Nhân viên phụ trách, Thời gian tạo) kèm Form đổi nhanh trạng thái đơn hàng (PATCH)
    2. Khối tổng kết thanh toán (Tổng số tiền, tổng số lượng mặt hàng) kèm nút Xóa đơn
    3. Bảng chi tiết từng món hàng (Tên sản phẩm, Danh mục, Đơn giá snapshot, Số lượng, Thành tiền) và dòng tổng cộng tfoot
--}}
@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))
@section('page_title', 'Chi tiết đơn hàng #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: flex-start; margin-bottom: 24px;">
    {{-- ================= KHỐI 1: THÔNG TIN KHÁCH HÀNG & NHÂN VIÊN ================= --}}
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Thông tin giao dịch</h2>
            {{-- Huy hiệu trạng thái đơn hàng --}}
            @if($order->status === 'Pending')
                <span class="badge badge-warning">Pending (Chờ xác nhận)</span>
            @elseif($order->status === 'Processing')
                <span class="badge badge-info">Processing (Đang chuẩn bị)</span>
            @elseif($order->status === 'Completed')
                <span class="badge badge-success">Completed (Hoàn thành)</span>
            @else
                <span class="badge badge-danger">Cancelled (Đã hủy)</span>
            @endif
        </div>
        <div style="padding: 24px;">
            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                <div>👤 Khách hàng: <strong>{{ $order->customer_name }}</strong></div>
                <div>📞 Số điện thoại: <strong>{{ $order->customer_phone }}</strong></div>
                <div>💼 Nhân viên phụ trách: 
                    <strong>{{ $order->employee->profile->name ?? $order->employee->username }}</strong>
                    <span style="font-size: 12px; color: var(--text-muted);">({{ $order->employee->username }})</span>
                </div>
                <div>📅 Thời gian tạo: <strong>{{ $order->created_at->format('d/m/Y H:i:s') }}</strong></div>
                <div>🔄 Cập nhật lần cuối: <strong>{{ $order->updated_at->format('d/m/Y H:i:s') }}</strong></div>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border-color);">

            {{-- Form cập nhật nhanh trạng thái đơn hàng (gửi method PATCH tới OrderController::updateStatus) --}}
            <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <label class="form-label">Cập nhật nhanh trạng thái đơn hàng:</label>
                <div style="display: flex; gap: 8px;">
                    <select name="status" class="form-control">
                        <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ $order->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= KHỐI 2: TỔNG KẾT THANH TOÁN & THAO TÁC ================= --}}
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Tổng kết thanh toán</h2>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
                {{-- Form xóa đơn hàng có xác nhận --}}
                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Xóa đơn hàng</button>
                </form>
            </div>
        </div>
        <div style="padding: 24px; text-align: center;">
            <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px;">TỔNG GIÁ TRỊ ĐƠN HÀNG</div>
            <div style="font-size: 36px; font-weight: 800; color: var(--primary); margin-bottom: 16px;">
                {{ number_format($order->total_amount) }} VNĐ
            </div>
            <div style="font-size: 13px; color: var(--text-muted);">
                Bao gồm {{ $order->orderItems->sum('quantity') }} sản phẩm thuộc {{ $order->orderItems->count() }} mặt hàng.
            </div>
        </div>
    </div>
</div>

{{-- ================= KHỐI 3: BẢNG CHI TIẾT CÁC MẶT HÀNG TRONG ĐƠN HÀNG ================= --}}
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Chi tiết các mặt hàng đã đặt</h2>
    </div>
    <div class="table-responsive">
        <table class="app-table">
            <thead>
                <tr>
                    <th style="width: 70px;">STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                {{-- Lặp qua từng bản ghi OrderItem --}}
                @foreach($order->orderItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 700;">
                            @if($item->product)
                                <a href="{{ route('products.show', $item->product) }}" style="color: var(--primary);">
                                    {{ $item->product->name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">Sản phẩm không còn tồn tại</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $item->product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td style="text-align: right;">{{ number_format($item->price) }} đ</td>
                        <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 800; color: var(--text-main);">
                            {{ number_format($item->price * $item->quantity) }} đ
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                {{-- Dòng chân bảng tính tổng cộng tiền đơn hàng --}}
                <tr style="background: #f8fafc; font-weight: 800;">
                    <td colspan="5" style="text-align: right; font-size: 15px;">Tổng cộng:</td>
                    <td style="text-align: right; font-size: 16px; color: var(--primary);">
                        {{ number_format($order->total_amount) }} đ
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
