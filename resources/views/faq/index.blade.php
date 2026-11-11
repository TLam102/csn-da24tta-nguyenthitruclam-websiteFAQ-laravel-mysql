@extends('layouts.app')

@section('title', 'Danh sách FAQ')

@section('content')
<style>
    .faqx { padding: 8px 0 56px; }
    .faqx h1 { font-size: 28px; color: #1b5e20; margin: 0 0 20px; }
    .faqx-filter { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 22px; }
    .faqx-filter input, .faqx-filter select { height: 44px; border: 1px solid #cfd8c4; border-radius: 10px; padding: 0 14px; font: inherit; background: #fff; }
    .faqx-filter input { flex: 1; min-width: 200px; }
    .faqx-btn { height: 44px; padding: 0 20px; border: 0; border-radius: 10px; background: linear-gradient(90deg,#9ccc2b,#f2d216); color: #1b3a00; font-weight: 700; cursor: pointer; }
    .faqx-btn:disabled { opacity: .6; cursor: default; }
    .faqx-btn-line { height: 44px; padding: 0 18px; border: 1.5px solid #1b6e1b; border-radius: 10px; background: #fff; color: #1b6e1b; font-weight: 700; cursor: pointer; }
    .faqx-list { background: #fff; border: 1px solid #dfe8d3; border-radius: 14px; }
    .faqx-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 16px 18px; border-bottom: 1px solid #edf2e6; }
    .faqx-item:last-child { border-bottom: 0; }
    .faqx-q { font-weight: 600; color: #1b3a00; text-decoration: none; }
    .faqx-q:hover { color: #2e7d32; }
    .faqx-meta { margin-top: 6px; font-size: 13px; color: #6b7a5c; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .faqx-side { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .faqx-badge { white-space: nowrap; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: #e3f4d6; color: #1b5e20; }
    .faqx-badge.none { background: #fff4d6; color: #8a6a00; }
    .faqx-reply-btn { flex-shrink: 0; padding: 8px 16px; border: 1.5px solid #2e7d32; border-radius: 999px; background: #fff; color: #2e7d32; font-weight: 600; cursor: pointer; }
    .faqx-reply-btn:hover { background: #2e7d32; color: #fff; }
    .faqx-empty { padding: 36px; text-align: center; color: #6b7a5c; }
    .faqx-pager { display: flex; justify-content: center; align-items: center; gap: 14px; margin-top: 22px; }
    .faqx-pager a { color: #2e7d32; font-weight: 600; text-decoration: none; }
    .faqx-pager .off { color: #aab59c; }

    /* ===== Cửa sổ soạn câu trả lời ===== */
    .faqx-modal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,.5); padding: 16px; }
    .faqx-modal.open { display: flex; }
    .faqx-box { background: #fff; width: 100%; max-width: 760px; max-height: 92vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; border-top: 4px solid #9ccc2b; }
    .faqx-scroll { overflow-y: auto; padding: 20px 24px; }
    .faqx-qtitle { margin: 0; font-size: 18px; color: #1b3a00; }
    .faqx-qmeta { color: #6b7a5c; font-size: 13px; margin-top: 4px; }
    .faqx-close { float: right; border: 0; background: none; font-size: 26px; line-height: 1; cursor: pointer; color: #888; }
    .faqx-thread { margin: 16px 0 6px; display: flex; flex-direction: column; gap: 10px; }
    .faqx-msg { background: #f4f8ec; border-radius: 10px; padding: 10px 14px; }
    .faqx-msg b { color: #1b5e20; }
    .faqx-msg span.t { color: #6b7a5c; font-size: 12px; margin-left: 6px; }
    .faqx-msg .c { margin-top: 4px; }
    .faqx-msg ul, .faqx-msg ol, .faqx-ed ul, .faqx-ed ol { padding-left: 22px; }
    .faqx-muted { color: #6b7a5c; margin: 0; }
    .faqx-comp-head { display: flex; align-items: center; gap: 12px; margin: 20px 0 12px; font-weight: 700; color: #16301a; }
    .faqx-comp-ico { width: 40px; height: 40px; border-radius: 10px; background: #e3f4d6; color: #1b6e1b; display: flex; align-items: center; justify-content: center; }
    .faqx-editor { border: 1px solid #dfe8d3; border-radius: 14px; overflow: hidden; }
    .faqx-tools { display: flex; align-items: center; gap: 4px; padding: 8px 12px; background: #f4f8ec; border-bottom: 1px solid #dfe8d3; }
    .faqx-tools button { width: 36px; height: 36px; border: 0; border-radius: 8px; background: transparent; cursor: pointer; color: #16301a; font-size: 15px; }
    .faqx-tools button:hover { background: #e3f4d6; }
    .faqx-tools i.sep { width: 1px; height: 22px; background: #dfe8d3; margin: 0 6px; }
    .faqx-ed { min-height: 140px; max-height: 240px; overflow-y: auto; padding: 14px 18px; outline: none; }
    .faqx-ed:empty:before { content: attr(data-ph); color: #9aa593; }
    .faqx-note { display: none; margin-top: 14px; padding: 12px 16px; border-radius: 10px; font-size: 14px; }
    .faqx-note.ok { display: block; background: #e4f1ee; border: 1px solid #b9d9d1; color: #1b5e50; }
    .faqx-note.err { display: block; background: #fdecea; border: 1px solid #f3b8b3; color: #b3261e; }
    .faqx-foot { display: flex; justify-content: space-between; gap: 10px; margin-top: 16px; }

    /* Thanh tiến trình gửi */
    .faqx-prog { display: none; margin-top: 14px; }
    .faqx-prog.on { display: block; }
    .faqx-prog-bar { height: 8px; border-radius: 999px; background: #e3ebcf; overflow: hidden; }
    .faqx-prog-bar i { display: block; height: 100%; width: 0; background: linear-gradient(90deg,#9ccc2b,#f2d216); transition: width .15s linear; }
    .faqx-prog small { display: block; margin-top: 6px; color: #4a5a43; font-size: 13px; }

    /* Đính kèm tệp */
    .faqx-drop { display: flex; align-items: center; gap: 14px; margin-top: 14px; padding: 16px 20px; border: 2px dashed #7cc02b; border-radius: 14px; background: #eefad6; cursor: pointer; color: #16301a; }
    .faqx-drop:hover, .faqx-drop.over { background: #e3f5bd; }
    .faqx-drop i { font-size: 30px; color: #2e8b1f; }
    .faqx-drop small { color: #4a5a43; }
    .faqx-files { list-style: none; margin: 10px 0 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
    .faqx-files li { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border: 1px solid #dfe8d3; border-radius: 10px; background: #fff; font-size: 14px; }
    .faqx-files li span.n { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .faqx-files li span.s { color: #6b7a5c; font-size: 12px; }
    .faqx-files li button { border: 0; background: none; color: #b3261e; cursor: pointer; font-size: 18px; line-height: 1; }

    /* Tệp trong lượt trả lời */
    .faqx-att { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    .faqx-att a { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; padding: 5px 10px; border: 1px solid #cfe3b5; border-radius: 8px; background: #fff; color: #1b5e20; font-size: 13px; text-decoration: none; }
    .faqx-att a span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px; }
    .faqx-att a:hover { background: #f1f8e6; }
    .faqx-att img { display: block; max-height: 110px; max-width: 200px; border-radius: 8px; border: 1px solid #cfe3b5; }
</style>

<div class="container list-page">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Danh sách FAQ</span>
    </div>

<div class="faqx">
    <h1>Danh sách FAQ</h1>

    <form class="faqx-filter" method="GET" action="{{ route('faqs.index') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Tìm câu hỏi...">
        <select name="danh_muc" onchange="this.form.submit()">
            <option value="">Tất cả danh mục</option>
            @foreach ($categories as $c)
                <option value="{{ $c->slug }}" @selected($slug === $c->slug)>{{ $c->tendanhmuc }}</option>
            @endforeach
        </select>
        <button type="submit" class="faqx-btn">Tìm kiếm</button>
    </form>

    <div class="faqx-list">
        @forelse ($faqs as $f)
            @php
                $ngay = $f->ngay_cap_nhat ?: $f->ngay_tao;
                $soTL = (int) ($f->so_tra_loi ?? 0);
            @endphp
            <div class="faqx-item">
                <div>
                    <a class="faqx-q" href="{{ route('faqs.show', $f->faq_id) }}">{{ $f->cau_hoi }}</a>
                    <div class="faqx-meta">
                        @if ($f->tendanhmuc) <span>{{ $f->tendanhmuc }}</span> @endif
                        @if ($ngay) <span>{{ \Carbon\Carbon::parse($ngay)->format('d/m/Y') }}</span> @endif
                        <span><span data-count="{{ $f->faq_id }}">{{ $soTL }}</span> lượt trả lời</span>
                    </div>
                </div>
                <div class="faqx-side">
                    <span class="faqx-badge {{ $soTL > 0 ? '' : 'none' }}" data-status="{{ $f->faq_id }}">
                        {{ $soTL > 0 ? 'Đã trả lời' : 'Chưa trả lời' }}
                    </span>
                    @if ($canReply)
                        <button type="button" class="faqx-reply-btn" data-open-reply="{{ $f->faq_id }}">
                            <i class="fa-solid fa-reply"></i> Trả lời
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="faqx-empty">Chưa có câu hỏi nào phù hợp.</div>
        @endforelse
    </div>

    @if ($faqs->hasPages())
        <div class="faqx-pager">
            @if ($faqs->onFirstPage()) <span class="off">&laquo; Trước</span>
            @else <a href="{{ $faqs->previousPageUrl() }}">&laquo; Trước</a> @endif
            <span>Trang {{ $faqs->currentPage() }} / {{ $faqs->lastPage() }}</span>
            @if ($faqs->hasMorePages()) <a href="{{ $faqs->nextPageUrl() }}">Sau &raquo;</a>
            @else <span class="off">Sau &raquo;</span> @endif
        </div>
    @endif
</div>
</div>

@if ($canReply)
<div class="faqx-modal" id="faqxModal">
    <div class="faqx-box">
        <div class="faqx-scroll">
            <button type="button" class="faqx-close" id="faqxClose" aria-label="Đóng">&times;</button>
            <h2 class="faqx-qtitle" id="faqxTitle"></h2>
            <div class="faqx-qmeta" id="faqxMeta"></div>

            <div class="faqx-thread" id="faqxThread"></div>

            <div class="faqx-comp-head">
                <span class="faqx-comp-ico"><i class="fa-solid fa-pen-to-square"></i></span>
                Soạn câu trả lời
            </div>

            <div class="faqx-editor">
                <div class="faqx-tools">
                    <button type="button" data-cmd="bold" title="Đậm"><i class="fa-solid fa-bold"></i></button>
                    <button type="button" data-cmd="italic" title="Nghiêng"><i class="fa-solid fa-italic"></i></button>
                    <button type="button" data-cmd="underline" title="Gạch chân"><i class="fa-solid fa-underline"></i></button>
                    <i class="sep"></i>
                    <button type="button" data-cmd="insertUnorderedList" title="Danh sách chấm"><i class="fa-solid fa-list-ul"></i></button>
                    <button type="button" data-cmd="insertOrderedList" title="Danh sách số"><i class="fa-solid fa-list-ol"></i></button>
                    <i class="sep"></i>
                    <button type="button" data-cmd="removeFormat" title="Xóa định dạng"><i class="fa-solid fa-eraser"></i></button>
                </div>
                <div class="faqx-ed" id="faqxEd" contenteditable="true" data-ph="Nhập nội dung trả lời..."></div>
            </div>

            {{-- Đính kèm tệp --}}
            <input type="file" id="faqxFile" multiple hidden
                   accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
            <div class="faqx-drop" id="faqxDrop" role="button" tabindex="0">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <div>
                    <strong>Đính kèm tệp</strong> <small>(ảnh, PDF, Word, Excel...) - tối đa 5 tệp, mỗi tệp 5MB. Ảnh được tự động thu nhỏ để gửi nhanh hơn.</small>
                </div>
            </div>
            <ul class="faqx-files" id="faqxFiles"></ul>

            <div class="faqx-prog" id="faqxProg">
                <div class="faqx-prog-bar"><i id="faqxProgBar"></i></div>
                <small id="faqxProgTxt"></small>
            </div>

            <div class="faqx-note" id="faqxNote"></div>

            <div class="faqx-foot">
                <button type="button" class="faqx-btn-line" id="faqxBack"><i class="fa-solid fa-arrow-left"></i> Quay lại</button>
                <button type="button" class="faqx-btn" id="faqxSend"><i class="fa-solid fa-paper-plane"></i> <span id="faqxSendTxt">Gửi câu trả lời</span></button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var MAX_FILES = 5;
    var MAX_SIZE = 5 * 1024 * 1024;          // giới hạn sau khi nén (khớp với server)
    var MAX_RAW_IMG = 25 * 1024 * 1024;      // ảnh gốc được phép lớn hơn vì sẽ được nén
    var CANH_MAX = 1600, CHAT_LUONG = 0.8;
    var EXT = ['jpg','jpeg','png','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt'];
    var EXT_ANH = ['jpg','jpeg','png','webp'];

    var modal = document.getElementById('faqxModal');
    var thread = document.getElementById('faqxThread');
    var ed = document.getElementById('faqxEd');
    var note = document.getElementById('faqxNote');
    var send = document.getElementById('faqxSend');
    var sendTxt = document.getElementById('faqxSendTxt');
    var drop = document.getElementById('faqxDrop');
    var fileInput = document.getElementById('faqxFile');
    var fileList = document.getElementById('faqxFiles');
    var prog = document.getElementById('faqxProg');
    var progBar = document.getElementById('faqxProgBar');
    var progTxt = document.getElementById('faqxProgTxt');
    var csrf = @json(csrf_token());
    var getTpl = @json(route('faqs.replies', ['id' => '__ID__']));
    var postTpl = @json(route('faqs.reply', ['id' => '__ID__']));
    var currentId = null;
    var files = [];
    var dangNen = 0;       // số ảnh đang được nén
    var dangGui = false;   // đang gửi lên server

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
    function fmtSize(b) {
        if (b >= 1048576) return (b / 1048576).toFixed(1) + ' MB';
        if (b >= 1024) return Math.round(b / 1024) + ' KB';
        return b + ' B';
    }
    function setNote(type, msg) {
        note.className = 'faqx-note' + (msg ? ' ' + type : '');
        note.innerHTML = msg ? (type === 'ok' ? '<i class="fa-solid fa-circle-info"></i> ' : '<i class="fa-solid fa-circle-exclamation"></i> ') + esc(msg) : '';
    }
    function capNhatNutGui() {
        send.disabled = dangGui || dangNen > 0;
        if (dangGui) return;
        sendTxt.textContent = dangNen > 0 ? 'Đang xử lý ảnh...' : 'Gửi câu trả lời';
    }
    function hienTienTrinh(p, txt) {
        prog.classList.add('on');
        progBar.style.width = p + '%';
        progTxt.textContent = txt;
    }
    function anTienTrinh() {
        prog.classList.remove('on');
        progBar.style.width = '0';
        progTxt.textContent = '';
    }

    // ===== Nén ảnh trên thiết bị (ảnh chụp điện thoại 3-8MB còn ~200-500KB) =====
    function docAnh(file) {
        if (window.createImageBitmap) {
            return createImageBitmap(file, { imageOrientation: 'from-image' }).catch(function () { return docAnhCu(file); });
        }
        return docAnhCu(file);
    }
    function docAnhCu(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () { URL.revokeObjectURL(url); resolve(img); };
            img.onerror = function () { URL.revokeObjectURL(url); reject(new Error('img')); };
            img.src = url;
        });
    }
    function nenAnh(file) {
        return docAnh(file).then(function (bmp) {
            var w0 = bmp.width, h0 = bmp.height;
            var tiLe = Math.min(1, CANH_MAX / Math.max(w0, h0));
            var w = Math.round(w0 * tiLe), h = Math.round(h0 * tiLe);
            var c = document.createElement('canvas');
            c.width = w; c.height = h;
            var ctx = c.getContext('2d');
            ctx.fillStyle = '#fff';            // ảnh PNG trong suốt không bị nền đen khi sang JPEG
            ctx.fillRect(0, 0, w, h);
            ctx.drawImage(bmp, 0, 0, w, h);
            if (bmp.close) bmp.close();
            return new Promise(function (resolve) { c.toBlob(resolve, 'image/jpeg', CHAT_LUONG); });
        }).then(function (blob) {
            if (!blob || blob.size >= file.size) return file;   // nén xong mà không nhẹ hơn thì giữ ảnh gốc
            return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
        }).catch(function () { return file; });
    }

    // ===== Tệp đính kèm =====
    function veTep() {
        fileList.innerHTML = '';
        files.forEach(function (f, i) {
            var li = document.createElement('li');
            li.innerHTML = '<i class="fa-solid fa-paperclip"></i><span class="n">' + esc(f.name) + '</span><span class="s">' + fmtSize(f.size) + '</span>';
            var x = document.createElement('button');
            x.type = 'button'; x.setAttribute('aria-label', 'Bỏ tệp'); x.innerHTML = '&times;';
            x.addEventListener('click', function () { if (dangGui) return; files.splice(i, 1); veTep(); });
            li.appendChild(x);
            fileList.appendChild(li);
        });
    }
    function themMotTep(f) {
        var ext = (f.name.split('.').pop() || '').toLowerCase();
        if (EXT.indexOf(ext) === -1) { setNote('err', 'Tệp "' + f.name + '" không được hỗ trợ (chỉ nhận ảnh, PDF, Word, Excel, PowerPoint, TXT).'); return Promise.resolve(); }
        if (files.length + dangNen >= MAX_FILES) { setNote('err', 'Chỉ được đính kèm tối đa 5 tệp.'); return Promise.resolve(); }

        var laAnh = EXT_ANH.indexOf(ext) !== -1;
        if (!laAnh && f.size > MAX_SIZE) { setNote('err', 'Tệp "' + f.name + '" vượt quá 5MB.'); return Promise.resolve(); }
        if (laAnh && f.size > MAX_RAW_IMG) { setNote('err', 'Ảnh "' + f.name + '" quá lớn (trên 25MB).'); return Promise.resolve(); }

        if (!laAnh) { files.push(f); veTep(); return Promise.resolve(); }

        dangNen++; capNhatNutGui();
        return nenAnh(f).then(function (kq) {
            if (kq.size > MAX_SIZE) { setNote('err', 'Ảnh "' + f.name + '" vẫn vượt quá 5MB sau khi thu nhỏ.'); return; }
            files.push(kq); veTep();
        }).then(function () {
            dangNen--; capNhatNutGui();
        });
    }
    function themTep(list) {
        setNote('', '');
        // Xử lý lần lượt để tiết kiệm bộ nhớ trên điện thoại
        var ds = Array.prototype.slice.call(list);
        ds.reduce(function (p, f) { return p.then(function () { return themMotTep(f); }); }, Promise.resolve());
    }
    drop.addEventListener('click', function () { if (!dangGui) fileInput.click(); });
    drop.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); if (!dangGui) fileInput.click(); } });
    fileInput.addEventListener('change', function () { themTep(fileInput.files); fileInput.value = ''; });
    ['dragenter', 'dragover'].forEach(function (ev) {
        drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('over'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
        drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('over'); });
    });
    drop.addEventListener('drop', function (e) { if (e.dataTransfer && e.dataTransfer.files) themTep(e.dataTransfer.files); });

    // ===== Hiển thị lượt trả lời =====
    function iconTep(ten) {
        var e = (String(ten).split('.').pop() || '').toLowerCase();
        if (e === 'pdf') return '<i class="fa-solid fa-file-pdf" style="color:#d32f2f"></i>';
        if (e === 'doc' || e === 'docx') return '<i class="fa-solid fa-file-word" style="color:#1565c0"></i>';
        if (e === 'xls' || e === 'xlsx') return '<i class="fa-solid fa-file-excel" style="color:#2e7d32"></i>';
        if (e === 'ppt' || e === 'pptx') return '<i class="fa-solid fa-file-powerpoint" style="color:#e65100"></i>';
        return '<i class="fa-solid fa-file-lines" style="color:#6b7a5c"></i>';
    }
    function htmlTep(tep) {
        if (!tep || !tep.length) return '';
        var h = '<div class="faqx-att">';
        tep.forEach(function (t) {
            if (t.la_anh) {
                h += '<a href="' + esc(t.url) + '" target="_blank" rel="noopener"><img src="' + esc(t.url) + '" alt="' + esc(t.ten) + '" loading="lazy" decoding="async"></a>';
            } else {
                h += '<a href="' + esc(t.url) + '" target="_blank" rel="noopener">' + iconTep(t.ten) + '<span>' + esc(t.ten) + '</span></a>';
            }
        });
        return h + '</div>';
    }
    function addReply(r) {
        var d = document.createElement('div');
        d.className = 'faqx-msg';
        d.innerHTML = '<b>' + esc(r.nguoi) + '</b><span class="t">' + esc(r.thoi_gian) + '</span><div class="c">' + r.noi_dung + '</div>' + htmlTep(r.tep);
        thread.appendChild(d);
    }
    function closeModal() {
        if (dangGui) return;   // đang gửi thì không cho đóng, tránh mất dữ liệu
        modal.classList.remove('open'); currentId = null;
    }

    function openModal(id) {
        currentId = id;
        thread.innerHTML = '<p class="faqx-muted">Đang tải...</p>';
        ed.innerHTML = ''; files = []; veTep(); setNote('', ''); anTienTrinh();
        document.getElementById('faqxTitle').textContent = '';
        document.getElementById('faqxMeta').textContent = '';
        modal.classList.add('open');
        fetch(getTpl.replace('__ID__', id), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) throw 0; return r.json(); })
            .then(function (d) {
                document.getElementById('faqxTitle').textContent = d.cau_hoi;
                document.getElementById('faqxMeta').textContent = d.nguoi_dang + ' · ' + d.ngay;
                thread.innerHTML = '';
                if (!d.replies.length) thread.innerHTML = '<p class="faqx-muted">Chưa có lượt trả lời nào. Hãy là người đầu tiên trả lời.</p>';
                d.replies.forEach(addReply);
                ed.focus();
            })
            .catch(function () { thread.innerHTML = '<p style="color:#b3261e">Không tải được dữ liệu. Vui lòng thử lại.</p>'; });
    }

    // ===== Gửi (XMLHttpRequest để có phần trăm tải lên) =====
    function guiLen(url, fd, onProg) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.upload.onprogress = function (e) { if (e.lengthComputable) onProg(Math.round(e.loaded / e.total * 100)); };
            xhr.onload = function () {
                var d = {};
                try { d = JSON.parse(xhr.responseText); } catch (e) {}
                resolve({ ok: xhr.status >= 200 && xhr.status < 300, status: xhr.status, d: d });
            };
            xhr.onerror = function () { reject(new Error('network')); };
            xhr.ontimeout = function () { reject(new Error('timeout')); };
            xhr.timeout = 180000;
            xhr.send(fd);
        });
    }

    function submit() {
        if (dangGui || dangNen > 0) return;
        var html = ed.innerHTML.trim();
        if (!ed.textContent.trim()) { setNote('err', 'Vui lòng nhập nội dung trả lời.'); return; }

        dangGui = true; send.disabled = true; setNote('', '');
        var coTep = files.length > 0;
        sendTxt.textContent = 'Đang gửi...';
        if (coTep) hienTienTrinh(0, 'Đang tải lên 0%');

        var fd = new FormData();
        fd.append('noi_dung', html);
        files.forEach(function (f) { fd.append('tep[]', f); });

        guiLen(postTpl.replace('__ID__', currentId), fd, function (p) {
            if (!coTep) return;
            hienTienTrinh(p, p < 100 ? 'Đang tải lên ' + p + '%' : 'Đang xử lý trên máy chủ...');
            sendTxt.textContent = p < 100 ? 'Đang gửi ' + p + '%' : 'Đang xử lý...';
        })
        .then(function (x) {
            if (!x.ok) {
                var msg = x.d.message || 'Gửi thất bại.';
                if (x.d.errors) {
                    var k = Object.keys(x.d.errors)[0];
                    if (k && x.d.errors[k][0]) msg = x.d.errors[k][0];
                }
                if (x.status === 413) msg = 'Tệp quá lớn so với cấu hình máy chủ.';
                if (x.status === 419) msg = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang (F5) rồi thử lại.';
                setNote('err', msg);
                return;
            }
            if (thread.querySelector('.faqx-muted')) thread.innerHTML = '';
            addReply(x.d.reply);
            thread.lastChild.scrollIntoView({ block: 'nearest' });
            ed.innerHTML = ''; files = []; veTep();
            setNote('ok', 'Câu trả lời đã được đăng. Bạn có thể tiếp tục trả lời thêm.');
            var c = document.querySelector('[data-count="' + currentId + '"]');
            if (c) c.textContent = x.d.so_tra_loi;
            var b = document.querySelector('[data-status="' + currentId + '"]');
            if (b) { b.textContent = 'Đã trả lời'; b.classList.remove('none'); }
        })
        .catch(function (e) {
            setNote('err', e && e.message === 'timeout' ? 'Gửi quá lâu, vui lòng kiểm tra mạng rồi thử lại.' : 'Không gửi được. Vui lòng thử lại.');
        })
        .then(function () {
            dangGui = false;
            anTienTrinh();
            capNhatNutGui();
        });
    }

    // Thanh công cụ định dạng
    document.querySelectorAll('.faqx-tools button').forEach(function (b) {
        b.addEventListener('mousedown', function (e) { e.preventDefault(); });
        b.addEventListener('click', function () { ed.focus(); document.execCommand(b.getAttribute('data-cmd'), false, null); });
    });
    // Dán văn bản thuần để không mang định dạng lạ
    ed.addEventListener('paste', function (e) {
        e.preventDefault();
        var t = (e.clipboardData || window.clipboardData).getData('text/plain');
        document.execCommand('insertText', false, t);
    });

    document.querySelectorAll('[data-open-reply]').forEach(function (b) {
        b.addEventListener('click', function () { openModal(b.getAttribute('data-open-reply')); });
    });
    document.getElementById('faqxClose').addEventListener('click', closeModal);
    document.getElementById('faqxBack').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
    send.addEventListener('click', submit);

    // Mở sẵn cửa sổ khi đi từ trang chi tiết: /faqs?tra_loi=ID
    var auto = new URLSearchParams(location.search).get('tra_loi');
    if (auto && document.querySelector('[data-open-reply="' + auto + '"]')) { openModal(auto); }
})();
</script>
@endif
@endsection