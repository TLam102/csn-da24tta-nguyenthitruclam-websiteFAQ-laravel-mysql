{{--
    SIDEBAR DÙNG CHUNG CHO CÁC TRANG QUẢN TRỊ
    Cách dùng trong mỗi trang:  @include('admin.partials.sidebar')
    Bảng màu xanh lá – vàng – xanh đen được khai báo ngay trong sidebar (phạm vi .sidebar),
    nên KHÔNG phụ thuộc vào :root của từng trang và luôn đồng bộ với trang người dùng.
--}}
<style>
    .sidebar {
        /* Màu riêng của sidebar (ghi đè :root của trang chứa nó) */
        --green: #2C8419;
        --green-dark: #1F6612;
        --green-soft: #EAF9CF;
        --lime: #8CD41C;
        --accent: #FFD21F;
        --ink: #0F2A30;
        --border: #E1E8DC;
        --text: #0F2A30;
        --muted: #5F6F73;
        --red: #dc2626;

        width: 240px;
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FCEF 100%);
        border-right: 3px solid;
        border-image: linear-gradient(180deg, var(--lime), var(--accent)) 1;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        position: sticky;
        top: 0;
        height: 100vh;
        box-shadow: 4px 0 16px rgba(15, 42, 48, .06);
    }

    /* ----- Phần logo ----- */
    .sidebar .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 18px;
        font-weight: 800;
        font-size: 15px;
        color: var(--green-dark);
        border-bottom: 3px solid;
        border-image: linear-gradient(90deg, var(--lime), var(--accent)) 1;
    }
    .sidebar .brand .logo {
        width: 38px;
        height: 38px;
        object-fit: contain;
        border-radius: 50%;
        flex-shrink: 0;
        transition: transform .4s ease;
    }
    .sidebar .brand:hover .logo { transform: rotate(-8deg) scale(1.08); }

    /* ----- Menu ----- */
    .sidebar nav { flex: 1; padding: 14px 10px; overflow-y: auto; }
    .sidebar nav a {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        margin-bottom: 4px;
        border-radius: 10px;
        color: var(--text);
        text-decoration: none;
        transition: background .2s, color .2s, transform .2s, box-shadow .2s;
    }
    .sidebar nav a span:first-child { width: 22px; text-align: center; transition: transform .25s; }
    .sidebar nav a:hover {
        background: var(--green-soft);
        color: var(--green-dark);
        transform: translateX(4px);
    }
    .sidebar nav a:hover span:first-child { transform: scale(1.25) rotate(-6deg); }

    /* Mục đang mở: nền xanh lá nhạt + thanh vàng bên trái */
    .sidebar nav a.active {
        background: linear-gradient(90deg, var(--green-soft), #FFFBE0);
        color: var(--green-dark);
        font-weight: 700;
        box-shadow: inset 4px 0 0 var(--accent), 0 4px 10px rgba(140, 212, 28, .20);
    }
    .sidebar nav a.active span:first-child { animation: sbBounce 2s ease-in-out infinite; }
    .sidebar nav a .ext { margin-left: auto; color: var(--muted); font-size: 12px; }

    .sidebar .nav-title {
        margin: 16px 12px 8px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ----- Đăng xuất ----- */
    .sidebar .logout { padding: 12px 10px; border-top: 1px solid var(--border); }
    .sidebar .logout button {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        background: none;
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
        font-family: inherit;
        color: var(--text);
        transition: background .2s, color .2s, transform .2s;
    }
    .sidebar .logout button span:first-child { width: 22px; text-align: center; }
    .sidebar .logout button:hover { background: #fdecec; color: var(--red); transform: translateX(4px); }

    .sidebar a:focus-visible,
    .sidebar button:focus-visible { outline: 3px solid var(--accent); outline-offset: 2px; }

    @keyframes sbBounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }

    @media (prefers-reduced-motion: reduce) {
        .sidebar *, .sidebar { animation: none !important; transition-duration: .01ms !important; }
    }

    @media (max-width: 800px) {
        .sidebar { width: 64px; }
        .sidebar .label, .sidebar .brand .name { display: none; }
    }
</style>

<aside class="sidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <span class="name">FAQ Admin</span>
    </div>

    <nav>
        <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
            <span>🏠</span><span class="label">Dashboard</span>
        </a>
        <a href="{{ url('/admin/faq') }}" class="{{ request()->is('admin/faq*') ? 'active' : '' }}">
            <span>📋</span><span class="label">Quản lý FAQ</span>
        </a>
        <a href="{{ url('/admin/danh-muc') }}" class="{{ request()->is('admin/danh-muc*') ? 'active' : '' }}">
            <span>📁</span><span class="label">Quản lý danh mục</span>
        </a>
        
        <a href="{{ url('/admin/cau-hoi') }}" class="{{ request()->is('admin/cau-hoi*') ? 'active' : '' }}">
            <span>❓</span><span class="label">Câu hỏi chờ duyệt</span>
        </a>

        {{-- Quản trị kế thừa chức năng người dùng: mở trang người dùng ở tab mới, không cần đăng xuất --}}
        <div class="nav-title label">Giao diện người dùng</div>
        <a href="{{ route('home') }}" target="_blank" rel="noopener">
            <span>🌐</span><span class="label">Trang chủ</span><span class="label ext">↗</span>
        </a>
        <a href="{{ route('faqs.index') }}" target="_blank" rel="noopener">
            <span>🔎</span><span class="label">Danh sách FAQ</span><span class="label ext">↗</span>
        </a>
        <a href="{{ route('cauhoi.create') }}" target="_blank" rel="noopener">
            <span>✉️</span><span class="label">Gửi câu hỏi</span><span class="label ext">↗</span>
        </a>
    </nav>

    <div class="logout">
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit"><span>🚪</span><span class="label">Đăng xuất</span></button>
        </form>
    </div>
</aside>