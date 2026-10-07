<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Mini Sales Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto; width: 50px; height: 50px; font-size: 24px;">📦</div>
            <h1>Đăng nhập hệ thống</h1>
            <p>Mini Sales Management - BRSE Project</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập (Username)</label>
                <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Nhập username..." required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu (Password)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="font-size: 13px; color: var(--text-muted); cursor: pointer;">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                Đăng nhập ngay 🚀
            </button>
        </form>

        <div class="demo-account-box">
            <strong>Tài khoản thử nghiệm:</strong>
            <div style="margin-top: 6px; display: flex; flex-direction: column; gap: 4px;">
                <div>👑 <strong>Quản lý:</strong> <code>admin</code> / <code>password123</code></div>
                <div>💼 <strong>Nhân viên bán hàng:</strong> <code>sales1</code> / <code>password123</code></div>
                <div>💼 <strong>Nhân viên bán hàng:</strong> <code>sales2</code> / <code>password123</code></div>
            </div>
        </div>
    </div>
</body>
</html>
