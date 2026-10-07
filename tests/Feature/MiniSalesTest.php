<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class MiniSalesTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Đăng nhập hệ thống');
    }

    public function test_manager_can_login_and_view_dashboard(): void
    {
        $manager = Employee::where('username', 'admin')->first();
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($manager);

        $dashboardResponse = $this->actingAs($manager)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Bảng điều khiển');
        $dashboardResponse->assertSee('Sơ đồ luồng hệ thống');
    }

    public function test_products_list_and_filter(): void
    {
        $manager = Employee::where('username', 'admin')->first();
        $response = $this->actingAs($manager)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Tất cả sản phẩm');
        $response->assertSee('iPhone 15 Pro Max');

        // Test search
        $searchResponse = $this->actingAs($manager)->get('/products?search=MacBook');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('MacBook Air M2');
    }

    public function test_sales_staff_can_only_see_their_own_orders(): void
    {
        $sales1 = Employee::where('username', 'sales1')->first();
        $response = $this->actingAs($sales1)->get('/orders');
        $response->assertStatus(200);
        $response->assertSee('Đơn hàng do bạn tạo');
    }

    public function test_categories_management(): void
    {
        $manager = Employee::where('username', 'admin')->first();
        $response = $this->actingAs($manager)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Điện thoại');
        $response->assertSee('Máy tính');
    }

    public function test_order_creation_flow(): void
    {
        $sales1 = Employee::where('username', 'sales1')->first();
        $product = Product::where('status', 'Đang bán')->first();

        $response = $this->actingAs($sales1)->post('/orders', [
            'customer_name' => 'Khách Hàng Test',
            'customer_phone' => '0988112233',
            'status' => 'Pending',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Khách Hàng Test',
            'customer_phone' => '0988112233',
            'employee_id' => $sales1->id,
        ]);
    }

    public function test_manager_can_access_employees_but_sales_cannot(): void
    {
        $manager = Employee::where('username', 'admin')->first();
        $sales1 = Employee::where('username', 'sales1')->first();

        // Manager can access
        $responseManager = $this->actingAs($manager)->get('/employees');
        $responseManager->assertStatus(200);
        $responseManager->assertSee('Danh sách nhân viên');

        // Sales staff is forbidden (403)
        $responseSales = $this->actingAs($sales1)->get('/employees');
        $responseSales->assertStatus(403);
    }
}
