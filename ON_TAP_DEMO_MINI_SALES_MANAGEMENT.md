# DỰ ÁN: MINI SALES MANAGEMENT (LARAVEL)
## BỘ CÂU HỎI & CÂU TRẢ LỜI ÔN TẬP VÀ DEMO CUỐI KHÓA BRSE

> **Mô tả:** Tổng hợp 33 câu hỏi dự kiến trong buổi demo, giải thích chi tiết bám sát 100% mã nguồn thực tế của dự án kèm vị trí file code tham chiếu.

---

## PHẦN I: TỔNG QUAN LARAVEL & XỬ LÝ CHỨC NĂNG

### Câu 1. Flow của project từ lúc user truy cập website đến khi lấy được dữ liệu là gì?

**📁 Vị trí code thực tế:** `routes/web.php (L56), app/Http/Controllers/ProductController.php (L28-L53), resources/views/products/index.blade.php`

**Trả lời:**

• Flow tổng quát:
Browser (User) ➔ Route ➔ Middleware (nếu có) ➔ Controller ➔ Model ➔ Database (MySQL) ➔ Model ➔ Controller ➔ View (Blade) ➔ Browser (HTML hiển thị).

• Ví dụ cụ thể trong dự án (Xem danh sách sản phẩm):
1. Trình duyệt gửi HTTP GET /products.
2. Router so khớp trong routes/web.php, qua middleware 'auth' và gọi ProductController::index().
3. Controller gọi Model Product::with('category')->latest()->paginate(10).
4. Eloquent Model truy vấn MySQL, nạp kèm thông tin danh mục (Eager Loading).
5. Dữ liệu trả về Controller, Controller truyền dữ liệu sang View: view('products.index', compact('products', 'categories')).
6. Blade Engine render thành HTML hoàn chỉnh và trả về trình duyệt hiển thị cho người dùng.

---

### Câu 2. Tại sao dùng Laravel thay vì PHP thuần?

**📁 Vị trí code thực tế:** `Toàn bộ kiến trúc thư mục app/, routes/, database/migrations/`

**Trả lời:**

1. Cấu trúc chuẩn MVC: Tách biệt rõ ràng giữa logic dữ liệu (Model), giao diện (View) và điều phối (Controller), giúp code ngăn nắp, dễ đọc và dễ bảo trì.
2. Hệ sinh thái có sẵn mạnh mẽ: Cung cấp sẵn Routing, Eloquent ORM, Validation, Middleware, Authentication, Migration, Blade template engine mà không cần tự code lại từ đầu.
3. Bảo mật tích hợp sẵn: Tự động phòng chống các lỗ hổng phổ biến như SQL Injection (PDO Prepared Statements qua Eloquent), XSS (Blade {{ }} tự escape html), CSRF (@csrf token trong mọi form POST/PUT/DELETE).
4. Quản lý database bằng code: Migration và Seeder giúp cả nhóm đồng bộ cấu trúc DB tức thì, có thể rollback khi cần.
5. Năng suất phát triển cao: Tiết kiệm 60-70% thời gian so với PHP thuần, hỗ trợ làm việc nhóm và mở rộng dự án lâu dài.

---

### Câu 3. MVC là gì? Project Laravel áp dụng MVC như thế nào?

**📁 Vị trí code thực tế:** `app/Models/Product.php, app/Http/Controllers/ProductController.php, resources/views/products/index.blade.php`

**Trả lời:**

• MVC (Model - View - Controller) là mô hình kiến trúc phân tách ứng dụng thành 3 thành phần chính:
- Model: Quản lý dữ liệu, logic thao tác cơ sở dữ liệu và quan hệ giữa các bảng. Nằm tại app/Models/ (Product, Category, Employee, EmployeeProfile, Order, OrderItem).
- View: Chịu trách nhiệm hiển thị giao diện cho người dùng, nhận dữ liệu từ Controller. Nằm tại resources/views/ (viết bằng Blade template engine).
- Controller: Tầng trung gian tiếp nhận request từ người dùng, gọi Model xử lý và trả về View tương ứng. Nằm tại app/Http/Controllers/ (ProductController, CategoryController, OrderController, AuthController).

