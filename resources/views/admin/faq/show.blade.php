<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi tiết FAQ - Quản trị</title>
    <style>
        :root {
            --green: #2f9e6e; --green-dark: #247a55; --green-soft: #e6f4ec;
            --bg: #f4f7f5; --border: #dfe6e2; --text: #1f2d27; --muted: #6b7a73;
            --red: #d64545; --orange: #e8943a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }
        .layout { display: flex; min-height: 100vh; }
        .content { flex: 1; min-width: 0; padding: 28px 32px 40px; }
        .page { max-width: 980px; margin: 0 auto; }

        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .breadcrumb { color: var(--muted); margin-top: 6px; font-size: 13px; }
        .breadcrumb a { color: var(--green-dark); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .head-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .btn { display: inline-flex; align-items: center; justify-content: center; height: 40px; padding: 0 20px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); font-weight: 500; }
        .btn.light:hover { background: var(--bg); }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31,45,39,.05); overflow: hidden; margin-bottom: 18px; }
        .section { padding: 24px 28px; }
        .section-title { font-size: 15px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .section-title:before { content: ''; width: 4px; height: 16px; background: var(--green); border-radius: 2px; }

        .q-title { font-size: 22px; line-height: 1.4; margin-bottom: 14px; }
        .meta { display: flex; flex-wrap: wrap; gap: 10px 26px; color: var(--muted); font-size: 13px; padding-top: 14px; border-top: 1px solid var(--border); }
        .meta b { color: var(--text); font-weight: 600; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge.approved { background: var(--green-soft); color: var(--green-dark); }
        .badge.pending { background: #fdf0e0; color: var(--orange); }
        .badge.draft { background: #eceff1; color: #607d8b; }

        .answer { line-height: 1.75; font-size: 15px; word-wrap: break-word; }
        .answer ul, .answer ol { margin: 6px 0 6px 24px; }
        .answer p { margin-bottom: 8px; }
        .empty-answer { color: var(--muted); font-style: italic; }

        .tags { display: flex; flex-wrap: wrap; gap: 8px; }
        .tag { display: inline-flex; align-items: center; height: 30px; padding: 0 14px; border-radius: 15px; background: var(--green-soft); color: var(--green-dark); font-weight: 600; font-size: 13px; }
        .files div { margin-bottom: 6px; }
        .files a { color: var(--green-dark); text-decoration: none; }
        .files a:hover { text-decoration: underline; }
        .muted { color: var(--muted); }

        .reply { padding: 14px 16px; border: 1px solid var(--border); border-radius: 10px; background: #fafcfb; margin-bottom: 10px; }
        .reply:last-child { margin-bottom: 0; }
        .reply-head { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 6px; font-size: 13px; }
        .reply-head span { color: var(--muted); }

        @media (max-width: 800px) {
            .content { padding: 16px; }
            .section { padding: 18px; }
        }
    </style>
    @if (file_exists(public_path('css/admin-theme.css')))
        <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">
    @endif
</head>
<body>
@php
    $cotTenDanhMuc = 'ten_danh_muc';
    $dsTrangThai   = \App\Models\Faq::TRANG_THAI;

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
    $dinhDang = fn ($t) => $t ? \Carbon\Carbon::parse($t)->format('d/m/Y H:i') : '—';
    $sach = fn ($html) => \App\Http\Controllers\GiangVienCauHoiController::cleanHtml($html);
    $traLoiChinh = trim(strip_tags((string) $faq->cau_tra_loi));
@endphp
<div class="layout">

    @include('partials.admin-sidebar')

    <main class="content">
        <div class="page">

            <div class="page-head">
                <div>
                    <h1>Chi tiết FAQ</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('admin.faq.index') }}">Quản lý FAQ</a> › Chi tiết FAQ
                    </div>
                </div>
                <div class="head-actions">
                    <a href="{{ route('admin.faq.index') }}" class="btn light">← Quay lại danh sách</a>
                    <a href="{{ route('admin.faq.edit', $faq) }}" class="btn">✏️ Sửa FAQ</a>
                </div>
            </div>

            {{-- ===== Câu hỏi + thông tin ===== --}}
            <div class="card">
                <div class="section">
                    <h2 class="q-title">{{ $faq->cau_hoi }}</h2>
                    <div class="meta">
                        <span>Danh mục: <b>{{ $faq->danhMuc->{$cotTenDanhMuc} ?? '—' }}</b></span>
                        <span>Trạng thái:
                            <span class="badge {{ $lopTrangThai($faq->trang_thai) }}">{{ $nhanTrangThai($faq->trang_thai) }}</span>
                        </span>
                        <span>Người tạo: <b>{{ $nguoiTao ?? '—' }}</b></span>
                        <span>Ngày tạo: <b>{{ $dinhDang($faq->ngay_tao) }}</b></span>
                        <span>Cập nhật: <b>{{ $dinhDang($faq->ngay_cap_nhat) }}</b></span>
                        <span>Lượt xem: <b>{{ $faq->luot_xem ?? 0 }}</b></span>
                    </div>
                </div>
            </div>

            {{-- ===== Nội dung trả lời ===== --}}
            <div class="card">
                <div class="section">
                    <div class="section-title">Nội dung trả lời</div>
                    @if ($traLoiChinh !== '')
                        <div class="answer">{!! $sach($faq->cau_tra_loi) !!}</div>
                    @else
                        <div class="empty-answer">Chưa có nội dung trả lời chính thức.</div>
                    @endif
                </div>
            </div>

            {{-- ===== Thẻ & tệp ===== --}}
            <div class="card">
                <div class="section">
                    <div class="section-title">Thẻ &amp; tệp đính kèm</div>

                    <div style="margin-bottom:16px">
                        <div class="muted" style="margin-bottom:8px">Thẻ</div>
                        @if ($faq->theTags->count())
                            <div class="tags">
                                @foreach ($faq->theTags as $tag)
                                    <span class="tag">{{ $tag->ten_the }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="muted">Không có thẻ.</span>
                        @endif
                    </div>

                    <div>
                        <div class="muted" style="margin-bottom:8px">Tệp đính kèm</div>
                        @if ($faq->tapDinhKems->count())
                            <div class="files">
                                @foreach ($faq->tapDinhKems as $tep)
                                    <div>📎 <a href="{{ asset('storage/' . $tep->duong_dan) }}" target="_blank">{{ $tep->ten_tep }}</a></div>
                                @endforeach
                            </div>
                        @else
                            <span class="muted">Không có tệp đính kèm.</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== Các lượt trả lời ===== --}}
            <div class="card">
                <div class="section">
                    <div class="section-title">Các lượt trả lời ({{ $traLois->count() }})</div>
                    @forelse ($traLois as $r)
                        <div class="reply">
                            <div class="reply-head">
                                <strong>{{ $r->nguoi ?? 'Người dùng' }}</strong>
                                <span>{{ $dinhDang($r->ngay_tra_loi) }}</span>
                            </div>
                            <div class="answer">{!! $sach($r->noi_dung) !!}</div>
                        </div>
                    @empty
                        <span class="muted">Chưa có lượt trả lời nào.</span>
                    @endforelse
                </div>
            </div>

        </div>
    </main>
</div>
</body>
</html>