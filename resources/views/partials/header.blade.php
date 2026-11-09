@php
    // Danh mục cho menu: lấy trực tiếp từ bảng danh_muc (thêm / sửa trong CSDL là menu tự cập nhật)
    try {
        $menuCategories = \Illuminate\Support\Facades\DB::table('danh_muc')
            ->where('trang_thai', 1)
            ->where('slug', '!=', 'khac')
            ->orderBy('danh_muc_id')
            ->select('ten_danh_muc', 'slug')
            ->get();
    } catch (\Throwable $e) {
        $menuCategories = collect();
    }

    // Ảnh đại diện: ưu tiên avatar_url, nếu không có thì dùng cột anh_dai_dien
    $hdUser = auth()->user();
    $hdAnh  = null;
    if ($hdUser) {
        $hdAnh = $hdUser->avatar_url
            ?: (!empty($hdUser->anh_dai_dien) ? asset('storage/' . ltrim($hdUser->anh_dai_dien, '/')) : null);
    }
@endphp

{{-- Giữ nút Đăng xuất luôn nằm trong màn hình, tên dài thì cắt "..." --}}
<style>
    .site-header .header-inner { flex-wrap: nowrap; gap: 14px; min-width: 0; }
    .site-header .brand, .site-header .main-nav { flex-shrink: 0; }
    .site-header .header-search { flex: 0 1 220px; min-width: 90px; }
    .site-header .header-search input { width: 100%; box-sizing: border-box; }

    .site-header .header-auth { display: flex; align-items: center; gap: 10px; flex-shrink: 0; margin-left: auto; }
    .site-header .auth-name { display: flex; align-items: center; gap: 10px; min-width: 0; max-width: 150px; }
    .site-header .auth-name-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .site-header .auth-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; display: block; }
    .site-header .header-auth form { margin: 0; flex-shrink: 0; }
    .site-header .btn-auth { white-space: nowrap; flex-shrink: 0; }

    /* Ảnh đại diện tròn; chưa có ảnh thì hiện chữ cái đầu trên nền gradient */
    .site-header .auth-avatar-box { width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; overflow: hidden;
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; color: #1b2a1b;
        background: linear-gradient(90deg, #a6e22e, #f1d62a); }
    .site-header .auth-avatar-box .auth-avatar { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }

    @media (max-width: 1350px) {
        .site-header .header-search { display: none; }
    }
    @media (max-width: 1200px) {
        .site-header .auth-name { max-width: 120px; }
    }
    @media (max-width: 1050px) {
        .site-header .auth-name-text { display: none; }
    }
</style>

{{-- ===== THANH TRÊN CÙNG ===== --}}
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-social">
            <a href="https://www.facebook.com/tvuCET" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
            <a href="https://youtube.com/@TruongKythuatCongnghe_TVU_CET" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        </div>
        <div class="topbar-contact">
            <span><i class="fa-solid fa-phone"></i> +84 2943 855 246 (135)</span>
            <span><i class="fa-solid fa-mobile-screen"></i> +84 2943 855 246 (203)</span>
            <a href="mailto:ktcn@tvu.edu.vn"><i class="fa-regular fa-envelope"></i> ktcn@tvu.edu.vn</a>
        </div>
    </div>
</div>

{{-- ===== THANH MENU ===== --}}
<header class="site-header">
    <div class="container header-inner">
        <button type="button" id="btnMenu" class="hamburger" aria-label="Mở menu">
            <span></span><span></span><span></span>
        </button>

        <a href="{{ url('/') }}" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
            <span>FAQ Khoa CNTT</span>
        </a>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a></li>

                <li class="has-sub">
                    <a href="{{ route('faqs.index') }}">Danh mục <i class="fa-solid fa-chevron-down"></i></a>
                    <ul class="sub-menu">
                        @forelse ($menuCategories as $dm)
                            <li>
                                <a href="{{ route('faqs.index', ['danh_muc' => $dm->slug]) }}"
                                   class="{{ request('danh_muc') === $dm->slug ? 'active' : '' }}">{{ $dm->ten_danh_muc }}</a>
                            </li>
                        @empty
                            <li><a href="{{ route('faqs.index') }}">Tất cả câu hỏi</a></li>
                        @endforelse
                    </ul>
                </li>

                <li><a href="{{ url('/faqs') }}" class="{{ request()->is('faqs*') ? 'active' : '' }}">Danh sách FAQ</a></li>
                <li><a href="{{ url('/gui-cau-hoi') }}" class="{{ request()->is('gui-cau-hoi*') ? 'active' : '' }}">Gửi câu hỏi</a></li>
            </ul>
        </nav>

        {{-- Ô tìm kiếm --}}
        <form class="header-search" action="{{ url('/faqs') }}" method="GET">
            <input type="text" name="q" placeholder="Search ..." value="{{ request('q') }}">
        </form>

        {{-- Đăng nhập / Đăng xuất: luôn ở góc phải --}}
        <div class="header-auth">
            @auth
                {{-- Chuông dùng chung mọi vai trò: câu hỏi được duyệt, có người trả lời, câu hỏi gửi đến --}}
                @include('partials.chuong-thong-bao')

                {{-- Ảnh đại diện + tên: giảng viên vào trang tổng quan, người khác vào hồ sơ cá nhân --}}
                <a href="{{ $hdUser->vai_tro === 'giang_vien' ? route('giangvien.dashboard') : route('profile.edit') }}"
                   class="auth-name" title="{{ $hdUser->name }}">
                    <span class="auth-avatar-box">
                        @if ($hdAnh)
                            <img src="{{ $hdAnh }}" alt="" class="auth-avatar" onerror="this.remove()">
                        @else
                            {{ mb_strtoupper(mb_substr($hdUser->name, 0, 1)) }}
                        @endif
                    </span>
                    <span class="auth-name-text">{{ $hdUser->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-auth">Đăng xuất</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-auth">Đăng nhập</a>
            @endauth
        </div>
    </div>
</header>

<script>
    document.getElementById('btnMenu').addEventListener('click', function () {
        document.getElementById('mainNav').classList.toggle('open');
    });
</script>