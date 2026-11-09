<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Câu hỏi chờ duyệt - Quản trị</title>
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
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }
        .layout { display: flex; min-height: 100vh; }

        /* ===== Nội dung ===== */
        .content { flex: 1; min-width: 0; padding: 28px 32px 40px; }
        .page { max-width: 1100px; margin: 0 auto; }

        .page-head { margin-bottom: 20px; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .breadcrumb { color: var(--muted); margin-top: 6px; font-size: 13px; }
        .breadcrumb a { color: var(--green-dark); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .alert { padding: 11px 16px; border-radius: 10px; margin-bottom: 16px; }
        .alert.ok { background: var(--green-soft); color: var(--green-dark); border: 1px solid #bfe3cf; }
        .alert.err { background: #fdecec; color: var(--red); border: 1px solid #f3c1c1; }

        /* Thanh tìm kiếm + lọc */
        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .toolbar input[type=text] { flex: 1; min-width: 220px; height: 42px; padding: 0 14px; border: 1px solid var(--border); border-radius: 10px; background: #fff; font-size: 14px; font-family: inherit; }
        .toolbar select { height: 42px; padding: 0 14px; border: 1px solid var(--border); border-radius: 10px; background: #fff; font-size: 14px; font-family: inherit; min-width: 190px; }
        .toolbar input:focus, .toolbar select:focus { outline: 3px solid var(--green-soft); border-color: var(--green); }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 16px; text-align: left; border-bottom: 1px solid var(--border); vertical-align: middle; }
        th { background: #fafcfb; font-size: 13px; color: var(--muted); font-weight: 600; white-space: nowrap; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fafcfb; }
        .title-cell { font-weight: 600; max-width: 300px; }
        .title-cell span { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .muted { color: var(--muted); }
        .tag { display: inline-block; padding: 3px 10px; border-radius: 12px; background: var(--green-soft); color: var(--green-dark); font-size: 12px; font-weight: 600; white-space: nowrap; }
        .empty { text-align: center; padding: 40px; color: var(--muted); }

        /* Nút */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 42px; padding: 0 22px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); font-weight: 500; }
        .btn.light:hover { background: var(--bg); }

        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .actions form { display: inline; }
        .mini { display: inline-flex; align-items: center; justify-content: center; height: 30px; padding: 0 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; font-family: inherit; border: 1px solid var(--border); background: #fff; color: var(--text); white-space: nowrap; }
        .mini:hover { background: var(--bg); }
        .mini.approve { background: var(--green-soft); border-color: #bfe3cf; color: var(--green-dark); }
        .mini.approve:hover { background: var(--green); border-color: var(--green); color: #fff; }
        .mini.reject { background: #fdecec; border-color: #f3c1c1; color: var(--red); }
        .mini.reject:hover { background: var(--red); border-color: var(--red); color: #fff; }

        .pagination-wrap { display: flex; justify-content: center; margin-top: 18px; }
        .pagination-wrap svg { width: 16px; height: 16px; }

        /* ===== Hộp thoại xem chi tiết ===== */
        dialog { border: 0; border-radius: 14px; padding: 0; width: min(600px, 92vw); box-shadow: 0 20px 50px rgba(0, 0, 0, .25); margin: auto; }
        dialog::backdrop { background: rgba(31, 45, 39, .45); }
        .modal-head { padding: 18px 24px; border-bottom: 1px solid var(--border); font-size: 17px; font-weight: 700; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .modal-head button { border: 0; background: none; font-size: 22px; line-height: 1; cursor: pointer; color: var(--muted); }
        .modal-body { padding: 22px 24px; }
        .meta { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
        .meta .k { font-size: 12px; color: var(--muted); margin-bottom: 3px; }
        .meta .v { font-weight: 600; }
        .q-title { font-size: 16px; font-weight: 700; margin-bottom: 10px; }
        .q-body { background: #fafcfb; border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; line-height: 1.7; white-space: pre-wrap; min-height: 80px; }
        .modal-foot { padding: 16px 24px; background: #fafcfb; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; flex-wrap: wrap; gap: 12px; }
        .modal-foot form { display: inline; margin: 0; }

        @media (max-width: 800px) {
            .content { padding: 16px; }
            th, td { padding: 11px 10px; }
            .meta { grid-template-columns: 1fr; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ @filemtime(public_path('css/admin-theme.css')) }}">
</head>
<body>
@php
    // Tên tài khoản người gửi: tra trực tiếp từ bảng users theo cau_hoi.nguoi_gui_id
    $idsCauHoi = collect($cauHois->items())->pluck('id')->filter()->values()->all();
    $nguoiGuiMap = $idsCauHoi
        ? \Illuminate\Support\Facades\DB::table('cau_hoi as c')
            ->leftJoin('users as u', 'u.id', '=', 'c.nguoi_gui_id')
            ->whereIn('c.cau_hoi_id', $idsCauHoi)
            ->select('c.cau_hoi_id', 'u.name', 'u.email')
            ->get()->keyBy('cau_hoi_id')
        : collect();
@endphp
<div class="layout">

    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <div class="page">

            <div class="page-head">
                <h1>Câu hỏi chờ duyệt</h1>
                <div class="breadcrumb">
                    <a href="{{ url('/admin') }}">Dashboard</a> › Câu hỏi chờ duyệt
                </div>
            </div>

            @if (session('ok'))
                <div class="alert ok">{{ session('ok') }}</div>
            @endif
            @if (session('loi'))
                <div class="alert err">{{ session('loi') }}</div>
            @endif

            {{-- Tìm kiếm + lọc theo danh mục --}}
            <form class="toolbar" method="GET" action="{{ url('/admin/cau-hoi') }}">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Tìm kiếm...">

                <select name="danh_muc" onchange="this.form.submit()">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($danhMucs as $dm)
                        <option value="{{ $dm->id }}" @selected(request('danh_muc') == $dm->id)>{{ $dm->ten }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn">Lọc</button>
                @if (request()->hasAny(['q', 'danh_muc']))
                    <a href="{{ url('/admin/cau-hoi') }}" class="btn light">Xóa lọc</a>
                @endif
            </form>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px">STT</th>
                            <th>Tiêu đề</th>
                            <th>Người gửi</th>
                            <th>Danh mục</th>
                            <th>Ngày gửi</th>
                            <th style="width:230px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cauHois as $i => $ch)
                            @php
                                $ngay = $ch->ngay_gui ? \Carbon\Carbon::parse($ch->ngay_gui)->format('d/m/Y') : '—';
                                $ng = $nguoiGuiMap[$ch->id] ?? null;
                                $tenGui = ($ng->name ?? null) ?: ($ch->nguoi_gui ?: 'Ẩn danh');
                                $mailGui = $ng->email ?? null;
                            @endphp
                            <tr>
                                <td>{{ $cauHois->firstItem() + $i }}</td>
                                <td class="title-cell"><span>{{ $ch->tieu_de }}</span></td>
                                <td>
                                    <div style="font-weight:600">{{ $tenGui }}</div>
                                    @if ($mailGui)<div class="muted" style="font-size:12px">{{ $mailGui }}</div>@endif
                                </td>
                                <td>
                                    @if ($ch->danh_muc)
                                        <span class="tag">{{ $ch->danh_muc }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td class="muted">{{ $ngay }}</td>
                                <td>
                                    <div class="actions">
                                        {{-- Xem chi tiết (hộp thoại) --}}
                                        <button type="button" class="mini btn-view"
                                                data-id="{{ $ch->id }}"
                                                data-tieude="{{ $ch->tieu_de }}"
                                                data-noidung="{{ $ch->noi_dung }}"
                                                data-nguoigui="{{ $tenGui }}{{ $mailGui ? ' (' . $mailGui . ')' : '' }}"
                                                data-danhmuc="{{ $ch->danh_muc ?: '—' }}"
                                                data-ngay="{{ $ngay }}">Xem</button>

                                        {{-- DUYỆT: đăng ngay lên danh sách FAQ, ai cũng xem được --}}
                                        <form method="POST" action="{{ route('admin.cau-hoi.duyet', $ch->id) }}"
                                              onsubmit="return confirm('Duyệt và đăng câu hỏi này lên danh sách FAQ?');">
                                            @csrf
                                            <button type="submit" class="mini approve">Duyệt</button>
                                        </form>

                                        {{-- Từ chối --}}
                                        <form method="POST" action="{{ route('admin.cau-hoi.tu-choi', $ch->id) }}"
                                              onsubmit="return confirm('Từ chối câu hỏi này? Câu hỏi sẽ không còn trong danh sách chờ duyệt.');">
                                            @csrf
                                            <button type="submit" class="mini reject">Từ chối</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">Không có câu hỏi nào đang chờ duyệt.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $cauHois->links() }}
            </div>
        </div>
    </main>
</div>

{{-- ============ HỘP THOẠI XEM CHI TIẾT ============ --}}
<dialog id="chModal">
    <div class="modal-head">
        <span>Chi tiết câu hỏi</span>
        <button type="button" id="chClose" aria-label="Đóng">×</button>
    </div>
    <div class="modal-body">
        <div class="meta">
            <div><div class="k">Người gửi</div><div class="v" id="chNguoi"></div></div>
            <div><div class="k">Danh mục</div><div class="v" id="chDanhMuc"></div></div>
            <div><div class="k">Ngày gửi</div><div class="v" id="chNgay"></div></div>
        </div>
        <div class="q-title" id="chTieuDe"></div>
        <div class="q-body" id="chNoiDung"></div>
    </div>
    <div class="modal-foot">
        <button type="button" class="btn light" id="chDong">Đóng</button>
        <form method="POST" id="chDuyetForm" action="#" onsubmit="return confirm('Duyệt và đăng câu hỏi này lên danh sách FAQ?');">
            @csrf
            <button type="submit" class="btn">Duyệt &amp; đăng FAQ</button>
        </form>
    </div>
</dialog>

<script>
    (function () {
        var modal = document.getElementById('chModal');
        var baseUrl = @json(url('/admin/cau-hoi'));

        document.querySelectorAll('.btn-view').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var d = btn.dataset;
                document.getElementById('chNguoi').textContent   = d.nguoigui;
                document.getElementById('chDanhMuc').textContent = d.danhmuc;
                document.getElementById('chNgay').textContent    = d.ngay;
                document.getElementById('chTieuDe').textContent  = d.tieude;
                document.getElementById('chNoiDung').textContent = d.noidung || '(Không có nội dung chi tiết)';
                document.getElementById('chDuyetForm').action = baseUrl + '/' + d.id + '/duyet';
                modal.showModal();
            });
        });

        function dong() { modal.close(); }
        document.getElementById('chClose').addEventListener('click', dong);
        document.getElementById('chDong').addEventListener('click', dong);
        modal.addEventListener('click', function (e) { if (e.target === modal) dong(); });
    })();
</script>
</body>
</html>