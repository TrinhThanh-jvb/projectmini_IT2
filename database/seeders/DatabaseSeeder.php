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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Employees & Profiles
        $manager = Employee::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $manager->id,
            'name' => 'Nguyễn Quản Lý (Admin)',
            'email' => 'admin@minisales.vn',
            'phone' => '0901234567',
            'address' => 'Tòa nhà Landmark 72, Nam Từ Liêm, Hà Nội',
            'avatar' => null,
        ]);

        $sales1 = Employee::create([
            'username' => 'sales1',
            'password' => Hash::make('password123'),
            'role' => 'sales_staff',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $sales1->id,
            'name' => 'Trần Thị Thu Hà (Sales 01)',
            'email' => 'ha.tran@minisales.vn',
            'phone' => '0912345678',
            'address' => 'Hải Châu, Đà Nẵng',
            'avatar' => null,
        ]);

        $sales2 = Employee::create([
            'username' => 'sales2',
            'password' => Hash::make('password123'),
            'role' => 'sales_staff',
            'status' => 'active',
        ]);
        EmployeeProfile::create([
            'employee_id' => $sales2->id,
            'name' => 'Lê Hoàng Nam (Sales 02)',
            'email' => 'nam.le@minisales.vn',
            'phone' => '0987654321',
            'address' => 'Quận 1, TP. Hồ Chí Minh',
            'avatar' => null,
        ]);

        // 2. Seed Categories
        $catPhones = Category::create(['name' => 'Điện thoại']);
        $catLaptops = Category::create(['name' => 'Máy tính']);
        $catAccessories = Category::create(['name' => 'Phụ kiện']);
        $catOffice = Category::create(['name' => 'Thiết bị văn phòng']);

        // 3. Seed Products
        $p1 = Product::create([
            'category_id' => $catPhones->id,
            'name' => 'iPhone 15 Pro Max 256GB',
            'price' => 30990000,
            'description' => 'Khung viền titan cao cấp, chip Apple A17 Pro mạnh mẽ, camera tiềm vọng 5x.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        $p2 = Product::create([
            'category_id' => $catPhones->id,
            'name' => 'Samsung Galaxy S24 Ultra 256GB',
            'price' => 27990000,
            'description' => 'Màn hình phẳng tích hợp Galaxy AI đột phá, camera 200MP kèm bút S-Pen.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        $p3 = Product::create([
            'category_id' => $catLaptops->id,
            'name' => 'MacBook Air M2 13-inch 16GB/256GB',
            'price' => 24500000,
            'description' => 'Thiết kế siêu mỏng nhẹ, chip M2 cân bằng hiệu năng và thời lượng pin ấn tượng.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        $p4 = Product::create([
            'category_id' => $catLaptops->id,
            'name' => 'Dell XPS 13 9315 Core i7',
            'price' => 28000000,
            'description' => 'Màn hình FHD+ sắc nét, thiết kế nhôm nguyên khối siêu bền bỉ.',
            'image' => null,
            'status' => 'Ngừng bán',
        ]);

        $p5 = Product::create([
            'category_id' => $catAccessories->id,
            'name' => 'Tai nghe Apple AirPods Pro Gen 2',
            'price' => 5490000,
            'description' => 'Chống ồn chủ động gấp 2 lần, cổng sạc Type-C hiện đại.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        $p6 = Product::create([
            'category_id' => $catAccessories->id,
            'name' => 'Chuột không dây Logitech MX Master 3S',
            'price' => 2190000,
            'description' => 'Cảm biến 8K DPI trên mọi bề mặt, nút bấm yên tĩnh 90%, cuộn MagSpeed siêu tốc.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        $p7 = Product::create([
            'category_id' => $catOffice->id,
            'name' => 'Máy in laser Canon LBP 2900',
            'price' => 4350000,
            'description' => 'Huyền thoại in ấn văn phòng, độ bền cao, chi phí mực siêu rẻ.',
            'image' => null,
            'status' => 'Đang bán',
        ]);

        // 4. Seed Orders & Order Items
        // Order 1 (created by sales1)
        $order1 = Order::create([
            'employee_id' => $sales1->id,
            'customer_name' => 'Phạm Minh Đức',
            'customer_phone' => '0934111222',
            'total_amount' => 36480000,
            'status' => 'Completed',
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p1->id,
            'quantity' => 1,
            'price' => 30990000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p5->id,
            'quantity' => 1,
            'price' => 5490000,
        ]);

        // Order 2 (created by sales1)
        $order2 = Order::create([
            'employee_id' => $sales1->id,
            'customer_name' => 'Vũ Thị Mai Lan',
            'customer_phone' => '0978333444',
            'total_amount' => 27990000,
            'status' => 'Processing',
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p2->id,
            'quantity' => 1,
            'price' => 27990000,
        ]);

        // Order 3 (created by sales2)
        $order3 = Order::create([
            'employee_id' => $sales2->id,
            'customer_name' => 'Công ty TNHH Ánh Dương',
            'customer_phone' => '0243888999',
            'total_amount' => 33230000,
            'status' => 'Pending',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p3->id,
            'quantity' => 1,
            'price' => 24500000,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p6->id,
            'quantity' => 2,
            'price' => 2190000,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p7->id,
            'quantity' => 1,
            'price' => 4350000,
        ]);
    }
}
