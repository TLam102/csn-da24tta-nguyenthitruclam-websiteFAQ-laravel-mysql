{{--
    Ô chọn giảng viên có ảnh đại diện + tên (giống kiểu "ảnh tròn + tên" ở thanh đăng nhập).
    Dùng trong form gửi câu hỏi:  @include('partials.chon-giang-vien')
    Giá trị gửi đi: giang_vien_id (id giảng viên), rỗng = gửi chung.
--}}
@php
    $ten = 'giang_vien_id';
    $dangChon = (string) old($ten, '');

    // Lấy đúng danh sách giảng viên mà controller đã truyền ($giangViens), kèm ảnh từ model User
    $ids = collect($giangViens ?? [])->pluck('id')->filter()->values();
    $users = $ids->isNotEmpty()
        ? \App\Models\User::whereIn('id', $ids)->orderBy('name')->get()
        : collect();

    $dsGv = $users->map(function ($u) {
        $ten = trim(preg_replace('/^(GS\.|PGS\.|TS\.|ThS\.|CN\.|KS\.)\s*/u', '', $u->name));
        return [
            'id'   => (string) $u->id,
            'name' => $u->name,
            'anh'  => $u->avatar_url ?? null,
            'ch'   => mb_strtoupper(mb_substr($ten !== '' ? $ten : $u->name, 0, 1)),
        ];
    })->values();

    $chon = $dsGv->firstWhere('id', $dangChon);
@endphp

