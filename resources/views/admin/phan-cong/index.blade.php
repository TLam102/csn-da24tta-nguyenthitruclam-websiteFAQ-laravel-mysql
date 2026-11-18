<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Phân công lĩnh vực - Quản trị</title>
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
    <style>
        .pc-cats { display: flex; flex-wrap: wrap; gap: 8px 18px; }
        .pc-cats label { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
        .pc-note { color: var(--muted); margin-bottom: 14px; }
    </style>

    <style>
        .pc-cats { display: flex; flex-wrap: wrap; gap: 8px 18px; }
        .pc-cats label { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
        .pc-note { color: var(--muted); margin-bottom: 14px; }
    </style>

</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar')

    <main class="content">
        <h1>Phân công lĩnh vực phụ trách</h1>
        <p class="pc-note">Giảng viên chỉ được xem và trả lời các câu hỏi, FAQ thuộc những lĩnh vực (danh mục) được tích chọn dưới đây. Quản trị viên trả lời được mọi lĩnh vực.</p>

        @if (session('ok'))
            <div class="alert">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert" style="background:#fdeaea;color:#a32d2d;border-color:#f3c4c4">{{ $errors->first() }}</div>
        @endif

        <div class="card">
            <table>
                <thead><tr><th style="width:220px">Giảng viên</th><th>Lĩnh vực phụ trách</th><th style="width:90px"></th></tr></thead>
                <tbody>
                @forelse ($giangViens as $gv)
                    <tr>
                            <td><strong>{{ $gv->name }}</strong><br><span style="color:var(--muted)">{{ $gv->email }}</span></td>
                            <td>
                                <div class="pc-cats">
                                    @foreach ($danhMucs as $dm)
                                        <label>
                                            <input type="checkbox" form="pc-{{ $gv->id }}" name="danh_muc[]" value="{{ $dm->danh_muc_id }}"
                                                {{ in_array((int) $dm->danh_muc_id, $daCo[$gv->id] ?? [], true) ? 'checked' : '' }}>
                                            {{ $dm->ten_danh_muc }}
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td><button class="btn" type="submit" form="pc-{{ $gv->id }}">Lưu</button></td>
                    </tr>
                @empty
                    <tr><td colspan="3">Chưa có tài khoản giảng viên nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mỗi giảng viên một biểu mẫu; các ô trong bảng gắn vào biểu mẫu bằng thuộc tính form="..." --}}
        @foreach ($giangViens as $gv)
            <form id="pc-{{ $gv->id }}" method="POST" action="{{ route('admin.phan-cong.update', $gv->id) }}">@csrf</form>
        @endforeach
    </main>
</div>
</body>
</html>