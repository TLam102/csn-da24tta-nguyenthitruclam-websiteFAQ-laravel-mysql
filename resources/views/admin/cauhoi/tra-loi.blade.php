<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soạn trả lời &amp; tạo FAQ - Quản trị</title>
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

        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .breadcrumb { color: var(--muted); margin-top: 6px; font-size: 13px; }
        .breadcrumb a { color: var(--green-dark); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .alert-error { background: #fdecec; border: 1px solid #f3c1c1; color: var(--red); padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; }
        .alert-error ul { margin-left: 18px; margin-top: 4px; }

        /* Hai cột */
        .cols { display: grid; grid-template-columns: 5fr 7fr; gap: 20px; align-items: start; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); }
        .card-head { padding: 16px 22px; border-bottom: 1px solid var(--border); font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .card-head:before { content: ''; width: 4px; height: 16px; background: var(--green); border-radius: 2px; }
        .card-body { padding: 20px 22px; }

        .field { margin-bottom: 18px; }
        .field:last-child { margin-bottom: 0; }
        .field > label, .label { display: block; font-weight: 600; margin-bottom: 8px; }
        .req { color: var(--red); }
        .field input[type=text], .field select {
            width: 100%; height: 42px; padding: 0 14px; border: 1px solid var(--border); border-radius: 10px; background: #fff; font-size: 14px; font-family: inherit; color: var(--text);
        }
        .field input[readonly] { background: #fafcfb; color: var(--muted); }
        .field input:focus, .field select:focus { outline: 3px solid var(--green-soft); border-color: var(--green); }
        .err { color: var(--red); font-size: 12px; margin-top: 5px; }
        .hint { color: var(--muted); font-size: 12px; margin-top: 6px; }
        .detail { background: #fafcfb; border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px; line-height: 1.6; white-space: pre-wrap; max-height: 170px; overflow-y: auto; color: var(--text); }

        /* Thẻ dạng chip có nút × */
        .chips { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .chip { display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 6px 0 12px; border-radius: 16px; background: var(--green-soft); border: 1px solid #bfe3cf; color: var(--green-dark); font-weight: 600; font-size: 13px; }
        .chip button { width: 20px; height: 20px; border: 0; border-radius: 50%; background: transparent; color: var(--green-dark); cursor: pointer; font-size: 15px; line-height: 1; }
        .chip button:hover { background: var(--green); color: #fff; }
        .add-tag { height: 32px; padding: 0 10px; border: 1px dashed var(--green); border-radius: 16px; background: #fff; color: var(--green-dark); font-weight: 600; font-size: 13px; font-family: inherit; cursor: pointer; max-width: 150px; }
        .add-tag:hover { background: var(--green-soft); }
        .tags-empty { color: var(--muted); font-size: 13px; }

        /* Trình soạn thảo */
        .editor-box { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: #fff; }
        .editor-box:focus-within { outline: 3px solid var(--green-soft); border-color: var(--green); }
        .editor-toolbar { display: flex; gap: 4px; padding: 8px 10px; border-bottom: 1px solid var(--border); background: #fafcfb; }
        .editor-toolbar button { min-width: 34px; height: 32px; border: 1px solid transparent; background: none; border-radius: 8px; cursor: pointer; font-size: 14px; color: var(--text); }
        .editor-toolbar button:hover { background: var(--green-soft); border-color: var(--border); }
        .editor-toolbar .sep { width: 1px; background: var(--border); margin: 4px 6px; }
        .editor { min-height: 260px; padding: 14px 16px; outline: none; line-height: 1.7; }
        .editor:empty:before { content: attr(data-placeholder); color: #9aa7a0; }
        .editor ul, .editor ol { margin-left: 22px; }

        /* Nút */
        .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 22px; background: #fafcfb; border-top: 1px solid var(--border); border-radius: 0 0 14px 14px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-width: 120px; height: 42px; padding: 0 22px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); font-weight: 500; }
        .btn.light:hover { background: var(--bg); }

        @media (max-width: 900px) {
            .cols { grid-template-columns: 1fr; }
            .content { padding: 16px; }
        }
    </style>
       <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}?v={{ filemtime(public_path('css/admin-theme.css')) }}">
    
</head>
<body>
@php
    $nhanTrangThai = function ($khoa) use ($dsTrangThai) {
        $v = $dsTrangThai[$khoa] ?? $khoa;
        if (is_array($v)) { $v = $v['label'] ?? $v['ten'] ?? reset($v); }
        return $v;
    };

    $tagDaChon = collect(old('tags', []))->map(fn ($v) => (int) $v)->all();
    $trangThaiChon = old('trang_thai', $khoaDang);
@endphp
<div class="layout">

    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <div class="page">

            <div class="page-head">
                <div>
                    <h1>Soạn trả lời &amp; tạo FAQ</h1>
                    <div class="breadcrumb">
                        <a href="{{ url('/admin') }}">Dashboard</a> ›
                        <a href="{{ url('/admin/cau-hoi') }}">Câu hỏi chờ duyệt</a> › Soạn trả lời
                    </div>
                </div>
                <a href="{{ url('/admin/cau-hoi') }}" class="btn light" style="min-width:0">← Quay lại danh sách</a>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    <strong>Vui lòng kiểm tra lại:</strong>
                    <ul>
                        @foreach ($errors->all() as $loi)
                            <li>{{ $loi }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="traLoiForm" method="POST" action="{{ route('admin.cau-hoi.tra-loi.luu', $cauHoi->id) }}">
                @csrf
                <input type="hidden" name="hanh_dong" id="hanhDong" value="dang">

                <div class="cols">

                    {{-- ===== Cột trái: thông tin câu hỏi ===== --}}
                    <section class="card">
                        <div class="card-head">Thông tin câu hỏi</div>
                        <div class="card-body">

                            <div class="field">
                                <label for="cau_hoi">Tiêu đề <span class="req">*</span></label>
                                <input type="text" id="cau_hoi" name="cau_hoi" maxlength="500"
                                       value="{{ old('cau_hoi', $cauHoi->tieu_de) }}" required>
                                <div class="hint">Tiêu đề này sẽ trở thành câu hỏi của FAQ, bạn có thể chỉnh lại cho gọn.</div>
                            </div>

                            <div class="field">
                                <label>Người gửi</label>
                                <input type="text" value="{{ $cauHoi->nguoi_gui ?: 'Ẩn danh' }}" readonly>
                            </div>

                            @if ($cauHoi->noi_dung)
                                <div class="field">
                                    <span class="label">Nội dung chi tiết</span>
                                    <div class="detail">{{ $cauHoi->noi_dung }}</div>
                                </div>
                            @endif

                            <div class="field">
                                <label for="danh_muc_id">Danh mục <span class="req">*</span></label>
                                <select id="danh_muc_id" name="danh_muc_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($danhMucs as $dm)
                                        <option value="{{ $dm->id }}"
                                            @selected(old('danh_muc_id', $cauHoi->danh_muc_id) == $dm->id)>{{ $dm->ten }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <span class="label">Thẻ</span>
                                <div class="chips" id="chips">
                                    {{-- chip và ô chọn thẻ được vẽ bằng JavaScript bên dưới --}}
                                </div>
                                <div id="tagInputs"></div>
                            </div>
                        </div>
                    </section>

                    {{-- ===== Cột phải: soạn trả lời ===== --}}
                    <section class="card">
                        <div class="card-head">Soạn trả lời</div>
                        <div class="card-body">

                            <div class="field">
                                <div class="editor-box">
                                    <div class="editor-toolbar">
                                        <button type="button" data-cmd="bold" title="In đậm"><b>B</b></button>
                                        <button type="button" data-cmd="italic" title="In nghiêng"><i>I</i></button>
                                        <button type="button" data-cmd="underline" title="Gạch chân"><u>U</u></button>
                                        <span class="sep"></span>
                                        <button type="button" data-cmd="insertUnorderedList" title="Danh sách chấm">•≡</button>
                                        <button type="button" data-cmd="insertOrderedList" title="Danh sách số">1≡</button>
                                    </div>
                                    <div id="editor" class="editor" contenteditable="true"
                                         data-placeholder="Nhập nội dung trả lời..."></div>
                                </div>
                                <textarea id="cau_tra_loi" name="cau_tra_loi" hidden>{{ old('cau_tra_loi') }}</textarea>
                            </div>

                            <div class="field">
                                <label for="trang_thai">Trạng thái</label>
                                <select id="trang_thai" name="trang_thai">
                                    @foreach ($dsTrangThai as $khoa => $nhan)
                                        <option value="{{ $khoa }}" @selected((string) $trangThaiChon === (string) $khoa)>{{ $nhanTrangThai($khoa) }}</option>
                                    @endforeach
                                </select>
                                <div class="hint">Nút "Lưu nháp" luôn lưu ở trạng thái bản nháp, bất kể ô này.</div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn light" data-hanh-dong="nhap">Lưu nháp</button>
                            <button type="submit" class="btn" data-hanh-dong="dang">Đăng / Cập nhật</button>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    (function () {
        var form    = document.getElementById('traLoiForm');
        var editor  = document.getElementById('editor');
        var input   = document.getElementById('cau_tra_loi');
        var hanhDong = document.getElementById('hanhDong');

        // ===== Trình soạn thảo =====
        editor.innerHTML = input.value;

        document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(function (btn) {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
                document.execCommand(btn.getAttribute('data-cmd'), false, null);
            });
        });

        // Nút nào được bấm thì ghi lại hành động đó
        document.querySelectorAll('button[data-hanh-dong]').forEach(function (btn) {
            btn.addEventListener('click', function () { hanhDong.value = btn.getAttribute('data-hanh-dong'); });
        });

        form.addEventListener('submit', function (e) {
            input.value = editor.innerHTML.trim();
            if (!editor.innerText.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập nội dung trả lời.');
                editor.focus();
            }
        });

        // ===== Thẻ =====
        var tatCaThe = @json($theTags->map(fn ($t) => ['id' => (int) $t->the_id, 'ten' => $t->ten_the])->values());
        var daChon   = @json($tagDaChon);
        var chips    = document.getElementById('chips');
        var inputs   = document.getElementById('tagInputs');

        function tenThe(id) {
            for (var i = 0; i < tatCaThe.length; i++) { if (tatCaThe[i].id === id) return tatCaThe[i].ten; }
            return '#' + id;
        }

        function veThe() {
            chips.innerHTML = '';
            inputs.innerHTML = '';

            daChon.forEach(function (id) {
                var chip = document.createElement('span');
                chip.className = 'chip';
                chip.appendChild(document.createTextNode(tenThe(id)));

                var x = document.createElement('button');
                x.type = 'button';
                x.setAttribute('aria-label', 'Bỏ thẻ');
                x.textContent = '×';
                x.addEventListener('click', function () {
                    daChon = daChon.filter(function (v) { return v !== id; });
                    veThe();
                });
                chip.appendChild(x);
                chips.appendChild(chip);

                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'tags[]';
                hidden.value = id;
                inputs.appendChild(hidden);
            });

            var conLai = tatCaThe.filter(function (t) { return daChon.indexOf(t.id) === -1; });
            if (conLai.length) {
                var sel = document.createElement('select');
                sel.className = 'add-tag';
                sel.innerHTML = '<option value="">+ Thêm thẻ</option>';
                conLai.forEach(function (t) {
                    var op = document.createElement('option');
                    op.value = t.id;
                    op.textContent = t.ten;
                    sel.appendChild(op);
                });
                sel.addEventListener('change', function () {
                    if (sel.value) { daChon.push(parseInt(sel.value, 10)); veThe(); }
                });
                chips.appendChild(sel);
            } else if (!daChon.length) {
                var rong = document.createElement('span');
                rong.className = 'tags-empty';
                rong.textContent = 'Chưa có thẻ nào. Tạo thẻ ở mục "Quản lý thẻ".';
                chips.appendChild(rong);
            }
        }

        veThe();
    })();
</script>
</body>
</html>