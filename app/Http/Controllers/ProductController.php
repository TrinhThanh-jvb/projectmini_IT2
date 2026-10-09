<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;

/**
 * Controller ProductController: Quản lý danh mục hàng hóa / sản phẩm (CRUD đầy đủ).
 * 
 * Các chức năng chính:
 * - Xem danh sách sản phẩm kết hợp tìm kiếm (search tên), bộ lọc (theo danh mục, theo trạng thái bán), phân trang
 * - Thêm mới sản phẩm có upload ảnh đại diện vào storage/public
 * - Xem chi tiết sản phẩm và các đơn hàng liên quan
 * - Chỉnh sửa sản phẩm, cập nhật thông tin và thay thế ảnh cũ nếu có ảnh mới
 * - Xóa sản phẩm và dọn dẹp file ảnh trong storage
 */
class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm có phân trang kết hợp tìm kiếm và bộ lọc.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Khởi tạo query cùng nạp trước mối quan hệ category (Eager Loading) để tối ưu truy vấn
        $query = Product::with('category');

        // 1. Tìm kiếm theo tên sản phẩm nếu người dùng nhập từ khóa
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 2. Lọc theo danh mục sản phẩm nếu có chọn
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Lọc theo trạng thái kinh doanh ('Đang bán' hoặc 'Ngừng bán')
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Phân trang 10 sản phẩm mỗi trang, giữ nguyên query string khi chuyển trang (withQueryString)
        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị form tạo mới sản phẩm.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Lấy danh sách danh mục để hiển thị thẻ dropdown <select>
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới vào cơ sở dữ liệu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ của dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // ID danh mục phải tồn tại trong bảng categories
            'price' => 'required|numeric|min:0',             // Đơn giá phải là số và lớn hơn hoặc bằng 0
            'description' => 'nullable|string',
            'status' => 'required|in:Đang bán,Ngừng bán',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // File ảnh tối đa 2MB
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là chữ số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
        ]);

        // 2. Xử lý upload file hình ảnh vào thư mục storage/app/public/products
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // 3. Lưu vào cơ sở dữ liệu
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm mới thành công!');
    }

    /**
     * Xem thông tin chi tiết một sản phẩm cụ thể.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        // Eager load danh mục và lịch sử đơn hàng chứa sản phẩm này
        $product->load('category', 'orderItems.order');
        return view('products.show', compact('product'));
    }

    /**
     * Hiển thị giao diện chỉnh sửa sản phẩm.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật thông tin sản phẩm và thay thế ảnh cũ nếu có tải lên ảnh mới.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        // 1. Kiểm tra tính hợp lệ dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:Đang bán,Ngừng bán',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là chữ số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        // 2. Nếu có file hình ảnh mới được tải lên
        if ($request->hasFile('image')) {
            // Xóa file ảnh cũ khỏi đĩa lưu trữ nếu tồn tại để tiết kiệm bộ nhớ
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // Lưu file ảnh mới
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // 3. Cập nhật bản ghi trong cơ sở dữ liệu
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Cập nhật thông tin sản phẩm thành công!');
    }

    /**
     * Xóa sản phẩm khỏi hệ thống.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Dọn dẹp file hình ảnh trong storage nếu có
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Thực hiện xóa sản phẩm
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}
