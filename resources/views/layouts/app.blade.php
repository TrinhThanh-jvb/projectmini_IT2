<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini Sales Management') - Quản lý bán hàng</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar">
            <div class="sidebar-header">
                <div class="brand-icon">📦</div>
                <div>
                    <div class="brand-title">MiniSales</div>
                    <div class="brand-subtitle">BRSE Final Project</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Hệ thống</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>

                <div class="nav-section-title">Nghiệp vụ bán hàng</div>
                <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="nav-icon">📱</span>
                    <span>Quản lý sản phẩm</span>
                </a>

                <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <span class="nav-icon">📂</span>
                    <span>Quản lý danh mục</span>
                </a>

                <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <span class="nav-icon">🛒</span>
                    <span>Quản lý đơn hàng</span>
                </a>

                @if(auth()->user()->isManager())
                    <div class="nav-section-title">Quản trị viên</div>
                    <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span>Quản lý nhân viên</span>
                    </a>
                @endif

                <div class="nav-section-title">Cá nhân</div>
                <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span>
                    <span>Hồ sơ cá nhân</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-snippet">
                    <div class="user-avatar">
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

        <!-- Main Content Area -->
        <main class="app-main">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <h1 class="page-headline">@yield('page_title', 'Tổng quan')</h1>
                <div class="navbar-actions">
                    <a href="{{ route('profile') }}" class="btn btn-secondary btn-sm">
                        <span>👤 Hồ sơ</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                            <span>🚪 Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

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

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