• Ví dụ áp dụng thực tế: Khi quản lý sản phẩm, ProductController gọi Product Model để lấy danh sách từ database, sau đó truyền biến $products sang view products/index.blade.php để hiển thị bảng dữ liệu.

---

### Câu 4. Khi user gửi một request lên Laravel thì request được xử lý như thế nào?

**📁 Vị trí code thực tế:** `public/index.php, app/Http/Kernel.php, routes/web.php`

**Trả lời:**

1. Entry Point: Request đi vào điểm đón đầu tiên tại public/index.php.
2. Bootstrap: HTTP Kernel khởi động hệ thống, nạp cấu hình và các Service Providers.
3. Routing: Router kiểm tra URL và HTTP Method (GET, POST, PUT, DELETE) đối chiếu với các định nghĩa trong routes/web.php.
4. Middleware: Request chạy qua các lớp bộ lọc bảo vệ (VerifyCsrfToken, Authenticate 'auth', CheckManager 'manager'). Nếu không đạt điều kiện sẽ bị redirect hoặc chặn lại.
5. Controller Handling: Nếu hợp lệ, request được chuyển tới Controller và Action tương ứng (ví dụ: ProductController@store).
6. Business & Database: Controller thực hiện validate dữ liệu, gọi Model truy vấn MySQL.
7. Response Delivery: Controller đóng gói kết quả thành HTTP Response (View HTML, Redirect kèm Flash message, hoặc JSON) gửi ngược lại cho trình duyệt.

---

### Câu 5. Flow khi thêm một Product mới là gì?

**📁 Vị trí code thực tế:** `app/Http/Controllers/ProductController.php (L60-L101), resources/views/products/create.blade.php`

**Trả lời:**

1. Bước 1 (Hiển thị Form): User click 'Thêm sản phẩm' ➔ GET /products/create ➔ ProductController::create() lấy danh sách Category::all() truyền vào view products/create.blade.php để hiển thị dropdown danh mục.
2. Bước 2 (Nhập liệu & Gửi đi): User điền tên, chọn danh mục, nhập giá, mô tả, chọn ảnh, trạng thái và bấm 'Lưu' ➔ Gửi HTTP POST /products kèm _token CSRF.
3. Bước 3 (Validation Backend): ProductController::store() kiểm tra dữ liệu đầu vào. Nếu vi phạm (ví dụ giá trống, danh mục không tồn tại), tự động văng về form cũ kèm thông báo lỗi và giữ lại old input.
4. Bước 4 (Xử lý File Ảnh): Nếu có file upload, controller lưu file vào storage/app/public/products và lấy path lưu vào database.
5. Bước 5 (Lưu Database): Gọi Product::create($validated) để thực hiện câu lệnh INSERT vào bảng products.
6. Bước 6 (Phản hồi): Redirect về route products.index kèm flash message thông báo 'Thêm sản phẩm mới thành công!'.

---

### Câu 6. Ví dụ Route dùng để thêm Product là gì?

**📁 Vị trí code thực tế:** `routes/web.php (L56)`

**Trả lời:**

• Trong dự án, hệ thống khai báo bằng Resource Route tại dòng 56 file routes/web.php:
Route::resource('products', ProductController::class);

• Resource này tự động sinh ra 2 route tương ứng phục vụ việc thêm mới:
1. Route hiển thị form thêm mới:
GET /products/create ➔ ProductController@create ➔ Name: products.create
2. Route tiếp nhận dữ liệu submit từ form:
POST /products ➔ ProductController@store ➔ Name: products.store

---

### Câu 7. Controller có trách nhiệm gì?

**📁 Vị trí code thực tế:** `app/Http/Controllers/`

**Trả lời:**

