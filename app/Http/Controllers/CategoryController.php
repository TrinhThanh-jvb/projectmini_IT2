<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

/**
 * Controller CategoryController: Quản lý danh mục hàng hóa (Categories).
 * 
 * Các chức năng:
 * - Xem danh sách kèm số lượng sản phẩm thuộc danh mục (withCount)
 * - Thêm mới danh mục (store)
 * - Cập nhật tên danh mục (update)
 * - Xóa danh mục kèm cơ chế ràng buộc toàn vẹn dữ liệu (destroy)
 */
class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các danh mục.
     * Sử dụng withCount('products') để đếm số sản phẩm trong từng danh mục mà không phải query N+1.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Lưu danh mục mới vào cơ sở dữ liệu.
     *
     * @param  \Illuminate\Http\Request  $request Chứa tên danh mục mới
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra tính hợp lệ: Tên danh mục là bắt buộc và không được trùng lặp
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        // 2. Tạo bản ghi mới trong bảng categories
        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Thêm danh mục mới thành công!');
    }

    /**
     * Cập nhật thông tin danh mục đã có.
     *
     * @param  \Illuminate\Http\Request  $request Chứa tên danh mục sửa đổi
     * @param  \App\Models\Category  $category Model danh mục được Laravel tự động resolve qua Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        // Kiểm tra tính hợp lệ: Cho phép giữ nguyên tên hiện tại của chính danh mục này, nhưng không trùng với danh mục khác
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        // Thực hiện cập nhật
        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Xóa một danh mục khỏi hệ thống.
     * Có kiểm tra ràng buộc nghiệp vụ: Không được xóa danh mục đang có sản phẩm.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Kiểm tra xem danh mục có chứa sản phẩm nào không
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có sản phẩm thuộc về nó. Hãy xóa hoặc chuyển danh mục sản phẩm trước.');
        }

        // Tiến hành xóa bản ghi nếu không có ràng buộc sản phẩm con
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
