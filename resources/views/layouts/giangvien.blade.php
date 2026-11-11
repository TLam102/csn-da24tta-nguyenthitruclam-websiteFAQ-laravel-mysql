<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Giảng viên') - FAQ Khoa CNTT</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ @filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/giangvien.css') }}?v={{ @filemtime(public_path('css/giangvien.css')) }}">

    {{-- Ảnh đại diện nằm gọn trong hình tròn ở thanh menu --}}
    <style>
        .gvx-avatar { overflow: hidden; }
        .gvx-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block; }
    </style>
    @stack('styles')
</head>
<body class="gvx-body">
@php
    $unread  = $stats['moi'] ?? 0;
    $me      = auth()->user();
   $meAnh = file_url($me->anh_dai_dien);
    $laAdmin = in_array($me->vai_tro ?? '', \App\Http\Middleware\QuanTriVien::VAI_TRO, true);
@endphp

{{-- ===== Thanh menu trên ===== --}}
<header class="gvx-top">
    <a href="{{ route('giangvien.dashboard') }}" class="gvx-brand">
        <img src="{{ asset('images/logo.png') }}" alt="" class="gvx-brand-logo" onerror="this.style.display='none'">
        Giảng viên <span>FAQ Khoa CNTT</span>
    </a>

        <nav class="gvx-nav">
        <a href="{{ url('/') }}"><i class="fa-solid fa-globe"></i> Trang chủ</a>
        <a href="{{ route('faqs.index') }}"><i class="fa-solid fa-magnifying-glass"></i> Danh sách FAQ</a>
        @if ($laAdmin)
            <a href="{{ route('admin.faq.index') }}"><i class="fa-solid fa-pen-to-square"></i> Quản lý FAQ</a>
        @endif
    </nav>

    <div class="gvx-top-right">
        <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'moi']) }}" class="gvx-bell" title="Câu hỏi mới">
            <i class="fa-solid fa-bell"></i>
            @if ($unread > 0)<em>{{ $unread > 99 ? '99+' : $unread }}</em>@endif
        </a>

        {{-- Menu tài khoản xổ xuống --}}
        <div class="gvx-account" id="gvxAccount">
            <button type="button" class="gvx-account-btn" id="gvxAccountBtn" aria-haspopup="true">
                <span class="gvx-avatar">
                    @if ($meAnh)
                        <img src="{{ $meAnh }}" alt="" onerror="this.remove()">
                    @else
                        {{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}
                    @endif
                </span>
                <span class="gvx-account-name">{{ $me->name }}</span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="gvx-menu" id="gvxMenu">
                <div class="gvx-menu-head">
                    <strong>{{ $me->name }}</strong>
                    <span>{{ $me->email }}</span>
                </div>
                <a href="{{ route('profile.edit') }}"><i class="fa-regular fa-user"></i> Hồ sơ cá nhân</a>
                <a href="{{ route('profile.password.edit') }}"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a>
                <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> Về trang chủ</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ===== Nội dung ===== --}}
<main class="gvx-main">

    @hasSection('gv_breadcrumb')
        <div class="gvx-crumb">
            <a href="{{ route('giangvien.dashboard') }}">Giảng viên</a>
            <i class="fa-solid fa-chevron-right"></i> @yield('gv_breadcrumb')
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    @yield('gv_content')
    @include('partials.footer')
</main>

<script>
    (function () {
        var btn = document.getElementById('gvxAccountBtn');
        var box = document.getElementById('gvxAccount');
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            box.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!box.contains(e.target)) box.classList.remove('open');
        });
    })();
</script>
@stack('scripts')
</body>
</html>