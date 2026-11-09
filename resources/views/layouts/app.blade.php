<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Trang chủ') - FAQ Khoa CNTT</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- ?v=... đổi theo thời điểm sửa file, nên trình duyệt luôn nạp bản CSS mới nhất --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    @stack('styles')
</head>
<body>
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Popup thông báo cho giảng viên (chỉ hiện 1 lần mỗi lần đăng nhập) --}}
    @if (!empty($gvShowNotice))
        <div id="gvNotice" class="gv-notice">
            <div class="gv-notice-icon"><i class="fa-solid fa-bell"></i></div>
            <div class="gv-notice-body">
                <strong>Bạn có {{ $gvUnread }} câu hỏi mới</strong>
                <p>Sinh viên đã gửi câu hỏi trực tiếp đến bạn.</p>
                <a href="{{ route('giangvien.cauhoi.index') }}" class="gv-notice-btn">Xem ngay</a>
            </div>
            <button type="button" class="gv-notice-close" aria-label="Đóng"
                    onclick="document.getElementById('gvNotice').remove()">&times;</button>
        </div>
        <script>
            setTimeout(function () {
                var el = document.getElementById('gvNotice');
                if (el) el.remove();
            }, 15000);
        </script>
    @endif

    @stack('scripts')
</body>
</html>