• Controller là tầng điều phối (Application Orchestration), có trách nhiệm:
1. Tiếp nhận HTTP Request và tham số gửi lên từ Client.
2. Thực hiện hoặc kích hoạt quá trình Validation dữ liệu đầu vào.
3. Gọi các Model, Service hoặc Event thích hợp để xử lý dữ liệu và logic nghiệp vụ.
4. Đóng gói và trả về Response thích hợp: View Blade kèm dữ liệu, Redirect URL kèm Flash session, hoặc JSON API.
• Nguyên tắc vàng: Tránh viết 'Fat Controller' (dồn toàn bộ business logic phức tạp hay câu SQL dài vào controller). Nên giữ Controller tinh gọn, dễ đọc, dễ kiểm thử.

---

### Câu 8. Product Model dùng để làm gì?

**📁 Vị trí code thực tế:** `app/Models/Product.php`

**Trả lời:**

• Product Model (app/Models/Product.php) đại diện cho bảng products trong database qua Eloquent ORM:
1. Khai báo thuộc tính fillable: Bảo vệ chống lỗ hổng Mass Assignment khi thêm/sửa bằng Product::create() hoặc $product->update().
2. Định nghĩa các Relationships:
   - category(): belongsTo(Category::class, 'category_id')
   - orderItems(): hasMany(OrderItem::class, 'product_id')
   - orders(): belongsToMany(Order::class, 'order_items')->withPivot('quantity', 'price')
3. Cung cấp API truy vấn dữ liệu trực quan: Product::where('status', 'Đang bán')->get(), Product::paginate(10),...

---

### Câu 19. Validation khi thêm Product được thực hiện như thế nào?

**📁 Vị trí code thực tế:** `app/Http/Controllers/ProductController.php (L75-L90)`

**Trả lời:**

• Validation được thực hiện ở Backend ngay đầu method store() trong ProductController.php:

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
    'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
]);

• Nếu không hợp lệ, Laravel tự động throw ValidationException, redirect về form kèm túi lỗi $errors.

---

### Câu 20. Nếu nhập price = 'abc' thì chuyện gì xảy ra?

**📁 Vị trí code thực tế:** `app/Http/Controllers/ProductController.php (L79, L87)`

**Trả lời:**

• Do trường 'price' có rule validation là 'numeric' và 'min:0', chuỗi 'abc' không thỏa mãn điều kiện số học.
• Controller sẽ dừng xử lý ngay lập tức tại bước validate(), không gọi tới lệnh Product::create(), bảo vệ database không bị lỗi kiểu dữ liệu.
• Laravel redirect người dùng quay lại form thêm sản phẩm, hiển thị thông báo lỗi màu đỏ: 'Giá sản phẩm phải là chữ số' và giữ lại các ô nhập liệu khác nhờ hàm old().

---

### Câu 21. Frontend đã validation rồi, tại sao Backend vẫn phải validation?

**📁 Vị trí code thực tế:** `Quy chuẩn an toàn trong thiết kế phần mềm doanh nghiệp`

**Trả lời:**

• Frontend Validation: Chủ yếu nâng cao trải nghiệm người dùng (UX), phản hồi lỗi nhanh chóng trên trình duyệt ngay khi người dùng gõ phím mà không cần gửi request.
• Tuy nhiên, Frontend Validation KHÔNG THỂ BẢO VỆ HỆ THỐNG vì rất dễ bị bypass (bằng cách dùng Postman, cURL, tắt JavaScript, hoặc chỉnh sửa trực tiếp HTML DOM qua F12 DevTools).
• Backend Validation: Là chốt chặn bảo mật bắt buộc cuối cùng, đảm bảo dữ liệu ghi vào database luôn toàn vẹn, đúng kiểu dữ liệu, ngăn chặn lỗi hệ thống và phòng chống các cuộc tấn công dữ liệu nguy hiểm.

---

### Câu 22. Nếu Product đã được sử dụng trong order_items thì có thể xóa Product không?

**📁 Vị trí code thực tế:** `database/migrations/2024_01_01_000006_create_order_items_table.php, app/Models/Product.php`

**Trả lời:**

