<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý FAQ - Quản trị</title>
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


        .content { flex: 1; padding: 24px 28px; min-width: 0; }
        .content h1 { font-size: 22px; margin-bottom: 18px; }
        .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; background: var(--green-soft); color: var(--green-dark); border: 1px solid #bfe3cf; }
        .alert.error { background: #fdecec; color: var(--red); border-color: #f3c1c1; }

        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .toolbar form { display: flex; gap: 10px; flex-wrap: wrap; flex: 1; }
        .toolbar input[type=text] { flex: 1; min-width: 200px; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; }
        .toolbar select { padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #fafcfb; font-size: 13px; color: var(--muted); font-weight: 600; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fafcfb; }
        .title-cell { font-weight: 500; }
        .title-cell a { color: inherit; text-decoration: none; }
        .title-cell a:hover { color: var(--green-dark); text-decoration: underline; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge.approved { background: var(--green-soft); color: var(--green-dark); }
        .badge.pending { background: #fdf0e0; color: var(--orange); }
        .badge.draft { background: #eceff1; color: #607d8b; }

        .actions { display: flex; gap: 8px; }
        .actions form { display: inline; }
        .actions a, .actions button { width: 30px; height: 30px; display: grid; place-items: center; border: 1px solid var(--border); border-radius: 6px; background: #fff; cursor: pointer; text-decoration: none; font-size: 14px; }
        .actions a:hover { background: var(--green-soft); }
        .actions button:hover { background: #fdecec; }
        .empty { text-align: center; padding: 30px; color: var(--muted); }

        .pagination-wrap { display: flex; justify-content: center; margin-top: 18px; }
        .pagination-wrap svg { width: 16px; height: 16px; }

        @media (max-width: 800px) {
            .content { padding: 16px; }
        }
    </style>
       <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">
</head>
<body>
@php
    // ===== CHỈ CẦN SỬA Ở ĐÂY NẾU TÊN CỘT KHÁC =====
    $cotTenDanhMuc = 'ten_danh_muc';   // tên cột chứa tên danh mục trong bảng danh_muc

    // Nhãn trạng thái lấy từ hằng số Faq::TRANG_THAI trong model Faq
    $dsTrangThai = \App\Models\Faq::TRANG_THAI;

    $nhanTrangThai = function ($khoa) use ($dsTrangThai) {
        $v = $dsTrangThai[$khoa] ?? $khoa;
        if (is_array($v)) { $v = $v['label'] ?? $v['ten'] ?? reset($v); }
        return $v;
    };
    $lopTrangThai = function ($khoa) {
        if (in_array($khoa, ['da_duyet', 'xuat_ban', 'da_xuat_ban', 'published', 'approved'])) return 'approved';
        if ($khoa === 'nhap') return 'draft';
        return 'pending';
    };
@endphp
<div class="layout">

    {{-- ============ SIDEBAR QUẢN TRỊ ============ --}}
    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <h1>Quản lý FAQ</h1>

        @if (session('ok'))
            <div class="alert">{{ session('ok') }}</div>
        @endif
        @if (session('loi'))
            <div class="alert error">{{ session('loi') }}</div>
        @endif

        <div class="toolbar">
            <form method="GET" action="{{ url('/admin/faq') }}">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Tìm kiếm FAQ...">

                <select name="danh_muc" onchange="this.form.submit()">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($danhMucs as $dm)
                        <option value="{{ $dm->danh_muc_id }}" @selected(request('danh_muc') == $dm->danh_muc_id)>
                            {{ $dm->{$cotTenDanhMuc} }}
                        </option>
                    @endforeach
                </select>

                <select name="trang_thai" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    @foreach (collect($dsTrangThai)->except('nhap') as $khoa => $nhan)
                        <option value="{{ $khoa }}" @selected(request('trang_thai') === (string) $khoa)>{{ $nhanTrangThai($khoa) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn light">Lọc</button>
            </form>

            <a href="{{ route('cauhoi.create') }}" class="btn">+ Thêm FAQ</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px">STT</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Ngày cập nhật</th>
                        <th style="width:110px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faqs as $i => $faq)
                        <tr>
                            <td>{{ $faqs->firstItem() + $i }}</td>
                            <td class="title-cell">
                                <a href="{{ route('faqs.show', $faq->faq_id) }}">{{ $faq->cau_hoi }}</a>
                            </td>
                            <td>{{ $faq->danhMuc->{$cotTenDanhMuc} ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $lopTrangThai($faq->trang_thai) }}">{{ $nhanTrangThai($faq->trang_thai) }}</span>
                            </td>
                            <td>{{ $faq->ngay_cap_nhat ? \Carbon\Carbon::parse($faq->ngay_cap_nhat)->format('d/m/Y') : '—' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('faqs.show', $faq->faq_id) }}" title="Xem chi tiết">👁️</a>
                                    <form method="POST" action="{{ route('admin.faq.destroy', $faq) }}"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa FAQ này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Chưa có FAQ nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $faqs->withQueryString()->links('vendor.pagination.admin') }}
        </div>
    </main>
</div>
</body>
</html>