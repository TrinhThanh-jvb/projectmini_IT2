{{-- 
    View: Đăng nhập hệ thống (Login page).
    Cho phép nhân viên và quản trị viên nhập thông tin tài khoản (username, password)
    để xác thực và truy cập vào ứng dụng.
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - DIOR BEAUTY</title>
    {{-- Tải file CSS định dạng giao diện chung --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <div class="auth-card">
        {{-- Tiêu đề và biểu tượng thương hiệu trên form đăng nhập --}}
        <div class="auth-header">
            <div class="brand-icon">✦</div>
            <h1>DIOR BEAUTY</h1>
            <p>Chào mừng bạn đến với hệ thống quản lý bán hàng Dior</p>
        </div>

        {{-- Hiển thị thông báo thành công (ví dụ: sau khi vừa đăng xuất) --}}
        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Hiển thị thông báo lỗi xác thực (sai mật khẩu, tài khoản bị khóa, v.v.) --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Form gửi yêu cầu POST xác thực tới AuthController::login --}}
        <form action="{{ route('login') }}" method="POST">
            {{-- Token CSRF bắt buộc của Laravel để chống giả mạo request --}}
            @csrf
            
            {{-- Ô nhập tên đăng nhập (Username) --}}
            <div class="form-group">
                <label for="username" class="form-label">Tên đăng nhập (Username)</label>
                <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Nhập username..." required autofocus>
            </div>

            {{-- Ô nhập mật khẩu (Password) --}}
            <div class="form-group">
                <label for="password" class="form-label">Mật khẩu (Password)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            {{-- Tùy chọn lưu phiên đăng nhập lâu dài (Remember Me) --}}
            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="font-size: 13px; color: var(--text-muted); cursor: pointer;">Ghi nhớ đăng nhập</label>
            </div>

            {{-- Nút bấm thực hiện đăng nhập --}}
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                Đăng nhập ngay 🚀
            </button>
        </form>

        {{-- Hộp thông tin tài khoản mẫu phục vụ việc thử nghiệm hệ thống nhanh chóng --}}
        <!-- Use Ctrl + K + C to comment out -->
        <!-- Use Ctrl + K + U to uncomment -->
        <!-- <div class="demo-account-box">
            <strong>Tài khoản thử nghiệm:</strong>
            <div style="margin-top: 6px; display: flex; flex-direction: column; gap: 4px;">
                <div>👑 <strong>Quản lý:</strong> <code>admin</code> / <code>password123</code></div>
                <div>💼 <strong>Nhân viên bán hàng:</strong> <code>sales1</code> / <code>password123</code></div>
                <div>💼 <strong>Nhân viên bán hàng:</strong> <code>sales2</code> / <code>password123</code></div>
            </div>
        </div> -->
    </div>
</body>
</html>