• Về mặt Database: Trong migration order_items, khóa ngoại product_id có onDelete('cascade'). Nếu xóa cứng Product thì DB sẽ xóa luôn các bản ghi chi tiết đơn hàng tương ứng.
• Về mặt Nghiệp vụ Bán hàng Thực tế: TUYỆT ĐỐI KHÔNG NÊN XÓA CỨNG sản phẩm đã phát sinh giao dịch, vì sẽ làm mất dữ liệu lịch sử đơn hàng của khách, sai lệch báo cáo doanh thu và kế toán.
• Giải pháp chuẩn công nghiệp:
1. Chuyển trạng thái sản phẩm sang 'Ngừng bán' (status = 'Ngừng bán') để không hiển thị khi tạo đơn hàng mới.
2. Hoặc áp dụng cơ chế Soft Delete (sử dụng trait SoftDeletes trong Laravel) để bản ghi được đánh dấu deleted_at thay vì bị xóa vĩnh viễn khỏi MySQL.

---

### Câu 23. Quan hệ giữa Category và Product là gì?

**📁 Vị trí code thực tế:** `app/Models/Category.php (L30), app/Models/Product.php (L40-L43)`

**Trả lời:**

• Quan hệ 1 - Nhiều (One-to-Many):
- Một danh mục (Category) có thể chứa nhiều sản phẩm (Products).
- Một sản phẩm (Product) chỉ thuộc về duy nhất một danh mục xác định.
• Trong Laravel Eloquent:
- Category Model: public function products() { return $this->hasMany(Product::class, 'category_id'); }
- Product Model: public function category() { return $this->belongsTo(Category::class, 'category_id'); }

---

### Câu 24. Nếu xóa Category đang có Product thì xử lý như thế nào?

**📁 Vị trí code thực tế:** `app/Http/Controllers/CategoryController.php (L83-L94)`

**Trả lời:**

• Trong dự án, hệ thống đã cài đặt cơ chế kiểm tra ràng buộc nghiệp vụ ngay tại CategoryController::destroy():

if ($category->products()->count() > 0) {
    return back()->with('error', 'Không thể xóa danh mục đang có sản phẩm thuộc về nó. Hãy xóa hoặc chuyển danh mục sản phẩm trước.');
}
$category->delete();

• Hệ thống sẽ chặn hành động xóa và hiển thị cảnh báo yêu cầu người quản trị phải chuyển các sản phẩm con sang danh mục khác hoặc xóa hết sản phẩm con trước, ngăn ngừa tình trạng mất dữ liệu hàng loạt ngoài ý muốn.

---

### Câu 25. hasMany và belongsTo khác nhau như thế nào?

**📁 Vị trí code thực tế:** `app/Models/Category.php vs app/Models/Product.php`

**Trả lời:**

• hasMany (1 - N): Đứng từ đối tượng cha sở hữu nhiều đối tượng con. Bảng cha KHÔNG chứa khóa ngoại. Ví dụ: Category hasMany Products (bảng categories không có cột product_id).
• belongsTo (N - 1): Đứng từ đối tượng con thuộc về đối tượng cha. Bảng con là bảng TRỰC TIẾP LƯU CỘT KHÓA NGOẠI (Foreign Key). Ví dụ: Product belongsTo Category (bảng products lưu cột category_id tham chiếu tới categories.id).

---

### Câu 26. Flow đăng nhập của hệ thống là gì?

**📁 Vị trí code thực tế:** `app/Http/Controllers/AuthController.php (L42-L79)`

**Trả lời:**

• Được hiện thực đầy đủ trong AuthController::login():
1. User nhập username & password ➔ Gửi POST /login.
2. Controller validate dữ liệu bắt buộc nhập cả username và password.
3. Truy vấn tìm tài khoản: $employee = Employee::where('username', $credentials['username'])->first();
4. So khớp mật khẩu đã hash: Dùng Hash::check($credentials['password'], $employee->password). Nếu sai trả lỗi 'Tên đăng nhập hoặc mật khẩu không chính xác.'.
5. Kiểm tra trạng thái tài khoản: Nếu $employee->status !== 'active' thì chặn đăng nhập với thông báo tài khoản bị khóa/ngừng hoạt động.
6. Xác thực thành công: Gọi Auth::login($employee, $remember), gọi $request->session()->regenerate() để tạo mới Session ID (phòng chống tấn công Session Fixation), sau đó redirect vào trang Dashboard.

---

