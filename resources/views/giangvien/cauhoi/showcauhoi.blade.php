@extends('layouts.giangvien')

@section('title', 'Trả lời: ' . $cauHoi->tieu_de)

@section('gv_breadcrumb')
    <a href="{{ route('giangvien.cauhoi.index') }}">Câu hỏi gửi đến tôi</a>
    <i class="fa-solid fa-chevron-right"></i> <span>Trả lời câu hỏi</span>
@endsection

@section('gv_content')

<style>
.rp-grid{--rp-green:#2C8419;--rp-green-dark:#1F6612;--rp-soft:#EAF9CF;--rp-lime:#8CD41C;--rp-yellow:#FFD21F;--rp-ink:#0F2A30;--rp-border:#E1E8DC;--rp-muted:#5F6F73;
  display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:22px;align-items:start}
.rp-main,.rp-side{display:flex;flex-direction:column;gap:20px;min-width:0}
.rp-side{position:sticky;top:90px}
.rp-grid .rp-card{position:relative;background:#fff;border:1px solid var(--rp-border);border-radius:14px;padding:24px 26px;box-shadow:0 8px 22px rgba(15,42,48,.07);animation:rpUp .5s ease both}
.rp-grid .rp-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;border-radius:14px 14px 0 0;background:linear-gradient(90deg,var(--rp-lime),var(--rp-yellow))}
@keyframes rpUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* Câu hỏi */
.rp-qhead{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px}
.rp-grid .pill-done,.rp-grid .pill-wait{display:inline-block;padding:4px 14px;font-size:13px;font-weight:700;border-radius:999px}
.rp-grid .pill-done{background:#DDF6C2;color:var(--rp-green-dark);border:1px solid var(--rp-lime)}
.rp-grid .pill-wait{background:#FFF3B8;color:#6B5300;border:1px solid #FFE57A}
.rp-time{font-size:13px;color:var(--rp-muted)}
.rp-question h2{font-size:22px;line-height:1.4;color:var(--rp-ink);margin:0 0 10px}
.rp-meta{display:flex;flex-wrap:wrap;gap:18px;font-size:14px;color:var(--rp-muted);margin-bottom:16px}
.rp-meta i{color:var(--rp-green);margin-right:6px}
.rp-qbody{background:#F6FAEE;border:1px solid var(--rp-border);border-left:4px solid var(--rp-lime);border-radius:8px;padding:16px 18px;font-size:15px;line-height:1.75;color:#2b3a3f}
.rp-files-title{margin:18px 0 8px;font-size:14px;font-weight:700;color:var(--rp-ink)}
.rp-files-title i{color:var(--rp-green);margin-right:6px}

/* Tiêu đề khối */
.rp-h{display:flex;align-items:center;gap:10px;margin:0 0 16px;font-size:17px;color:var(--rp-ink)}
.rp-h i{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:var(--rp-soft);color:var(--rp-green);font-size:14px}

/* Dòng thời gian */
.rp-timeline{display:flex;flex-direction:column}
.rp-node{position:relative;display:flex;gap:14px;padding-bottom:18px}
.rp-node:not(:last-child)::before{content:'';position:absolute;left:17px;top:38px;bottom:0;width:2px;background:var(--rp-border)}
.rp-node:last-child{padding-bottom:0}
.rp-dot{flex:none;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;border:2px solid #fff;box-shadow:0 0 0 2px var(--rp-border)}
.rp-dot-q{background:#FFF3B8;color:#B07C00}
.rp-dot-a{background:linear-gradient(135deg,#9BE02A,#FFD21F);color:var(--rp-ink)}
.rp-dot-w{background:#F1F4EE;color:var(--rp-muted)}
.rp-bubble{flex:1;min-width:0;background:#fff;border:1px solid var(--rp-border);border-radius:12px;padding:12px 16px}
.rp-bubble-a{background:#F6FAEE;border-color:#CFE8A6}
.rp-bubble-w{border-style:dashed;background:transparent}
.rp-bhead{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:6px;font-size:14px}
.rp-bhead span{color:var(--rp-muted);font-size:13px}
.rp-btext{font-size:14.5px;line-height:1.7;color:#2b3a3f}
.rp-rich ul,.rp-rich ol{padding-left:22px;margin:6px 0}
.rp-rich p{margin:0 0 6px}

/* Trình soạn thảo */
.rp-editor{border:1px solid var(--rp-border);border-radius:12px;overflow:hidden;background:#fff;transition:border-color .2s,box-shadow .2s}
.rp-editor:focus-within{border-color:var(--rp-green);box-shadow:0 0 0 3px rgba(255,210,31,.45)}
.rp-editor.invalid{border-color:#dc2626}
.rp-toolbar{display:flex;align-items:center;gap:4px;padding:8px 10px;background:#F6FAEE;border-bottom:1px solid var(--rp-border)}
.rp-toolbar button{width:34px;height:34px;border:1px solid transparent;border-radius:8px;background:transparent;color:var(--rp-ink);cursor:pointer;transition:.2s}
.rp-toolbar button:hover{background:#FFF3B8;border-color:#FFE57A}
.rp-sep{width:1px;height:22px;background:var(--rp-border);margin:0 6px}
.rp-area{min-height:200px;max-height:420px;overflow:auto;padding:16px 18px;font-size:15px;line-height:1.75;color:var(--rp-ink);outline:none}
.rp-area:empty::before{content:attr(data-placeholder);color:#9aa8a2;pointer-events:none}
.rp-area ul,.rp-area ol{padding-left:24px;margin:6px 0}
.rp-grid .field-error{margin-top:8px;font-size:13px;color:#dc2626}

/* Đính kèm */
.rp-attach{margin-top:16px}
.rp-drop{display:flex;align-items:center;gap:14px;padding:16px 18px;border:2px dashed var(--rp-lime);border-radius:12px;background:#FBFFF0;color:var(--rp-ink);font-size:14px;cursor:pointer;transition:.2s}
.rp-drop:hover{background:var(--rp-soft);border-color:var(--rp-green)}
.rp-drop i{font-size:26px;color:var(--rp-green)}
.rp-filelist{list-style:none;margin:10px 0 0;padding:0;display:flex;flex-direction:column;gap:8px}
.rp-filelist li{display:flex;align-items:center;gap:10px;padding:8px 12px;background:#fff;border:1px solid var(--rp-border);border-radius:10px;font-size:14px}
.rp-filelist li i{color:var(--rp-green)}
.rp-filelist li span{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rp-filelist li small{color:var(--rp-muted)}
.rp-filelist li button{width:26px;height:26px;border:none;border-radius:50%;background:#fdecec;color:#dc2626;cursor:pointer}
.rp-filelist li button:hover{background:#dc2626;color:#fff}

.rp-note{margin-top:16px;padding:12px 16px;border-radius:10px;background:#E6F2F0;border:1px solid #B7DAD5;color:#134E4A;font-size:14px}
.rp-note i{margin-right:6px}

/* Nút */
.rp-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:18px}
.rp-actions-right{display:flex;gap:10px;flex-wrap:wrap}
.rp-actions .btn-submit{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;font-size:15px;font-weight:700;font-family:inherit;color:var(--rp-ink);background:linear-gradient(135deg,#9BE02A 0%,#FFD21F 100%);border:none;border-radius:10px;box-shadow:0 4px 12px rgba(140,212,28,.45);cursor:pointer;transition:transform .2s,box-shadow .2s}
.rp-actions .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 18px rgba(240,184,0,.5)}
.rp-actions .btn-submit:disabled{opacity:.7;cursor:wait;transform:none}
.rp-actions .gv-btn-outline{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;font-size:14px;font-weight:600;font-family:inherit;color:var(--rp-green-dark);background:#fff;border:2px solid var(--rp-green);border-radius:10px;cursor:pointer;text-decoration:none;transition:.2s}
.rp-actions .gv-btn-outline:hover{background:var(--rp-soft)}

/* Cột phải */
.rp-sv{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.rp-sv strong{display:block;font-size:16px;color:var(--rp-ink)}
.rp-sv small{color:var(--rp-muted);word-break:break-all}
.rp-grid .rp-sv-avatar{flex:none;width:54px;height:54px;border-radius:50%;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:var(--rp-ink);background:linear-gradient(135deg,#9BE02A,#FFD21F)}
.rp-sv-avatar img{width:100%;height:100%;object-fit:cover}
.rp-sv-info{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;font-size:14px;color:#2b3a3f}
.rp-sv-info i{width:18px;color:var(--rp-green);margin-right:6px}
.rp-hint{margin:-6px 0 12px;font-size:13px;color:var(--rp-muted)}
.rp-tpls{display:flex;flex-direction:column;gap:10px}
.rp-tpl{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--rp-border);border-radius:10px;background:#FBFFF0}
.rp-tpl-title{font-size:14px;font-weight:600;color:var(--rp-ink)}
.rp-tpl-title em{margin-left:6px;padding:1px 8px;font-size:11px;font-style:normal;font-weight:700;color:#6B5300;background:#FFF3B8;border-radius:999px}
.rp-tpl-btns{display:flex;gap:6px;flex:none}
.rp-tpl-btns form{margin:0}
.rp-mini{padding:5px 12px;font-size:13px;font-weight:600;font-family:inherit;color:var(--rp-green-dark);background:#fff;border:1px solid var(--rp-lime);border-radius:8px;cursor:pointer;transition:.2s}
.rp-mini:hover{background:linear-gradient(135deg,#9BE02A,#FFD21F);border-color:transparent;color:var(--rp-ink)}
.rp-mini-del{color:#dc2626;border-color:#f5c2c2;background:#fdecec}
.rp-mini-del:hover{background:#dc2626;border-color:#dc2626;color:#fff}

@media (max-width:1000px){
  .rp-grid{grid-template-columns:1fr}
  .rp-side{position:static}
}
@media (max-width:560px){
  .rp-grid .rp-card{padding:18px 16px}
  .rp-actions{flex-direction:column-reverse;align-items:stretch}
  .rp-actions-right{flex-direction:column}
}
@media (prefers-reduced-motion:reduce){.rp-grid .rp-card{animation:none}}
</style>
@php
    $svAnh = file_url($sv->anh_dai_dien);
    $daTraLoi = $traLois->count() > 0;
@endphp

<div class="rp-grid">

    {{-- ================= CỘT CHÍNH ================= --}}
    <div class="rp-main">

        {{-- Chi tiết câu hỏi --}}
        <section class="rp-card rp-question">
            <div class="rp-qhead">
                @if ($daTraLoi)
                    <span class="pill pill-done">Đã trả lời</span>
                @else
                    <span class="pill pill-wait">Chưa trả lời</span>
                @endif
                <span class="rp-time"><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($cauHoi->created_at)->format('d/m/Y H:i') }}</span>
            </div>

            <h2>{{ $cauHoi->tieu_de }}</h2>

            <div class="rp-meta">
                <span><i class="fa-solid fa-user"></i> {{ $cauHoi->nguoi_gui ?? 'Ẩn danh' }}</span>
                <span><i class="fa-solid fa-folder"></i> {{ $cauHoi->tendanhmuc ?? 'Chưa phân loại' }}</span>
            </div>

            <div class="rp-qbody">{!! nl2br(e($cauHoi->noi_dung)) !!}</div>

            @if ($tepCauHoi->count())
                <div class="rp-files-title"><i class="fa-solid fa-paperclip"></i> Tệp đính kèm của sinh viên ({{ $tepCauHoi->count() }})</div>
                @include('giangvien.cauhoi._tep', ['files' => $tepCauHoi])
            @endif
        </section>

        {{-- Lịch sử trao đổi --}}
        <section class="rp-card">
            <h3 class="rp-h"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử trao đổi</h3>

            <div class="rp-timeline">
                <div class="rp-node">
                    <span class="rp-dot rp-dot-q"><i class="fa-solid fa-circle-question"></i></span>
                    <div class="rp-bubble">
                        <div class="rp-bhead">
                            <strong>{{ $cauHoi->nguoi_gui ?? 'Sinh viên' }}</strong>
                            <span>{{ \Carbon\Carbon::parse($cauHoi->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="rp-btext">Đã gửi câu hỏi.</div>
                    </div>
                </div>

                @foreach ($traLois as $tl)
                    <div class="rp-node">
                        <span class="rp-dot rp-dot-a"><i class="fa-solid fa-reply"></i></span>
                        <div class="rp-bubble rp-bubble-a">
                            <div class="rp-bhead">
                                <strong>{{ $tl->nguoi_tra_loi ?? 'Giảng viên' }}</strong>
                                <span>{{ \Carbon\Carbon::parse($tl->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="rp-btext rp-rich">
                                @if ($tl->noi_dung !== strip_tags($tl->noi_dung))
                                    {!! \App\Http\Controllers\GiangVienCauHoiController::cleanHtml($tl->noi_dung) !!}
                                @else
                                    {!! nl2br(e($tl->noi_dung)) !!}
                                @endif
                            </div>
                            @if (isset($tepTraLoi[$tl->tra_loi_id]))
                                @include('giangvien.cauhoi._tep', ['files' => $tepTraLoi[$tl->tra_loi_id]])
                            @endif
                        </div>
                    </div>
                @endforeach

                @if (!$daTraLoi)
                    <div class="rp-node">
                        <span class="rp-dot rp-dot-w"><i class="fa-regular fa-hourglass-half"></i></span>
                        <div class="rp-bubble rp-bubble-w"><div class="rp-btext">Đang chờ bạn phản hồi.</div></div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Khung soạn câu trả lời --}}
        <section class="rp-card" id="reply">
            <h3 class="rp-h"><i class="fa-solid fa-pen-to-square"></i> {{ $daTraLoi ? 'Gửi thêm phản hồi' : 'Soạn câu trả lời' }}</h3>

            <form method="POST" action="{{ route('giangvien.cauhoi.reply', $cauHoi->cau_hoi_pk) }}" enctype="multipart/form-data" id="replyForm" novalidate>
                @csrf

                <div class="rp-editor {{ $errors->has('noi_dung') ? 'invalid' : '' }}">
                    <div class="rp-toolbar">
                        <button type="button" data-cmd="bold" title="In đậm"><i class="fa-solid fa-bold"></i></button>
                        <button type="button" data-cmd="italic" title="In nghiêng"><i class="fa-solid fa-italic"></i></button>
                        <button type="button" data-cmd="underline" title="Gạch chân"><i class="fa-solid fa-underline"></i></button>
                        <span class="rp-sep"></span>
                        <button type="button" data-cmd="insertUnorderedList" title="Gạch đầu dòng"><i class="fa-solid fa-list-ul"></i></button>
                        <button type="button" data-cmd="insertOrderedList" title="Đánh số"><i class="fa-solid fa-list-ol"></i></button>
                        <span class="rp-sep"></span>
                        <button type="button" data-cmd="removeFormat" title="Xóa định dạng"><i class="fa-solid fa-eraser"></i></button>
                    </div>

                    <div id="editor" class="rp-area" contenteditable="true"
                         data-placeholder="Nhập nội dung trả lời cho sinh viên...">{!! \App\Http\Controllers\GiangVienCauHoiController::cleanHtml(old('noi_dung', '')) !!}</div>
                    <textarea name="noi_dung" id="noiDung" hidden></textarea>
                </div>
                @error('noi_dung') <p class="field-error">{{ $message }}</p> @enderror

                {{-- Đính kèm tệp --}}
                <div class="rp-attach">
                    <label for="tepInput" class="rp-drop">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span><b>Đính kèm tệp</b> (ảnh, PDF, Word, Excel...) - tối đa 5 tệp, mỗi tệp 5MB</span>
                    </label>
                    <input type="file" id="tepInput" multiple hidden
                           accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                    <ul class="rp-filelist" id="fileList"></ul>
                    @if ($errors->first('tep') || $errors->first('tep.*'))
                        <p class="field-error">{{ $errors->first('tep') ?: $errors->first('tep.*') }}</p>
                    @endif
                </div>

                <div class="rp-note"><i class="fa-solid fa-circle-info"></i> Câu trả lời đã được đăng. Sinh viên sẽ nhận được thông báo.</div>

                <div class="rp-actions">
                    <a href="{{ route('giangvien.cauhoi.index') }}" class="gv-btn-outline"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
                    <div class="rp-actions-right">
                        <button type="button" class="gv-btn-outline" id="saveTpl"><i class="fa-regular fa-bookmark"></i> Lưu làm mẫu</button>
                        <button type="submit" class="btn-submit" id="sendBtn"><i class="fa-solid fa-paper-plane"></i> Gửi câu trả lời</button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    {{-- ================= CỘT PHẢI ================= --}}
    <aside class="rp-side">

        {{-- Thông tin sinh viên --}}
        <section class="rp-card">
            <h3 class="rp-h"><i class="fa-solid fa-user-graduate"></i> Thông tin sinh viên</h3>
            <div class="rp-sv">
                <span class="gvx-avatar rp-sv-avatar">
                    @if ($svAnh)
                        <img src="{{ $svAnh }}" alt="">
                    @else
                        {{ mb_strtoupper(mb_substr($cauHoi->nguoi_gui ?? '?', 0, 1)) }}
                    @endif
                </span>
                <div>
                    <strong>{{ $sv->name ?? ($cauHoi->nguoi_gui ?? 'Ẩn danh') }}</strong>
                    <small>{{ $sv->email ?? '' }}</small>
                </div>
            </div>
            <ul class="rp-sv-info">
                <li><i class="fa-regular fa-calendar"></i> Tham gia: {{ !empty($sv->created_at) ? \Carbon\Carbon::parse($sv->created_at)->format('d/m/Y') : '—' }}</li>
                <li><i class="fa-solid fa-inbox"></i> Câu hỏi gửi đến bạn: <b>{{ $svStats['gui_den_toi'] }}</b></li>
                <li><i class="fa-solid fa-comments"></i> Tổng câu hỏi đã gửi: <b>{{ $svStats['tong'] }}</b></li>
            </ul>
        </section>

        {{-- Câu trả lời mẫu --}}
        <section class="rp-card">
            <h3 class="rp-h"><i class="fa-solid fa-bolt"></i> Câu trả lời mẫu</h3>
            <p class="rp-hint">Bấm "Chèn" để đưa mẫu vào khung soạn thảo, sau đó chỉnh sửa theo ý bạn.</p>

            <div class="rp-tpls">
                @forelse ($mau as $m)
                    <div class="rp-tpl">
                        <div class="rp-tpl-title">
                            {{ $m->tieu_de }}
                            @if ($m->user_id) <em>Của tôi</em> @endif
                        </div>
                        <div class="rp-tpl-btns">
                            <button type="button" class="rp-mini js-insert" data-body="{{ $m->noi_dung }}">Chèn</button>
                            @if ($m->user_id)
                                <form method="POST" action="{{ route('giangvien.mau.destroy', $m->id) }}" onsubmit="return confirm('Xóa mẫu này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rp-mini rp-mini-del" title="Xóa"><i class="fa-regular fa-trash-can"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="gvx-muted">Chưa có mẫu nào.</p>
                @endforelse
            </div>
        </section>
    </aside>
</div>

<script>
(function () {
    var editor  = document.getElementById('editor');
    var hidden  = document.getElementById('noiDung');
    var form    = document.getElementById('replyForm');
    var tepIn   = document.getElementById('tepInput');
    var listEl  = document.getElementById('fileList');
    var MAX_FILES = 5, MAX_SIZE = 5 * 1024 * 1024;
    var dt = new DataTransfer();

    /* ----- Thanh công cụ ----- */
    document.querySelectorAll('.rp-toolbar button').forEach(function (b) {
        b.addEventListener('mousedown', function (e) { e.preventDefault(); });
        b.addEventListener('click', function () {
            editor.focus();
            document.execCommand(b.dataset.cmd, false, null);
        });
    });

    editor.addEventListener('input', function () {
        if (editor.innerHTML === '<br>') editor.innerHTML = '';
    });

    /* Dán văn bản thuần để không mang theo định dạng lạ */
    editor.addEventListener('paste', function (e) {
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text/plain');
        document.execCommand('insertText', false, text);
    });

    /* ----- Chèn câu trả lời mẫu ----- */
    document.querySelectorAll('.js-insert').forEach(function (b) {
        b.addEventListener('click', function () {
            var html = b.dataset.body;
            if (editor.innerText.trim() === '') {
                editor.innerHTML = html;
            } else {
                editor.innerHTML += '<br>' + html;
            }
            editor.focus();
            document.getElementById('reply').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    /* ----- Lưu làm mẫu ----- */
    document.getElementById('saveTpl').addEventListener('click', function () {
        if (editor.innerText.trim() === '') { alert('Khung soạn thảo đang trống, hãy nhập nội dung trước.'); return; }
        var title = prompt('Đặt tên cho câu trả lời mẫu:');
        if (!title || !title.trim()) return;

        var f = document.createElement('form');
        f.method = 'POST';
        f.action = '{{ route('giangvien.mau.store') }}';
        function add(n, v) { var i = document.createElement(n === 'noi_dung' ? 'textarea' : 'input'); i.name = n; i.value = v; i.hidden = true; f.appendChild(i); }
        add('_token', '{{ csrf_token() }}');
        add('tieu_de', title.trim());
        add('noi_dung', editor.innerHTML);
        document.body.appendChild(f);
        f.submit();
    });

    /* ----- Tệp đính kèm ----- */
    function renderFiles() {
        listEl.innerHTML = '';
        Array.prototype.forEach.call(dt.files, function (f, i) {
            var li = document.createElement('li');
            li.innerHTML = '<i class="fa-solid fa-paperclip"></i><span></span><small>' + Math.round(f.size / 1024) + ' KB</small>' +
                           '<button type="button" aria-label="Bỏ tệp"><i class="fa-solid fa-xmark"></i></button>';
            li.querySelector('span').textContent = f.name;
            li.querySelector('button').addEventListener('click', function () {
                var n = new DataTransfer();
                Array.prototype.forEach.call(dt.files, function (x, j) { if (j !== i) n.items.add(x); });
                dt = n; tepIn.files = dt.files; renderFiles();
            });
            listEl.appendChild(li);
        });
    }

    tepIn.addEventListener('change', function () {
        Array.prototype.forEach.call(tepIn.files, function (f) {
            if (dt.files.length >= MAX_FILES) { alert('Chỉ được đính kèm tối đa ' + MAX_FILES + ' tệp.'); return; }
            if (f.size > MAX_SIZE) { alert('Tệp "' + f.name + '" vượt quá 5MB.'); return; }
            dt.items.add(f);
        });
        tepIn.files = dt.files;
        renderFiles();
    });

    /* Input chọn tệp cần có name để gửi đi */
    tepIn.name = 'tep[]';

    /* ----- Gửi ----- */
    form.addEventListener('submit', function (e) {
        if (editor.innerText.trim() === '') {
            e.preventDefault();
            alert('Vui lòng nhập nội dung trả lời.');
            editor.focus();
            return;
        }
        hidden.value = editor.innerHTML;
        var btn = document.getElementById('sendBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
    });
})();
</script>
@endsection