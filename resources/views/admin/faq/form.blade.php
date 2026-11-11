<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $faq->exists ? 'Sửa FAQ' : 'Thêm FAQ' }} - Quản trị</title>
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


        /* ===== Nội dung (căn giữa, cân đối) ===== */
        .content { flex: 1; min-width: 0; padding: 28px 32px 40px; }
        .page { max-width: 980px; margin: 0 auto; }

        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-head h1 { font-size: 24px; line-height: 1.2; }
        .breadcrumb { color: var(--muted); margin-top: 6px; font-size: 13px; }
        .breadcrumb a { color: var(--green-dark); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 3px rgba(31, 45, 39, .05); overflow: hidden; }
        .section { padding: 24px 28px; border-bottom: 1px solid var(--border); }
        .section:last-of-type { border-bottom: 0; }
        .section-title { font-size: 15px; font-weight: 700; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        .section-title:before { content: ''; width: 4px; height: 16px; background: var(--green); border-radius: 2px; }

        .alert-error { background: #fdecec; border: 1px solid #f3c1c1; color: var(--red); padding: 12px 16px; border-radius: 10px; margin: 20px 28px 0; }
        .alert-error ul { margin-left: 18px; margin-top: 4px; }

        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px 24px; }
        .grid .full { grid-column: 1 / -1; }

        .field > label, .label { display: block; font-weight: 600; margin-bottom: 8px; }
        .req { color: var(--red); }
        .field input[type=text], .field select {
            width: 100%; height: 42px; padding: 0 14px; border: 1px solid var(--border); border-radius: 10px; background: #fff; font-size: 14px; font-family: inherit; color: var(--text);
        }
        .field input[type=text]:focus, .field select:focus { outline: 3px solid var(--green-soft); border-color: var(--green); }
        .err { color: var(--red); font-size: 12px; margin-top: 5px; }
        .hint { color: var(--muted); font-size: 12px; margin-top: 6px; }

        /* Trạng thái dạng nút chọn */
        .seg { display: flex; gap: 8px; flex-wrap: wrap; }
        .seg label { position: relative; margin: 0; }
        .seg input { position: absolute; opacity: 0; pointer-events: none; }
        .seg span { display: inline-flex; align-items: center; height: 42px; padding: 0 18px; border: 1px solid var(--border); border-radius: 10px; cursor: pointer; font-weight: 500; background: #fff; transition: all .15s; }
        .seg span:hover { border-color: var(--green); }
        .seg input:checked + span { background: var(--green-soft); border-color: var(--green); color: var(--green-dark); font-weight: 600; }
        .seg input:focus-visible + span { outline: 3px solid var(--green-soft); }

        /* Trình soạn thảo */
        .editor-box { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: #fff; }
        .editor-box:focus-within { outline: 3px solid var(--green-soft); border-color: var(--green); }
        .editor-toolbar { display: flex; gap: 4px; padding: 8px 10px; border-bottom: 1px solid var(--border); background: #fafcfb; }
        .editor-toolbar button { min-width: 34px; height: 32px; border: 1px solid transparent; background: none; border-radius: 8px; cursor: pointer; font-size: 14px; color: var(--text); }
        .editor-toolbar button:hover { background: var(--green-soft); border-color: var(--border); }
        .editor-toolbar .sep { width: 1px; background: var(--border); margin: 4px 6px; }
        .editor { min-height: 220px; padding: 14px 16px; outline: none; line-height: 1.7; }
        .editor:empty:before { content: attr(data-placeholder); color: #9aa7a0; }
        .editor ul, .editor ol { margin-left: 22px; }

        /* Tệp */
        input[type=file] { width: 100%; padding: 9px 12px; border: 1px dashed var(--border); border-radius: 10px; background: #fafcfb; font-family: inherit; font-size: 13px; }
        input[type=file]::file-selector-button { margin-right: 12px; padding: 6px 14px; border: 1px solid var(--border); border-radius: 8px; background: #fff; cursor: pointer; font-family: inherit; }
        input[type=file]::file-selector-button:hover { background: var(--green-soft); }
        .files-list { margin-top: 10px; font-size: 13px; color: var(--muted); }
        .files-list a { color: var(--green-dark); text-decoration: none; }
        .files-list a:hover { text-decoration: underline; }

        /* Nút thao tác */
        .form-actions { display: flex; justify-content: flex-end; gap: 12px; padding: 18px 28px; background: #fafcfb; border-top: 1px solid var(--border); }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-width: 110px; height: 42px; padding: 0 24px; border-radius: 10px; border: 0; background: var(--green); color: #fff; text-decoration: none; cursor: pointer; font-size: 14px; font-weight: 600; }
        .btn:hover { background: var(--green-dark); }
        .btn.light { background: #fff; color: var(--text); border: 1px solid var(--border); font-weight: 500; }
        .btn.light:hover { background: var(--bg); }

        @media (max-width: 800px) {
            .content { padding: 16px; }
            .section { padding: 18px; }
            .grid { grid-template-columns: 1fr; }
            .form-actions { padding: 14px 18px; }
        }
    </style>
</head>
<body>
@php
    // ===== CHỈ CẦN SỬA Ở ĐÂY NẾU TÊN CỘT KHÁC =====
    $cotTenDanhMuc = 'ten_danh_muc';   // tên cột chứa tên danh mục trong bảng danh_muc

    $dsTrangThai = \App\Models\Faq::TRANG_THAI;
    $nhanTrangThai = function ($khoa) use ($dsTrangThai) {
        $v = $dsTrangThai[$khoa] ?? $khoa;
        if (is_array($v)) { $v = $v['label'] ?? $v['ten'] ?? reset($v); }
        return $v;
    };

    $laSua = $faq->exists;
    $trangThaiHienTai = old('trang_thai', $faq->trang_thai ?: 'nhap');
@endphp
<div class="layout">

    {{-- ============ SIDEBAR QUẢN TRỊ ============ --}}
    @include('partials.admin-sidebar')

    {{-- ============ NỘI DUNG ============ --}}
    <main class="content">
        <div class="page">

            <div class="page-head">
                <div>
                    <h1>{{ $laSua ? 'Sửa FAQ' : 'Thêm FAQ' }}</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('admin.faq.index') }}">Quản lý FAQ</a> › {{ $laSua ? 'Sửa FAQ' : 'Thêm FAQ' }}
                    </div>
                </div>
                <a href="{{ route('admin.faq.index') }}" class="btn light" style="min-width:0">← Quay lại danh sách</a>
            </div>

            <form id="faqForm" class="card" method="POST"
                  action="{{ $laSua ? route('admin.faq.update', $faq) : route('admin.faq.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($laSua) @method('PUT') @endif

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

                {{-- ===== Thông tin chung ===== --}}
                <div class="section">
                    <div class="section-title">Thông tin chung</div>
                    <div class="grid">
                        <div class="field full">
                            <label for="cau_hoi">Tiêu đề <span class="req">*</span></label>
                            <input type="text" id="cau_hoi" name="cau_hoi" maxlength="500"
                                   value="{{ old('cau_hoi', $faq->cau_hoi) }}" placeholder="Nhập tiêu đề câu hỏi..." required>
                            @error('cau_hoi') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <label for="danh_muc_id">Danh mục <span class="req">*</span></label>
                            <select id="danh_muc_id" name="danh_muc_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach ($danhMucs as $dm)
                                    <option value="{{ $dm->danh_muc_id }}"
                                        @selected(old('danh_muc_id', $faq->danh_muc_id) == $dm->danh_muc_id)>
                                        {{ $dm->{$cotTenDanhMuc} }}
                                    </option>
                                @endforeach
                            </select>
                            @error('danh_muc_id') <div class="err">{{ $message }}</div> @enderror
                        </div>

                        <div class="field">
                            <span class="label">Trạng thái</span>
                            <div class="seg">
                                @foreach ($dsTrangThai as $khoa => $nhan)
                                    <label>
                                        <input type="radio" name="trang_thai" value="{{ $khoa }}"
                                               @checked((string) $trangThaiHienTai === (string) $khoa)>
                                        <span>{{ $nhanTrangThai($khoa) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('trang_thai') <div class="err">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- ===== Nội dung trả lời ===== --}}
                <div class="section">
                    <div class="section-title">Nội dung trả lời <span class="req">*</span></div>
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
                    <textarea id="cau_tra_loi" name="cau_tra_loi" hidden>{{ old('cau_tra_loi', $faq->cau_tra_loi) }}</textarea>
                    @error('cau_tra_loi') <div class="err">{{ $message }}</div> @enderror
                </div>

                {{-- ===== Tệp đính kèm ===== --}}
                <div class="section">
                    <div class="section-title">Tệp đính kèm</div>
                    <div class="field">
                        <label for="files">Chọn tệp</label>
                        <input type="file" id="files" name="files[]" multiple>
                        <div class="hint">Mỗi tệp tối đa 10MB.</div>
                        @error('files.*') <div class="err">{{ $message }}</div> @enderror

                        @if ($laSua && $faq->tapDinhKems->count())
                            <div class="files-list">
                                Đã đính kèm:
                                @foreach ($faq->tapDinhKems as $tep)
                                    <div>📎 <a href="{{ file_url($tep->duong_dan) }}"s target="_blank">{{ $tep->ten_tep }}</a></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.faq.index') }}" class="btn light">Hủy</a>
                    <button type="submit" class="btn">Lưu</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    (function () {
        var editor = document.getElementById('editor');
        var input  = document.getElementById('cau_tra_loi');
        var form   = document.getElementById('faqForm');

        // Đưa nội dung đã có (khi sửa) vào trình soạn thảo
        editor.innerHTML = input.value;

        // Các nút định dạng
        document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(function (btn) {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
                document.execCommand(btn.getAttribute('data-cmd'), false, null);
            });
        });

        // Khi lưu: chép nội dung trình soạn thảo vào ô ẩn
        form.addEventListener('submit', function (e) {
            input.value = editor.innerHTML.trim();
            if (!editor.innerText.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập nội dung trả lời.');
                editor.focus();
            }
        });
    })();
</script>
</body>
</html>