## PHẦN II: DATABASE & ELOQUENT RELATIONSHIP

### Câu 27. Database của project gồm những bảng nào và quan hệ tổng thể ra sao?

**📁 Vị trí code thực tế:** `ERD_AND_SYSTEM_FLOW.md, README.md, database/migrations/`

**Trả lời:**

• Hệ thống gồm 6 bảng nghiệp vụ cốt lõi:
1. employees: Tài khoản đăng nhập, vai trò (role: manager/staff), trạng thái (active/inactive).
2. employee_profiles: Thông tin cá nhân chi tiết (name, email, phone, address, avatar).
3. categories: Danh mục phân loại sản phẩm.
4. products: Sản phẩm kinh doanh, giá bán, mô tả, ảnh, trạng thái.
5. orders: Đơn hàng, mã đơn, nhân viên phụ trách, khách hàng, tổng tiền, trạng thái đơn.
6. order_items: Chi tiết món hàng trong đơn, số lượng, đơn giá snapshot thời điểm mua.

• Sơ đồ quan hệ tổng thể:
- Employee 1 — 1 EmployeeProfile (hasOne / belongsTo qua employee_id)
- Category 1 — N Product (hasMany / belongsTo qua category_id)
- Employee 1 — N Order (hasMany / belongsTo qua employee_id)
- Order 1 — N OrderItem (hasMany / belongsTo qua order_id)
- Product 1 — N OrderItem (hasMany / belongsTo qua product_id)
- Order N — N Product thông qua bảng trung gian order_items (belongsToMany)

---

### Câu 9. Employee có những relationship nào?

**📁 Vị trí code thực tế:** `app/Models/Employee.php (L40-L55)`

**Trả lời:**

• Trong Employee Model (app/Models/Employee.php):
1. profile(): hasOne(EmployeeProfile::class, 'employee_id')
   ➔ Quan hệ 1 - 1: Mỗi nhân viên gắn với duy nhất 1 hồ sơ cá nhân.
2. orders(): hasMany(Order::class, 'employee_id')
   ➔ Quan hệ 1 - N: Một nhân viên có thể phụ trách tạo nhiều đơn hàng.

---

### Câu 10. Order có những relationship nào?

**📁 Vị trí code thực tế:** `app/Models/Order.php (L30-L55)`

**Trả lời:**

• Trong Order Model (app/Models/Order.php):
1. employee(): belongsTo(Employee::class, 'employee_id')
   ➔ Quan hệ N - 1: Đơn hàng thuộc về nhân viên phụ trách.
2. orderItems(): hasMany(OrderItem::class, 'order_id')
   ➔ Quan hệ 1 - N: Một đơn hàng có nhiều dòng chi tiết sản phẩm mua.
3. products(): belongsToMany(Product::class, 'order_items')->withPivot('quantity', 'price')->withTimestamps()
   ➔ Quan hệ N - N: Một đơn hàng liên kết nhiều sản phẩm thông qua bảng trung gian order_items.

---

### Câu 11. Primary Key (PK) và Foreign Key (FK) là gì? Vai trò trong project?

**📁 Vị trí code thực tế:** `database/migrations/`

**Trả lời:**

• Primary Key (Khóa chính - PK):
- Là cột định danh duy nhất cho mỗi bản ghi trong bảng (không trùng lặp, không rỗng).
- Ví dụ trong project: id trong bảng employees, categories, products, orders, order_items.
• Foreign Key (Khóa ngoại - FK):
- Là cột dùng để liên kết dữ liệu với khóa chính của một bảng khác, ràng buộc tính toàn vẹn dữ liệu.
- Ví dụ trong project:
  + products.category_id trỏ tới categories.id
  + orders.employee_id trỏ tới employees.id
  + order_items.order_id trỏ tới orders.id
  + order_items.product_id trỏ tới products.id
• Vai trò: Đảm bảo tính nhất quán (không thể trỏ tới ID không tồn tại) và hỗ trợ tự động xóa/cập nhật theo cấu hình cascading.

---

### Câu 12. Các loại relationship chính trong project là gì?

**📁 Vị trí code thực tế:** `app/Models/`

