{{-- 
    Layout chính (Master Layout) cho toàn bộ ứng dụng Mini Sales Management.
    Cung cấp cấu trúc khung trang bao gồm:
    - Sidebar điều hướng bên trái (Navigation Bar với phân quyền hiển thị theo Role)
    - Thanh tiêu đề trên cùng (Top Navbar với nút Profile & Logout)
    - Khu vực hiển thị thông báo trạng thái (Alert Success / Error / Validation Errors)
    - Vùng nội dung động (@yield('content'))
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Token bảo vệ chống tấn công CSRF cho các yêu cầu AJAX nếu có --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini Sales Management') - Quản lý bán hàng</title>
    {{-- Tải file giao diện CSS dùng chung của hệ thống --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-layout">
        {{-- ================= THANH ĐIỀU HƯỚNG BÊN TRÁI (SIDEBAR) ================= --}}
        <aside class="app-sidebar">
            {{-- Logo và tên ứng dụng Dior Beauty --}}
            <div class="sidebar-header">
                <div class="brand-icon">✦</div>
                <div>
                    <div class="brand-title">DIOR BEAUTY</div>
                    <div class="brand-subtitle">Luxury Sales System</div>
                </div>
            </div>

            {{-- Danh mục các menu chức năng --}}
            <nav class="sidebar-nav">
                <div class="nav-section-title">Tổng quan</div>
                {{-- Trang Dashboard thống kê --}}
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">✨</span>
                    <span>Dashboard</span>
                </a>

                <div class="nav-section-title">Nghiệp vụ bán hàng</div>
                {{-- Quản lý danh mục hàng hóa (Được đưa lên trên Quản lý sản phẩm) --}}
                <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏷️</span>
                    <span>Quản lý danh mục</span>
                </a>

                {{-- Quản lý sản phẩm --}}
                <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="nav-icon">💄</span>
                    <span>Quản lý sản phẩm</span>
                </a>

                {{-- Quản lý đơn đặt hàng --}}
                <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <span class="nav-icon">🛍️</span>
                    <span>Quản lý đơn hàng</span>
                </a>

                {{-- Menu chỉ hiển thị khi người dùng có vai trò là Quản lý (Manager) --}}
                @if(auth()->user()->isManager())
                    <div class="nav-section-title">Quản trị viên</div>
                    {{-- Quản lý tài khoản nhân viên --}}
                    <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span>Quản lý nhân viên</span>
                    </a>
                @endif

                <div class="nav-section-title">Cá nhân</div>
                {{-- Trang thông tin cá nhân của người dùng hiện tại --}}
                <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span>
                    <span>Hồ sơ cá nhân</span>
                </a>
            </nav>

            {{-- Thông tin tóm tắt của người dùng ở đáy Sidebar --}}
            <div class="sidebar-footer">
                <div class="user-snippet">
                    <div class="user-avatar">
                        {{-- Hiển thị ảnh đại diện hoặc lấy 2 ký tự đầu username nếu chưa có ảnh --}}
                        @if(auth()->user()->profile && auth()->user()->profile->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->profile->avatar) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->profile->name ?? auth()->user()->username }}</div>
                        <span class="user-role-badge">
                            {{ auth()->user()->isManager() ? 'Quản lý' : 'Nhân viên bán hàng' }}
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ================= KHU VỰC NỘI DUNG CHÍNH (MAIN CONTENT) ================= --}}
        <main class="app-main">
            {{-- Top Navbar: Thanh tiêu đề trang và các nút tác vụ nhanh --}}
            <header class="top-navbar">
                <h1 class="page-headline">@yield('page_title', 'Tổng quan')</h1>
                <div class="navbar-actions">
                    <a href="{{ route('profile') }}" class="btn btn-secondary btn-sm">
                        <span>👤 Hồ sơ</span>
                    </a>
                    {{-- Form gửi request POST đăng xuất khỏi hệ thống --}}
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                            <span>🚪 Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Vùng chứa nội dung chi tiết & thông báo --}}
            <div class="content-body">
                {{-- Thông báo thành công từ Flash Session --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Thông báo lỗi từ Flash Session --}}
                @if(session('error'))
                    <div class="alert alert-danger">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Hiển thị danh sách các lỗi Validate form nếu có --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        <span>⚠️</span>
                        <div>
                            <strong>Vui lòng kiểm tra lại:</strong>
                            <ul style="margin-left: 20px; margin-top: 4px;">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Placeholder chứa nội dung của từng trang con kế thừa layout này --}}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Vị trí nhúng các đoạn mã JavaScript bổ sung từ các view con --}}
    @stack('scripts')
</body>
</html>
