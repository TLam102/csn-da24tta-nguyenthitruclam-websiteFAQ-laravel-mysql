@auth
@php
    $tbDs   = \Illuminate\Support\Facades\DB::table('thong_bao')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

    $tbChua = \Illuminate\Support\Facades\DB::table('thong_bao')
                ->where('user_id', auth()->id())
                ->where('da_doc', false)
                ->count();

    $tbIcon = function ($t) {
        $s = mb_strtolower(($t->tieu_de ?? '') . ' ' . ($t->noi_dung ?? ''));
        if (\Illuminate\Support\Str::contains($s, 'duyệt'))   return ['fa-circle-check', 'cth-ok'];
        if (\Illuminate\Support\Str::contains($s, 'trả lời')) return ['fa-comments',     'cth-reply'];
        return ['fa-envelope', 'cth-new'];
    };
@endphp

<style>
    .cth-wrap { position: relative; display: inline-block; flex-shrink: 0; }
    .cth-wrap .cth-bell { position: relative; width: 40px; height: 40px; border: 0; border-radius: 50%; background: none; cursor: pointer; color: #44503a; font-size: 19px; padding: 0; }
    .cth-wrap .cth-bell:hover { background: #eef8d9; }
    .cth-wrap .cth-bell em { position: absolute; top: 2px; right: 0; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px; background: #e53935; color: #fff;
        font-style: normal; font-size: 10px; font-weight: 700; line-height: 18px; text-align: center; }

    .cth-wrap .cth-panel { display: none; position: absolute; right: 0; top: calc(100% + 10px); width: 380px; max-width: calc(100vw - 24px);
        background: #fff; border: 1px solid #e6ecd2; border-top: 4px solid #a6e22e; border-radius: 14px; box-shadow: 0 16px 38px rgba(0,0,0,.16);
        z-index: 1000; overflow: hidden; text-align: left; }
    .cth-wrap.open .cth-panel { display: block; }

    .cth-wrap .cth-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid #e6ecd2; }
    .cth-wrap .cth-head strong { font-size: 16px; color: #14532d; }
    .cth-wrap .cth-head form { margin: 0; }
    .cth-wrap .cth-head button { border: 0; background: none; color: #1a7a2e; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; padding: 0; }
    .cth-wrap .cth-head button:hover { text-decoration: underline; }

    .cth-wrap .cth-list { max-height: 420px; overflow-y: auto; }
    .cth-wrap .cth-row { position: relative; border-bottom: 1px solid #f0f3e4; background: #fff; }
    .cth-wrap .cth-row.unread { background: #fbfef0; }
    .cth-wrap .cth-row:hover { background: #f7fbe9; }
    .cth-wrap a.cth-item { display: flex; align-items: flex-start; gap: 12px; padding: 13px 40px 13px 16px; margin: 0; border: 0;
        border-radius: 0; box-shadow: none; background: transparent; color: #1b2a1b; text-decoration: none; width: auto; }

    .cth-wrap .cth-x-form { position: absolute; top: 8px; right: 8px; margin: 0; }
    .cth-wrap .cth-x { width: 24px; height: 24px; border: 0; border-radius: 50%; background: transparent; color: #8a947c; font-size: 15px; line-height: 1; cursor: pointer; padding: 0; }
    .cth-wrap .cth-x:hover { background: #fde7e7; color: #d32f2f; }

    .cth-wrap .cth-ic { width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 16px; }
    .cth-wrap .cth-ok    { background: #dff3d0; color: #2e6b12; }
    .cth-wrap .cth-reply { background: #d9f3e6; color: #0e7a5a; }
    .cth-wrap .cth-new   { background: #fff3c9; color: #c98a00; }

    .cth-wrap .cth-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
    .cth-wrap .cth-body b { font-size: 14px; line-height: 1.35; }
    .cth-wrap .cth-body small { font-size: 13px; color: #5d6b52; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .cth-wrap .cth-body time { font-size: 12px; color: #8a947c; }
    .cth-wrap .cth-dot { width: 9px; height: 9px; border-radius: 50%; background: #e53935; margin-top: 6px; flex-shrink: 0; }

    .cth-wrap .cth-empty { text-align: center; padding: 34px 16px; color: #8a947c; }
    .cth-wrap .cth-empty i { font-size: 34px; color: #c9dd98; }
    .cth-wrap .cth-empty p { margin: 8px 0 0; font-size: 14px; }

    .cth-wrap a.cth-all { display: block; text-align: center; padding: 12px; font-size: 14px; font-weight: 700; color: #1a7a2e; border-top: 1px solid #e6ecd2;
        background: #fafdf0; text-decoration: none; }
    .cth-wrap a.cth-all:hover { background: #eef8d9; }
</style>

<div class="cth-wrap" id="cthWrap">
    <button type="button" class="cth-bell" id="cthBtn" aria-label="Thông báo">
        <i class="fa-solid fa-bell"></i>
        @if ($tbChua > 0)<em id="cthCount">{{ $tbChua > 99 ? '99+' : $tbChua }}</em>@endif
    </button>

    <div class="cth-panel">
        <div class="cth-head">
            <strong>Thông báo</strong>
            @if ($tbChua > 0)
                <form method="POST" action="{{ route('thongbao.readall') }}">
                    @csrf
                    <button type="submit">Đánh dấu tất cả đã đọc</button>
                </form>
            @endif
        </div>

        <div class="cth-list" id="cthList">
            @forelse ($tbDs as $t)
                @php [$ic, $lop] = $tbIcon($t); @endphp
                <div class="cth-row {{ $t->da_doc ? '' : 'unread' }}" data-unread="{{ $t->da_doc ? 0 : 1 }}">
                    <a href="{{ route('thongbao.open', $t->id) }}" class="cth-item">
                        <span class="cth-ic {{ $lop }}"><i class="fa-solid {{ $ic }}"></i></span>
                        <span class="cth-body">
                            <b>{{ $t->tieu_de }}</b>
                            <small>{{ $t->noi_dung }}</small>
                            <time>{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->locale('vi')->diffForHumans() : '' }}</time>
                        </span>
                        @if (!$t->da_doc)<i class="cth-dot"></i>@endif
                    </a>
                    <form method="POST" action="{{ route('thongbao.destroy', $t->id) }}" class="cth-x-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cth-x" title="Bỏ qua" aria-label="Bỏ qua thông báo">&times;</button>
                    </form>
                </div>
            @empty
                <div class="cth-empty">
                    <i class="fa-regular fa-bell-slash"></i>
                    <p>Chưa có thông báo nào.</p>
                </div>
            @endforelse
        </div>

        <a href="{{ route('thongbao.index') }}" class="cth-all">Xem tất cả thông báo</a>
    </div>
</div>

<script>
    (function () {
        var wrap = document.getElementById('cthWrap');
        if (!wrap || wrap.dataset.ready) return;
        wrap.dataset.ready = '1';

        document.getElementById('cthBtn').addEventListener('click', function (e) {
            e.stopPropagation();
            wrap.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) wrap.classList.remove('open');
        });

        // Bấm ✕: xóa thông báo ngay, không tải lại trang
        wrap.querySelectorAll('.cth-x-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var row = form.closest('.cth-row');
                var token = form.querySelector('input[name=_token]').value;

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ _method: 'DELETE' })
                }).then(function (res) {
                    if (!res.ok) throw new Error('fail');

                    // Giảm số đỏ nếu thông báo chưa đọc
                    if (row.dataset.unread === '1') {
                        var badge = document.getElementById('cthCount');
                        if (badge) {
                            var n = parseInt(badge.textContent, 10);
                            if (isNaN(n) || n <= 1) badge.remove(); else badge.textContent = n - 1;
                        }
                    }
                    row.remove();

                    // Hết thông báo thì hiện trạng thái trống
                    var list = document.getElementById('cthList');
                    if (!list.querySelector('.cth-row')) {
                        list.innerHTML = '<div class="cth-empty"><i class="fa-regular fa-bell-slash"></i><p>Chưa có thông báo nào.</p></div>';
                    }
                }).catch(function () {
                    form.submit(); // lỗi mạng: gửi form bình thường
                });
            });
        });
    })();
</script>
@endauth