**Trả lời:**

• Trong dự án áp dụng đầy đủ 3 loại quan hệ kinh điển của hệ CSDL quan hệ:
1. One-to-One (1 - 1): Employee ➔ EmployeeProfile
2. One-to-Many (1 - N):
   - Category ➔ Products
   - Employee ➔ Orders
   - Order ➔ OrderItems
   - Product ➔ OrderItems
3. Many-to-Many (N - N): Order ➔ Products (thông qua bảng liên kết trung gian order_items kèm thông tin bổ sung: quantity, price).

---

### Câu 13. Tại sao Order và Product dùng order_items thay vì liên kết trực tiếp?

**📁 Vị trí code thực tế:** `database/migrations/2024_01_01_000006_create_order_items_table.php, app/Models/OrderItem.php`

**Trả lời:**

1. Bản chất quan hệ Nhiều - Nhiều (N - N): Một đơn hàng có thể mua nhiều loại sản phẩm, và một sản phẩm có thể có mặt trong nhiều đơn hàng khác nhau. Không thể đặt foreign key trực tiếp vào một trong hai bảng mà bắt buộc phải dùng bảng trung gian.
2. Lưu trữ thuộc tính riêng của từng dòng hàng: Bảng trung gian order_items không chỉ lưu cặp (order_id, product_id) mà còn lưu:
   - quantity: Số lượng mua của từng món.
   - price: Đơn giá snapshot tại thời điểm đặt hàng. Đây là nguyên tắc cốt lõi của thương mại: nếu sau này giá sản phẩm trong bảng products thay đổi, đơn giá của các đơn hàng trong quá khứ vẫn giữ nguyên, không làm sai lệch báo cáo tài chính.

---

### Câu 14. Migration là gì và dùng để làm gì?

**📁 Vị trí code thực tế:** `database/migrations/`

**Trả lời:**

• Migration là công cụ quản lý phiên bản cơ sở dữ liệu (Version Control for Database) bằng code PHP trong Laravel.
• Tác dụng chính:
1. Cho phép tạo bảng, chỉnh sửa cột, thêm index và thiết lập Foreign Key bằng code.
2. Giúp đồng bộ hóa cấu trúc cơ sở dữ liệu trên toàn bộ máy của các lập trình viên trong nhóm và server production chỉ bằng 1 lệnh duy nhất: php artisan migrate.
3. Hỗ trợ rollback (hoàn tác) cấu trúc database khi có sự cố bằng lệnh: php artisan migrate:rollback.
4. Giúp code review theo dõi được lịch sử thay đổi cấu trúc database giống như git commit code.

---

## PHẦN III: GIT & QUY TRÌNH LÀM VIỆC

### Câu 15. git status dùng để làm gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Dùng để kiểm tra trạng thái hiện tại của thư mục làm việc (Working Tree) và vùng chuẩn bị (Staging Area).
• Cho biết:
  - Những file nào đã chỉnh sửa nhưng chưa đưa vào Staging Area (Changes not staged for commit).
  - Những file mới tạo mà Git chưa theo dõi (Untracked files).
  - Những thay đổi đã được staged sẵn sàng commit (Changes to be committed).
  - Tên branch hiện tại và tình trạng so với remote (ahead/behind).

---

### Câu 16. git add dùng để làm gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Dùng để chuyển các file hoặc đoạn thay đổi từ Working Directory vào Staging Area (vùng đệm chuẩn bị cho commit kế tiếp).
• Ví dụ:
  - git add . : Thêm tất cả thay đổi trong thư mục hiện tại.
  - git add app/Models/Product.php : Chỉ thêm file Model chỉ định.
  - git add -p : Thêm từng phần nhỏ của file (chọn lọc từng block code).

---

### Câu 17. git commit dùng để làm gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Dùng để chụp lại một snapshot (bản ghi lịch sử) của tất cả các thay đổi đang nằm trong Staging Area và ghi vào Git Database Local.
• Luôn đi kèm thông điệp mô tả thay đổi (commit message):
  Ví dụ: git commit -m "feat: implement order status update patch route"

---

