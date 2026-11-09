<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý danh mục - Quản trị</title>
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
        .page { max-width: 980px; margin: 0 auto; }

        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .breadcrumb { color: var(--muted); margin-top: 6px; font-size: 13px; }
        .breadcrumb a { color: var(--green-dark); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .alert { padding: 11px 16px; border-radius: 10px; margin-bottom: 16px; }
        .alert.ok { background: var(--green-soft); color: var(--green-dark); border: 1px solid #bfe3cf; }
        .alert.err { background: #fdecec; color: var(--red); border: 1px solid #f3c1c1; }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 18px; text-align: left; border-bottom: 1px solid var(--border); vertical-align: middle; }
        th { background: #fafcfb; font-size: 13px; color: var(--muted); font-weight: 600; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fafcfb; }
        .name-cell { font-weight: 600; }
        .desc-cell { color: var(--muted); }
        .empty { text-align: center; padding: 36px; color: var(--muted); }

        .actions { display: flex; gap: 8px; }
        .actions form { display: inline; }
        .actions button { width: 32px; height: 32px; display: grid; place-items: center; border: 1px solid var(--border); border-radius: 8px; background: #fff; cursor: pointer; font-size: 14px; }
        .actions .edit:hover { background: var(--green-soft); border-color: var(--green); }
        .actions .del:hover { background: #fdecec; border-color: var(--red); }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 42px; padding: 0 22px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); font-weight: 500; }
        .btn.light:hover { background: var(--bg); }

        .pagination-wrap { display: flex; justify-content: center; margin-top: 18px; }
        .pagination-wrap svg { width: 16px; height: 16px; }

        /* ===== Hộp thoại thêm / sửa ===== */
        dialog { border: 0; border-radius: 14px; padding: 0; width: min(520px, 92vw); box-shadow: 0 20px 50px rgba(0, 0, 0, .25); margin: auto; }
        dialog::backdrop { background: rgba(31, 45, 39, .45); }
        .modal-head { padding: 18px 24px; border-bottom: 1px solid var(--border); font-size: 17px; font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
        .modal-head button { border: 0; background: none; font-size: 22px; line-height: 1; cursor: pointer; color: var(--muted); }
        .modal-body { padding: 22px 24px; }
        .field { margin-bottom: 18px; }
        .field:last-child { margin-bottom: 0; }
        .field label { display: block; font-weight: 600; margin-bottom: 8px; }
        .req { color: var(--red); }
        .field input[type=text], .field textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-size: 14px; font-family: inherit; color: var(--text); }
        .field input[type=text] { height: 42px; }
        .field textarea { min-height: 100px; resize: vertical; line-height: 1.5; }
        .field input:focus, .field textarea:focus { outline: 3px solid var(--green-soft); border-color: var(--green); }
        .err-text { color: var(--red); font-size: 12px; margin-top: 5px; }
        .modal-foot { padding: 16px 24px; background: #fafcfb; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; }

        @media (max-width: 800px) {
            .content { padding: 16px; }
            th, td { padding: 11px 12px; }
        }
    </style>
       <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">
</head>
<body>
@php
    // ===== CHỈ CẦN SỬA Ở ĐÂY NẾU TÊN CỘT KHÁC (phải giống hằng số trong DanhMucController) =====
    $cotTen  = 'ten_danh_muc';   // cột tên danh mục
    $cotMoTa = 'mo_ta';          // cột mô tả
@endphp
<div class="layout">

    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <div class="page">

            <div class="page-head">
                <div>
                    <h1>Quản lý danh mục</h1>
                    <div class="breadcrumb">
                        <a href="{{ url('/admin') }}">Dashboard</a> › Quản lý danh mục
                    </div>
                </div>
                <button type="button" class="btn" id="btnAdd">+ Thêm danh mục</button>
            </div>

            @if (session('ok'))
                <div class="alert ok">{{ session('ok') }}</div>
            @endif
            @if (session('loi'))
                <div class="alert err">{{ session('loi') }}</div>
            @endif

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th style="width:70px">STT</th>
                            <th style="width:30%">Tên danh mục</th>
                            <th>Mô tả</th>
                            <th style="width:110px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($danhMucs as $i => $dm)
                            <tr>
                                <td>{{ $danhMucs->firstItem() + $i }}</td>
                                <td class="name-cell">{{ $dm->{$cotTen} }}</td>
                                <td class="desc-cell">{{ $dm->{$cotMoTa} ?: '—' }}</td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="edit" title="Sửa"
                                                data-id="{{ $dm->getKey() }}"
                                                data-ten="{{ $dm->{$cotTen} }}"
                                                data-mota="{{ $dm->{$cotMoTa} }}">✏️</button>

                                        <form method="POST" action="{{ route('admin.danh-muc.destroy', $dm->getKey()) }}"
                                              onsubmit="return confirm('Xóa danh mục này? Chỉ xóa được khi chưa có FAQ nào thuộc danh mục.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="del" title="Xóa">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Chưa có danh mục nào. Bấm "+ Thêm danh mục" để tạo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $danhMucs->links() }}
            </div>
        </div>
    </main>
</div>

{{-- ============ HỘP THOẠI THÊM / SỬA ============ --}}
<dialog id="dmModal">
    <form id="dmForm" method="POST" action="{{ route('admin.danh-muc.store') }}">
        @csrf
        <input type="hidden" name="_method" value="PUT" id="dmMethod" disabled>
        <input type="hidden" name="_mode" value="create" id="dmMode">
        <input type="hidden" name="_id" value="" id="dmId">

        <div class="modal-head">
            <span id="dmTitle">Thêm danh mục</span>
            <button type="button" id="dmClose" aria-label="Đóng">×</button>
        </div>

        <div class="modal-body">
            <div class="field">
                <label for="dmTen">Tên danh mục <span class="req">*</span></label>
                <input type="text" id="dmTen" name="ten" maxlength="255" placeholder="Nhập tên danh mục..." required>
                @error('ten') <div class="err-text">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="dmMoTa">Mô tả</label>
                <textarea id="dmMoTa" name="mo_ta" maxlength="1000" placeholder="Nhập mô tả ngắn..."></textarea>
                @error('mo_ta') <div class="err-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="modal-foot">
            <button type="button" class="btn light" id="dmCancel">Hủy</button>
            <button type="submit" class="btn">Lưu</button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        var modal   = document.getElementById('dmModal');
        var form    = document.getElementById('dmForm');
        var method  = document.getElementById('dmMethod');
        var mode    = document.getElementById('dmMode');
        var idInput = document.getElementById('dmId');
        var title   = document.getElementById('dmTitle');
        var ten     = document.getElementById('dmTen');
        var moTa    = document.getElementById('dmMoTa');

        var storeUrl = @json(route('admin.danh-muc.store'));
        var baseUrl  = @json(url('/admin/danh-muc'));

        function openModal(kieu, id, tenValue, moTaValue) {
            var sua = (kieu === 'edit');
            form.action     = sua ? baseUrl + '/' + id : storeUrl;
            method.disabled = !sua;               // chỉ gửi _method=PUT khi sửa
            mode.value      = sua ? 'edit' : 'create';
            idInput.value   = sua ? id : '';
            title.textContent = sua ? 'Sửa danh mục' : 'Thêm danh mục';
            ten.value  = tenValue || '';
            moTa.value = moTaValue || '';
            modal.showModal();
            ten.focus();
        }

        document.getElementById('btnAdd').addEventListener('click', function () { openModal('create'); });

        document.querySelectorAll('button.edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openModal('edit', btn.dataset.id, btn.dataset.ten, btn.dataset.mota);
            });
        });

        function dong() { modal.close(); }
        document.getElementById('dmClose').addEventListener('click', dong);
        document.getElementById('dmCancel').addEventListener('click', dong);
        // Bấm ra ngoài hộp thoại để đóng
        modal.addEventListener('click', function (e) { if (e.target === modal) dong(); });

        @if ($errors->any())
            // Có lỗi nhập liệu: mở lại hộp thoại và giữ nguyên nội dung đã gõ
            openModal(@json(old('_mode', 'create')), @json(old('_id')), @json(old('ten')), @json(old('mo_ta')));
        @endif
    })();
</script>
</body>
</html>