<style>
    .gvp { position: relative; }
    .gvp-btn { width: 100%; min-height: 56px; display: flex; align-items: center; gap: 12px; padding: 8px 16px; border: 1px solid #dfe8d3; border-radius: 12px; background: #fff; font: inherit; color: inherit; text-align: left; cursor: pointer; box-sizing: border-box; }
    .gvp-btn:focus, .gvp.open .gvp-btn { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,.15); }
    .gvp-btn.invalid { border-color: #c62828; }
    .gvp-caret { margin-left: auto; color: #6b7a5c; transition: transform .15s; }
    .gvp.open .gvp-caret { transform: rotate(180deg); }

    .gvp-av { position: relative; width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; overflow: hidden; background: linear-gradient(135deg, #b6dc2a, #f2d216); color: #1b3a00; display: flex; align-items: center; justify-content: center; font-weight: 700; }
    .gvp-av.none { background: #eef2e8; color: #6b7a5c; }
    .gvp-av img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
    .gvp-nm { font-weight: 600; color: #16301a; }

    .gvp-panel { display: none; position: absolute; z-index: 50; left: 0; right: 0; top: calc(100% + 6px); background: #fff; border: 1px solid #dfe8d3; border-radius: 12px; box-shadow: 0 14px 34px rgba(0,0,0,.14); overflow: hidden; }
    .gvp.open .gvp-panel { display: block; }
    .gvp-search { width: 100%; box-sizing: border-box; border: 0; border-bottom: 1px solid #edf2e6; padding: 12px 16px; font: inherit; outline: none; background: #fff; }
    .gvp-list { max-height: 320px; overflow-y: auto; margin: 0; padding: 6px; list-style: none; }
    .gvp-item { display: flex; align-items: center; gap: 12px; padding: 8px 10px; border-radius: 10px; cursor: pointer; }
    .gvp-item:hover { background: #f1f8e6; }
    .gvp-item[aria-selected="true"] { background: #e3f4d6; }
    .gvp-empty { padding: 14px 16px; color: #6b7a5c; display: none; }
</style>

<div class="gvp" id="gvp">
    <input type="hidden" name="{{ $ten }}" id="gvpValue" value="{{ $chon['id'] ?? '' }}">

    <button type="button" class="gvp-btn {{ $errors->has($ten) ? 'invalid' : '' }}" id="gvpBtn" aria-haspopup="listbox" aria-expanded="false">
        <span class="gvp-av {{ $chon ? '' : 'none' }}" id="gvpBtnAv">
            @if ($chon)
                <span>{{ $chon['ch'] }}</span>
                @if ($chon['anh']) <img src="{{ $chon['anh'] }}" alt="" onerror="this.remove()"> @endif
            @else
                <i class="fa-solid fa-users"></i>
            @endif
        </span>
        <span class="gvp-nm" id="gvpBtnText">{{ $chon['name'] ?? 'Không chọn (gửi chung)' }}</span>
        <i class="fa-solid fa-chevron-down gvp-caret"></i>
    </button>

    <div class="gvp-panel" id="gvpPanel">
        <input type="text" class="gvp-search" id="gvpSearch" placeholder="Tìm giảng viên..." autocomplete="off">
        <ul class="gvp-list" id="gvpList" role="listbox">
            <li class="gvp-item" role="option" data-id="" data-name="Không chọn (gửi chung)" data-search="khong chon gui chung"
                aria-selected="{{ $chon ? 'false' : 'true' }}">
                <span class="gvp-av none"><i class="fa-solid fa-users"></i></span>
                <span class="gvp-nm">Không chọn (gửi chung)</span>
            </li>
            @foreach ($dsGv as $g)
                <li class="gvp-item" role="option"
                    data-id="{{ $g['id'] }}" data-name="{{ $g['name'] }}" data-ch="{{ $g['ch'] }}" data-anh="{{ $g['anh'] }}"
                    data-search="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($g['name'])) }}"
                    aria-selected="{{ $dangChon === $g['id'] ? 'true' : 'false' }}">
                    <span class="gvp-av">
                        <span>{{ $g['ch'] }}</span>
                        @if ($g['anh']) <img src="{{ $g['anh'] }}" alt="" loading="lazy" onerror="this.remove()"> @endif
                    </span>
                    <span class="gvp-nm">{{ $g['name'] }}</span>
                </li>
            @endforeach
        </ul>
        <div class="gvp-empty" id="gvpEmpty">Không tìm thấy giảng viên.</div>
    </div>
</div>

<script>
(function () {
    var root = document.getElementById('gvp');
    var btn = document.getElementById('gvpBtn');
    var list = document.getElementById('gvpList');
    var search = document.getElementById('gvpSearch');
    var empty = document.getElementById('gvpEmpty');
    var value = document.getElementById('gvpValue');
    var btnAv = document.getElementById('gvpBtnAv');
    var btnText = document.getElementById('gvpBtnText');

    function open() { root.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); search.value = ''; filter(''); search.focus(); }
    function close() { root.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); }

    function filter(q) {
        q = q.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
        var shown = 0;
        list.querySelectorAll('.gvp-item').forEach(function (li) {
            var ok = !q || li.getAttribute('data-search').indexOf(q) !== -1;
            li.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });
        empty.style.display = shown ? 'none' : 'block';
    }

    function choose(li) {
        var id = li.getAttribute('data-id');
        value.value = id;
        btnText.textContent = li.getAttribute('data-name');
        list.querySelectorAll('.gvp-item').forEach(function (x) { x.setAttribute('aria-selected', x === li ? 'true' : 'false'); });

        btnAv.className = 'gvp-av' + (id ? '' : ' none');
        btnAv.innerHTML = '';
        if (!id) {
            btnAv.innerHTML = '<i class="fa-solid fa-users"></i>';
        } else {
            var letter = document.createElement('span');
            letter.textContent = li.getAttribute('data-ch');
            btnAv.appendChild(letter);
            var anh = li.getAttribute('data-anh');
            if (anh) {
                var img = document.createElement('img');
                img.alt = '';
                img.onerror = function () { img.remove(); };
                img.src = anh;
                btnAv.appendChild(img);
            }
        }
        close();
        btn.focus();
    }

    btn.addEventListener('click', function () { root.classList.contains('open') ? close() : open(); });
    search.addEventListener('input', function () { filter(search.value); });
    list.addEventListener('click', function (e) { var li = e.target.closest('.gvp-item'); if (li) choose(li); });
    document.addEventListener('click', function (e) { if (!root.contains(e.target)) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();
</script>