### Câu 18. git push dùng để làm gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Dùng để tải lên (upload) các commit từ local repository lên remote repository (GitHub / GitLab / Bitbucket) để chia sẻ code với các thành viên khác trong team.
• Ví dụ: git push origin feature/product-crud

---

### Câu 28. git pull dùng để làm gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Dùng để lấy các commit mới nhất từ remote repository về máy local và tự động tích hợp (merge hoặc rebase) vào branch hiện tại.
• Về bản chất: git pull tương đương với việc chạy git fetch sau đó chạy git merge (hoặc git rebase).

---

### Câu 29. git fetch khác git pull như thế nào?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• git fetch: Chỉ tải về các commit, branch và tag mới từ remote về lưu trữ local nhưng KHÔNG tự động gộp (merge) vào code của branch bạn đang đứng. An toàn tuyệt đối, cho phép developer xem trước sự thay đổi bằng git diff hoặc git log trước khi quyết định merge.
• git pull: Thực hiện cả hai việc: Tải về (fetch) và TỰ ĐỘNG GỘP (merge) ngay vào branch hiện tại. Nếu có xung đột sẽ phát sinh merge conflict ngay lập tức.

---

### Câu 30. Branch trong Git là gì?

**📁 Vị trí code thực tế:** `Lệnh cơ bản Git CLI`

**Trả lời:**

• Branch (nhánh) là một con trỏ di động trỏ tới một chuỗi commit độc lập trong Git.
• Lợi ích: Cho phép developer tách riêng luồng phát triển cho một tính năng (feature), sửa lỗi (bugfix) hoặc thử nghiệm (experiment) mà không làm ảnh hưởng đến nhánh chính (main/master). Nhiều lập trình viên có thể làm việc song song trên các nhánh khác nhau mà không giẫm chân lên nhau.

---

### Câu 31. Tại sao không nên code trực tiếp trên main?

**📁 Vị trí code thực tế:** `Quy chuẩn Git Flow trong doanh nghiệp`

**Trả lời:**

1. Main là nhánh chạy thực tế: Nhánh main thường được kết nối CI/CD để triển khai lên Production hoặc môi trường Staging. Code trực tiếp trên main rất dễ đẩy code lỗi, code chưa hoàn thiện làm sập hệ thống.
2. Bỏ qua quy trình kiểm duyệt: Code trực tiếp sẽ bỏ qua bước tạo Pull Request (PR) và Code Review từ Tech Lead/đồng nghiệp, dẫn đến chất lượng code kém.
3. Rất khó sửa lỗi khi xảy ra xung đột lớn: Khi nhiều người cùng push lên main, xung đột mã nguồn xảy ra liên tục và làm gián đoạn toàn bộ dự án.
➔ Quy tắc chuẩn: Luôn rẽ nhánh từ main (feature/...), code xong tạo Pull Request, review và test đạt chuẩn mới merge vào main.

---

### Câu 32. Khi xảy ra merge conflict thì xử lý như thế nào?

**📁 Vị trí code thực tế:** `Quy chuẩn xử lý sự cố Git`

**Trả lời:**

• Quy trình xử lý xung đột chuẩn gồm 7 bước:
1. Chạy git status để xác định danh sách các file đang bị xung đột (Unmerged paths).
2. Mở các file bị conflict trên IDE (VS Code / PHPStorm).
3. Nhận diện các điểm conflict marker do Git chèn vào:
   <<<<<<< HEAD (code hiện tại của bạn)
   =======
   >>>>>>> branch_name (code từ branch đang merge vào)
4. Phân tích ngữ cảnh, trao đổi với đồng đội viết đoạn code đó để quyết định giữ code của ai hoặc kết hợp cả hai.
5. Xóa sạch các dấu marker (<<<<<<<, =======, >>>>>>>) và sửa lại code hoàn chỉnh.
6. Chạy thử kiểm tra cú pháp và chạy test đảm bảo ứng dụng không lỗi.
7. Chạy git add <file> để đánh dấu đã xử lý conflict, sau đó chạy git commit để kết thúc quá trình merge.

---

### Câu 33. Rebase là gì?

