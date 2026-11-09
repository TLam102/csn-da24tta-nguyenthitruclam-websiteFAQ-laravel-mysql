<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard quản trị</title>
    <style>
        :root {
            --green: #2f9e6e;
            --green-dark: #247a55;
            --green-soft: #e6f4ec;
            --bg: #f4f7f5;
            --border: #dfe6e2;
            --text: #1f2d27;
            --muted: #6b7a73;
            --red: #d64545;
            --orange: #e8943a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }
        .layout { display: flex; min-height: 100vh; }

        /* ===== Nội dung ===== */
        .content { flex: 1; min-width: 0; padding: 28px 32px 40px; }
        .page { max-width: 1040px; margin: 0 auto; }

        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 22px; flex-wrap: wrap; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .sub { color: var(--muted); margin-top: 6px; font-size: 13px; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 42px; padding: 0 22px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
        .btn:hover { background: var(--green-dark); }

        .section-title { font-size: 16px; font-weight: 700; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
        .section-title:before { content: ''; width: 4px; height: 16px; background: var(--green); border-radius: 2px; }

        /* ===== 4 thẻ số liệu ===== */
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 30px; }
        .stat { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 4px; padding: 22px 16px 18px; background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); color: var(--text); text-decoration: none; transition: transform .15s, box-shadow .15s, border-color .15s; }
        .stat:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(31, 45, 39, .08); border-color: var(--green); }
        .stat .icon { width: 46px; height: 46px; border-radius: 12px; background: var(--green-soft); display: grid; place-items: center; font-size: 22px; margin-bottom: 6px; }
        .stat .num { font-size: 30px; font-weight: 700; line-height: 1.1; }
        .stat .lbl { color: var(--muted); font-size: 13px; }
        .stat.warn .icon { background: #fdf0e0; }
        .stat.warn .num { color: var(--orange); }
        .stat .go { margin-top: 8px; font-size: 12px; color: var(--green-dark); font-weight: 600; }

        /* ===== Hoạt động gần đây ===== */
        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 18px; text-align: left; border-bottom: 1px solid var(--border); vertical-align: middle; }
        th { background: #fafcfb; font-size: 13px; color: var(--muted); font-weight: 600; white-space: nowrap; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fafcfb; }
        .time { color: var(--muted); white-space: nowrap; }
        .act a { color: var(--text); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .act a:hover { color: var(--green-dark); text-decoration: underline; }
        .empty { text-align: center; padding: 40px; color: var(--muted); }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .badge.approved { background: var(--green-soft); color: var(--green-dark); }
        .badge.pending { background: #fdf0e0; color: var(--orange); }
        .badge.draft { background: #eceff1; color: #607d8b; }
        .badge.rejected { background: #fdecec; color: var(--red); }

        @media (max-width: 900px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .content { padding: 16px; }
            th, td { padding: 11px 12px; }
        }
        @media (max-width: 480px) {
            .stats { grid-template-columns: 1fr; }
        }
    </style>
       <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">
</head>
<body>
@php
    $nhanFaq = function ($khoa) use ($dsTrangThai) {
        $v = $dsTrangThai[$khoa] ?? $khoa;
        if (is_array($v)) { $v = $v['label'] ?? $v['ten'] ?? reset($v); }
        return $v;
    };

    // Trả về [nhãn, class màu] cho cột "Trạng thái" của một dòng hoạt động
    $trangThai = function (array $h) use ($nhanFaq) {
        $k = (string) $h['trang_thai'];

        if ($h['loai'] === 'faq') {
            if ($k === 'nhap') return [$nhanFaq($k), 'draft'];
            if (in_array($k, ['da_duyet', 'xuat_ban', 'da_xuat_ban', 'published', 'approved'], true)) return [$nhanFaq($k), 'approved'];
            return [$nhanFaq($k), 'pending'];
        }

        // câu hỏi
        if (in_array($k, ['cho_duyet', 'chua_duyet', 'chua_xu_ly', 'moi', 'pending', 'Chờ duyệt', 'chờ duyệt', 'cho duyet'], true)) return ['Chờ duyệt', 'pending'];
        if ($k === 'tu_choi') return ['Từ chối', 'rejected'];
        if ($k === 'da_xu_ly') return ['Đã xử lý', 'approved'];
        if ($k === 'da_tra_loi') return ['Đã trả lời', 'draft'];
        return [$k ?: '—', 'draft'];
    };

    $tenAdmin = optional(auth()->user())->name;
@endphp
<div class="layout">

    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <div class="page">

            <div class="page-head">
                <div>
                    <h1>Dashboard quản trị</h1>
                    <div class="sub">Xin chào{{ $tenAdmin ? ', ' . $tenAdmin : '' }}. Đây là tổng quan hệ thống FAQ Khoa CNTT.</div>
                </div>
                <a href="{{ route('cauhoi.create') }}" class="btn">+ Thêm FAQ</a>
            </div>

            {{-- ===== Tổng quan ===== --}}
            <h2 class="section-title">Tổng quan</h2>
            <div class="stats">
                <a class="stat" href="{{ url('/admin/faq') }}">
                    <div class="icon">📋</div>
                    <div class="num">{{ number_format($soLieu['faq']) }}</div>
                    <div class="lbl">Tổng số FAQ</div>
                    <div class="go">Quản lý FAQ →</div>
                </a>
                <a class="stat {{ $soLieu['cho_duyet'] ? 'warn' : '' }}" href="{{ url('/admin/cau-hoi') }}">
                    <div class="icon">🕒</div>
                    <div class="num">{{ number_format($soLieu['cho_duyet']) }}</div>
                    <div class="lbl">Câu hỏi chờ duyệt</div>
                    <div class="go">Xem danh sách →</div>
                </a>
                <a class="stat" href="{{ url('/admin/danh-muc') }}">
                    <div class="icon">📁</div>
                    <div class="num">{{ number_format($soLieu['danh_muc']) }}</div>
                    <div class="lbl">Danh mục</div>
                    <div class="go">Quản lý danh mục →</div>
                </a>
            </div>

            {{-- ===== Hoạt động gần đây ===== --}}
            <h2 class="section-title">Hoạt động gần đây</h2>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th style="width:150px">Thời gian</th>
                            <th style="width:180px">Người dùng</th>
                            <th>Hoạt động</th>
                            <th style="width:120px">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hoatDongs as $h)
                            @php [$nhan, $lop] = $trangThai($h); @endphp
                            <tr>
                                <td class="time">{{ $h['thoi_gian'] ? $h['thoi_gian']->format('d/m/Y H:i') : '—' }}</td>
                                <td>{{ $h['nguoi'] }}</td>
                                <td class="act"><a href="{{ $h['lien_ket'] }}">{{ $h['hoat_dong'] }}</a></td>
                                <td><span class="badge {{ $lop }}">{{ $nhan }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Chưa có hoạt động nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>s