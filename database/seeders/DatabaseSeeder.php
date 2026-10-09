<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\EmployeeProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Class DatabaseSeeder: Khởi tạo dữ liệu mẫu ban đầu cho toàn bộ hệ thống.
 * 
 * Dữ liệu khởi tạo gồm:
 * 1. 3 Nhân viên (1 Quản lý: admin, 2 Nhân viên bán hàng: sales1, sales2) cùng hồ sơ đầy đủ
 * 2. 4 Danh mục sản phẩm (Điện thoại, Máy tính, Phụ kiện, Thiết bị văn phòng)
 * 3. 7 Sản phẩm công nghệ tiêu biểu kèm giá cả, mô tả và trạng thái bán
 * 4. 3 Đơn hàng mẫu ở các trạng thái khác nhau (Completed, Processing, Pending) kèm chi tiết sản phẩm
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Chạy toàn bộ tiến trình gieo dữ liệu (Seed).
     */
    public function run(): void
    {
        // =========================================================================
        // 1. KHỞI TẠO TÀI KHOẢN NHÂN VIÊN & HỒ SƠ CÁ NHÂN (EMPLOYEES & PROFILES)
        // =========================================================================

        // 1.1. Tài khoản Quản lý (Admin / Manager)
        $manager = Employee::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $manager->id,
            'name' => 'Nguyễn Văn Admin',
            'email' => 'admin@diorbeauty.vn',
            'phone' => '0901234567',
            'address' => 'Dior Boutique, Tràng Tiền Plaza, Hoàn Kiếm, Hà Nội',
            'avatar' => null,
        ]);

        // 1.2. Nhân viên bán hàng số 1: Nguyễn Thị Sale1
        $sales1 = Employee::create([
            'username' => 'sales1',
            'password' => Hash::make('password123'),
            'role' => 'sales_staff',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $sales1->id,
            'name' => 'Nguyễn Thị Sale1',
            'email' => 'sale1@diorbeauty.vn',
            'phone' => '0912345678',
            'address' => 'Vincom Center Đồng Khởi, Quận 1, TP. Hồ Chí Minh',
            'avatar' => null,
        ]);

        // 1.3. Nhân viên bán hàng số 2: Nguyễn Thị Sale2
        $sales2 = Employee::create([
            'username' => 'sales2',
            'password' => Hash::make('password123'),
            'role' => 'sales_staff',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $sales2->id,
            'name' => 'Nguyễn Thị Sale2',
            'email' => 'sale2@diorbeauty.vn',
            'phone' => '0987654321',
            'address' => 'Lotte Mall West Lake, Tây Hồ, Hà Nội',
            'avatar' => null,
        ]);

        // =========================================================================
        // 2. KHỞI TẠO DANH MỤC SẢN PHẨM (CATEGORIES: Son, Cushion, Nước Hoa, Phấn Má)
        // =========================================================================
        $catSon = Category::create(['name' => 'Son']);
        $catCushion = Category::create(['name' => 'Cushion']);
        $catPerfume = Category::create(['name' => 'Nước Hoa']);
        $catBlush = Category::create(['name' => 'Phấn Má']);

        // =========================================================================
        // 3. KHỞI TẠO DANH SÁCH SẢN PHẨM DIOR (PRODUCTS & IMAGES)
        // =========================================================================
        $p1 = Product::create([
            'category_id' => $catSon->id,
            'name' => 'Son Thỏi Rouge Dior Velvet 999 Iconic Red',
            'price' => 1350000,
            'description' => 'Sắc đỏ kinh điển couture của Christian Dior với chất son nhung mịn lì, giữ màu tươi tắn suốt 16 giờ.',
            'image' => 'products/dior-lipstick.jpg',
            'status' => 'Đang bán',
        ]);

        $p2 = Product::create([
            'category_id' => $catSon->id,
            'name' => 'Son Kem Dior Addict Lip Tint Natural Berry',
            'price' => 1200000,
            'description' => 'Dòng son tint căng mọng tự nhiên, không lem, cung cấp độ ẩm dịu nhẹ cho đôi môi suốt 24 giờ.',
            'image' => 'products/dior-tint.jpg',
            'status' => 'Đang bán',
        ]);

        $p3 = Product::create([
            'category_id' => $catCushion->id,
            'name' => 'Phấn Nước Dior Prestige Le Cushion Teint de Rose',
            'price' => 2450000,
            'description' => 'Cushion hoàng gia chiết xuất 500 cánh hoa hồng Granville, tái tạo làn da căng bóng mịn màng và kiêu sa.',
            'image' => 'products/dior-cushion.jpg',
            'status' => 'Đang bán',
        ]);

        $p4 = Product::create([
            'category_id' => $catPerfume->id,
            'name' => 'Nước Hoa Nữ Dior J’adore Eau de Parfum 100ml',
            'price' => 4750000,
            'description' => 'Tuyệt tác hương thơm quyến rũ bất hủ của Dior hòa quyện hoa ngọc lan tây, hoa hồng Đan Mạch và nhài Grasse.',
            'image' => 'products/dior-perfume.jpg',
            'status' => 'Đang bán',
        ]);

        $p5 = Product::create([
            'category_id' => $catPerfume->id,
            'name' => 'Nước Hoa Nữ Miss Dior Eau de Parfum 100ml',
            'price' => 4200000,
            'description' => 'Bản giao hưởng ngát hương hoa linh lan, hoa mẫu đơn ngọt ngào thanh lịch và dải nơ couture thủ công lấp lánh.',
            'image' => 'products/miss-dior.jpg',
            'status' => 'Đang bán',
        ]);

        $p6 = Product::create([
            'category_id' => $catBlush->id,
            'name' => 'Phấn Má Hồng Dior Rosy Glow 001 Petal Pink',
            'price' => 1450000,
            'description' => 'Phấn má công nghệ Color Reviver phản ứng độc đáo theo độ ẩm da, mang lại đôi gò má ửng hồng trong trẻo tự nhiên.',
            'image' => 'products/dior-blush.jpg',
            'status' => 'Đang bán',
        ]);

        // =========================================================================
        // 4. KHỞI TẠO ĐƠN HÀNG MẪU & CHI TIẾT ĐƠN HÀNG (ORDERS & ORDER_ITEMS)
        // =========================================================================

        // Đơn hàng 1: Tạo bởi sales1, trạng thái Completed (Đã hoàn tất)
        $order1 = Order::create([
            'employee_id' => $sales1->id,
            'customer_name' => 'Trần Ngọc Bích',
            'customer_phone' => '0934111222',
            'total_amount' => 6100000,
            'status' => 'Completed',
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p1->id,
            'quantity' => 1,
            'price' => 1350000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p4->id,
            'quantity' => 1,
            'price' => 4750000,
        ]);

        // Đơn hàng 2: Tạo bởi sales1, trạng thái Processing (Đang xử lý)
        $order2 = Order::create([
            'employee_id' => $sales1->id,
            'customer_name' => 'Vũ Hoàng Yến Linh',
            'customer_phone' => '0978333444',
            'total_amount' => 3650000,
            'status' => 'Processing',
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p2->id,
            'quantity' => 1,
            'price' => 1200000,
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p3->id,
            'quantity' => 1,
            'price' => 2450000,
        ]);

        // Đơn hàng 3: Tạo bởi sales2, trạng thái Pending (Chờ xác nhận)
        $order3 = Order::create([
            'employee_id' => $sales2->id,
            'customer_name' => 'Lê Thùy Dương',
            'customer_phone' => '0918889999',
            'total_amount' => 5650000,
            'status' => 'Pending',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p5->id,
            'quantity' => 1,
            'price' => 4200000,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p6->id,
            'quantity' => 1,
            'price' => 1450000,
        ]);
    }
}