**📁 Vị trí code thực tế:** `Lệnh nâng cao Git CLI`

**Trả lời:**

• git rebase là thao tác chuyển các commit của nhánh hiện tại đặt nối tiếp lên trên commit mới nhất của nhánh gốc khác (định vị lại điểm xuất phát của nhánh).
• So sánh với Merge:
  - Merge: Tạo ra một commit gộp mới (Merge commit), giữ nguyên lịch sử rẽ nhánh nhưng biểu đồ git graph có thể rối rắm.
  - Rebase: Viết lại lịch sử commit thành một đường thẳng tắp, sạch đẹp, dễ theo dõi log.
• Lưu ý quan trọng (Quy tắc vàng của Rebase): Tuyệt đối KHÔNG rebase trên các nhánh công khai (như main hoặc nhánh có nhiều người cùng làm việc), vì rebase thay đổi hash commit, sẽ gây hỗn loạn lịch sử git cho người khác khi pull về.

---

## PHẦN IV: CHIẾN LƯỢC TRẢ LỜI & GỠ ĐIỂM TRONG BUỔI DEMO

### Mẹo 1: Trả lời theo cấu trúc kim tự tháp (Kết luận trước ➔ Lý giải ➔ Minh chứng)

**📁 Vị trí code thực tế:** `Kỹ năng thuyết trình BrSE`

**Trả lời:**

Khi người chấm hỏi, đừng mở đầu bằng những lời ấp úng dài dòng. Hãy đưa ngay kết luận ngắn gọn, sau đó giải thích lý do, và cuối cùng mở code/màn hình ra minh chứng.
Ví dụ: 'Dạ, trong dự án của em, Category và Product là quan hệ 1 - Nhiều ạ. Một danh mục có nhiều sản phẩm và một sản phẩm chỉ thuộc 1 danh mục. Em khai báo hasMany trong Category Model và belongsTo trong Product Model, đây là code ở dòng 40 Product.php ạ.'

---

### Mẹo 2: Điểm cộng sáng giá khi nói về Nghiệp vụ Bán hàng thực tế

**📁 Vị trí code thực tế:** `Tư duy nghiệp vụ Business Analyst / BrSE`

**Trả lời:**

---

### Mẹo 3: Cách trả lời câu hỏi tình huống Debug: "Em hãy nêu 1 bug thực tế đã gặp và cách em debug nó?"

**📁 Vị trí code thực tế:** `app/Models/Category.php (L25-L27), README.md (Mục 8 - Case 1)`

**Trả lời:**

1. **Nêu hiện tượng (Issue):** "Dạ thưa thầy/cô, trong quá trình phát triển chức năng Quản lý danh mục, em từng gặp lỗi khi submit form thêm mới danh mục thì hệ thống văng Exception đỏ `SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'name' cannot be null`."
2. **Quy trình debug (Investigation):**
   - Em đặt `dd($request->all())` ở Controller thì thấy form HTML vẫn gửi lên trường `name` bình thường.
   - Tiếp theo em bật `\DB::enableQueryLog()` để xem câu SQL Eloquent thực thi, thì thấy câu INSERT chỉ có `created_at` và `updated_at`, hoàn toàn bị mất cột `name`.
   - Em kiểm tra Model `Category.php` thì phát hiện ra mình chưa khai báo thuộc tính `'name'` vào mảng `protected $fillable`.
3. **Giải thích nguyên nhân cốt lõi (Root Cause):** "Nguyên nhân là do cơ chế **Mass Assignment Protection** của Laravel. Để ngăn chặn việc người dùng can thiệp dữ liệu hàng loạt nguy hiểm, Eloquent bắt buộc lập trình viên phải liệt kê các cột được phép gán trong `$fillable`. Do thiếu `'name'`, Eloquent đã tự động lọc bỏ trường này, dẫn đến MySQL nhận giá trị NULL vào cột bắt buộc NOT NULL và báo lỗi 1048."
4. **Cách khắc phục:** "Em thêm `'name'` vào `protected $fillable = ['name']` trong `Category.php`, test lại form thêm mới hoạt động mượt mà và lưu vào DB thành công ạ."


