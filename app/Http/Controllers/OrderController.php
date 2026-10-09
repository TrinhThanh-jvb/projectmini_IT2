<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

/**
 * Controller OrderController: Quản lý quy trình bán hàng và xử lý đơn hàng.
 * 
 * Các tính năng và quy tắc nghiệp vụ:
 * - Phân quyền xem đơn hàng: Quản lý (Manager) thấy toàn bộ đơn hàng; Nhân viên bán hàng (Sales Staff) chỉ xem được đơn do mình tạo.
 * - Tìm kiếm theo tên khách hàng hoặc số điện thoại.
 * - Lọc theo trạng thái đơn hàng (Pending, Processing, Completed, Cancelled).
 * - Tạo đơn hàng: Sử dụng Database Transaction để tính tổng tiền (total_amount) và lưu chi tiết sản phẩm kèm đơn giá snapshot.
 * - Xem chi tiết đơn hàng (show).
 * - Cập nhật trạng thái đơn hàng (updateStatus).
 * - Hủy/Xóa đơn hàng (destroy) kèm kiểm tra quyền sở hữu.
 */
class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng có phân trang, tìm kiếm và lọc.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Nạp trước các quan hệ liên quan để tối ưu hóa truy vấn
        $query = Order::with(['employee.profile', 'orderItems.product']);

        // Phân quyền nghiệp vụ: Nếu không phải Quản lý, chỉ cho phép xem đơn của chính mình
        if (!$user->isManager()) {
            $query->where('employee_id', $user->id);
        }

        // Lọc theo trạng thái đơn hàng nếu được chọn
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên khách hàng hoặc số điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Phân trang 10 đơn hàng mỗi trang và duy trì chuỗi tham số truy vấn
        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Hiển thị giao diện tạo mới đơn hàng.
     * Chỉ nạp các sản phẩm có trạng thái 'Đang bán' để phục vụ việc lên đơn.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::where('status', 'Đang bán')->get();
        return view('orders.create', compact('products'));
    }

    /**
     * Xử lý lưu đơn hàng mới và các chi tiết sản phẩm tương ứng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ của dữ liệu
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
            'items' => 'required|array|min:1',                    // Đơn hàng bắt buộc phải có ít nhất 1 mặt hàng
            'items.*.product_id' => 'required|exists:products,id', // Từng mã sản phẩm phải tồn tại trong bảng products
            'items.*.quantity' => 'required|integer|min:1',        // Số lượng từng mặt hàng phải >= 1
        ], [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại khách hàng.',
            'items.required' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.min' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.quantity.min' => 'Số lượng mỗi sản phẩm tối thiểu là 1.',
        ]);

        // 2. Sử dụng Database Transaction để đảm bảo tính toàn vẹn khi ghi vào 2 bảng orders và order_items
        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItemsData = [];

            // Bước 2.1: Duyệt qua từng sản phẩm để tính toán giá trị dòng và tổng tiền đơn hàng
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                // Chuẩn bị dữ liệu cho từng dòng chi tiết đơn hàng (lưu giá tại thời điểm đặt)
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            // Bước 2.2: Tạo bản ghi đơn hàng chính (Order)
            $order = Order::create([
                'employee_id' => Auth::id(), // Gán nhân viên đang đăng nhập làm người tạo đơn
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
            ]);

            // Bước 2.3: Tạo các bản ghi chi tiết (OrderItems) liên kết với order_id vừa tạo
            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
            }

            // Hoàn tất Transaction
            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Tạo đơn hàng mới thành công!');
        } catch (\Exception $e) {
            // Rollback nếu có bất kỳ lỗi nào xảy ra
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Xem thông tin chi tiết một đơn đặt hàng.
     * Kiểm tra quyền: Nhân viên bán hàng chỉ được xem đơn hàng do chính mình tạo.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        // Kiểm tra quyền hạn truy cập
        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền xem đơn hàng của nhân viên khác.');
        }

        // Tải thông tin người tạo và danh sách sản phẩm trong đơn
        $order->load(['employee.profile', 'orderItems.product']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cập nhật nhanh trạng thái của đơn hàng (Pending, Processing, Completed, Cancelled).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        // Kiểm tra quyền: Chỉ quản lý hoặc người tạo ra đơn mới có quyền thay đổi trạng thái
        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền cập nhật đơn hàng này.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Xóa hoặc hủy bỏ đơn hàng khỏi hệ thống.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        $user = Auth::user();

        // Kiểm tra quyền hạn
        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa đơn hàng này.');
        }

        // Thực hiện xóa (các bản ghi order_items sẽ tự động cascade theo thiết kế migration)
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }
}
