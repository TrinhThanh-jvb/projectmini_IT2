# TÀI LIỆU SƠ ĐỒ ERD & HỆ THỐNG (MINI SALES MANAGEMENT)

## 1. Sơ đồ thực thể quan hệ - ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    employees ||--|| employee_profiles : "1 - 1 (hasOne)"
    employees ||--o{ orders : "1 - N (creates)"
    categories ||--o{ products : "1 - N (categorizes)"
    orders ||--|{ order_items : "1 - N (contains)"
    products ||--|{ order_items : "1 - N (included in)"

    employees {
        bigint id PK
        string username UK
        string password
        string role "manager | sales_staff"
        string status "active | inactive"
        timestamp created_at
        timestamp updated_at
    }

    employee_profiles {
        bigint id PK
        bigint employee_id FK
        string name
        string email UK
        string phone
        string address
        string avatar
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        bigint category_id FK
        string name
        decimal price
        text description
        string image
        string status "Đang bán | Ngừng bán"
        timestamp created_at
        timestamp updated_at
    }

    orders {
        bigint id PK
        bigint employee_id FK
        string customer_name
        string customer_phone
        decimal total_amount
        string status "Pending | Processing | Completed | Cancelled"
        timestamp created_at
        timestamp updated_at
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        int quantity
        decimal price
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Sơ đồ System Flow (Luồng xử lý khi thực hiện CRUD)

```mermaid
sequenceDiagram
    autonumber
    actor User as Người dùng (Browser)
    participant Route as routes/web.php
    participant Controller as ProductController / OrderController
    participant Model as Eloquent Model
    participant DB as MySQL Database
    participant View as Blade View Template

    User->>Route: Gửi HTTP Request (GET / POST / PUT / DELETE)
    Route->>Controller: Điều hướng đến Controller & Action
    Controller->>Controller: Validate dữ liệu đầu vào (Form Request)
    Controller->>Model: Gọi hàm xử lý dữ liệu (ORM Query / Save)
    Model->>DB: Thực thi truy vấn SQL (SELECT, INSERT, UPDATE, DELETE)
    DB-->>Model: Trả về tập dữ liệu bản ghi
    Model-->>Controller: Trả về Eloquent Collection / Object
    Controller->>View: Truyền dữ liệu sang View (compact/with)
    View-->>Controller: Biên dịch Blade Template thành mã HTML thuần
    Controller-->>User: Phản hồi HTTP Response (200 OK / Redirect) kèm giao diện hiển thị
```

---

## 3. Sơ đồ quy trình Deploy Server (Deploy Flow)

```mermaid
graph TD
    A[Khách hàng nhập Domain minisales.com] --> B[DNS Server phân giải thành IP máy chủ]
    B --> C[Nginx Web Server: Cổng 80/443 SSL]
    C -->|Static Files: CSS, JS, Images| D[Thư mục public/storage]
    C -->|Dynamic PHP Request qua FastCGI| E[PHP-FPM Pool: Cổng 9000 hoặc Unix Socket]
    E --> F[Laravel Core: public/index.php]
    F --> G[Xử lý Routing, Middleware, Controller]
    G --> H[("MySQL Database Server: Cổng 3306")]
    H --> G
    G --> E
    E --> C
    C --> A
```
