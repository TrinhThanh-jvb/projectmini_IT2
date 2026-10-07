<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Order::with(['employee.profile', 'orderItems.product']);

        // Phân quyền: Manager xem tất cả, Sales Staff chỉ xem đơn của mình
        if (!$user->isManager()) {
            $query->where('employee_id', $user->id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên khách hàng hoặc SĐT
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        // Chỉ lấy các sản phẩm đang bán
        $products = Product::where('status', 'Đang bán')->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại khách hàng.',
            'items.required' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.min' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.quantity.min' => 'Số lượng mỗi sản phẩm tối thiểu là 1.',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = Order::create([
                'employee_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Tạo đơn hàng mới thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        // Kiểm tra quyền truy cập
        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền xem đơn hàng của nhân viên khác.');
        }

        $order->load(['employee.profile', 'orderItems.product']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền cập nhật đơn hàng này.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function destroy(Order $order)
    {
        $user = Auth::user();

        if (!$user->isManager() && $order->employee_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa đơn hàng này.');
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }
}
