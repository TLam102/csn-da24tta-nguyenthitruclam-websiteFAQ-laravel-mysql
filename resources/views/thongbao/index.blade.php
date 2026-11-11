@php
    $laGiangVien = (auth()->user()->vai_tro ?? '') === 'giang_vien';
    $layout      = $laGiangVien ? 'layouts.giangvien' : 'layouts.app';   // <-- đổi 'layouts.app' nếu layout trang chủ của bạn tên khác
@endphp

@extends($layout)

@section('title', 'Thông báo')

@if ($laGiangVien)
    @section('gv_breadcrumb') <span>Thông báo</span> @endsection
@endif

@php
    $tbIcon = function ($t) {
        $s = mb_strtolower(($t->tieu_de ?? '') . ' ' . ($t->noi_dung ?? ''));
        if (\Illuminate\Support\Str::contains($s, 'duyệt'))   return ['fa-circle-check', 'tbp-ok'];
        if (\Illuminate\Support\Str::contains($s, 'trả lời')) return ['fa-comments',     'tbp-reply'];
        return ['fa-envelope', 'tbp-new'];
    };
@endphp

@section($laGiangVien ? 'gv_content' : 'content')
<style>
    .tbp-page { max-width: 1100px; margin: 0 auto; padding: 22px 16px 40px; }
    .tbp-head { display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .tbp-title { margin: 0; font-size: 22px; color: #14532d; }
    .tbp-sub { margin: 4px 0 0; color: #6f7d63; font-size: 14px; }
    .tbp-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; border: 1px solid #cfe3a8; background: #fff;
        color: #1a7a2e; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
    .tbp-btn:hover { background: #eef8d9; }

    .tbp-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }
    .tbp-tabs a { display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border-radius: 999px; background: #fff; border: 1px solid #dbe8bd;
        color: #44503a; font-weight: 600; font-size: 14px; text-decoration: none; transition: .15s; }
    .tbp-tabs a:hover { border-color: #a6e22e; transform: translateY(-1px); }
    .tbp-tabs a span { min-width: 22px; height: 22px; padding: 0 7px; border-radius: 11px; background: #eef8d9; color: #14532d; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center; }
    .tbp-tabs a.active { background: linear-gradient(90deg, #a6e22e, #f1d62a); color: #1b2a1b; border-color: transparent; box-shadow: 0 4px 12px rgba(166,226,46,.35); }
    .tbp-tabs a.active span { background: rgba(255,255,255,.7); }

    .tbp-row { position: relative; display: flex; align-items: flex-start; gap: 14px; background: #fff; border-radius: 14px; padding: 16px 52px 16px 20px; margin-bottom: 12px;
        border: 1px solid #eef2e0; border-left: 4px solid #dbe8bd; box-shadow: 0 2px 10px rgba(0,0,0,.04); transition: .15s; }
    .tbp-row:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,0,0,.08); }
    .tbp-row.unread { border-left-color: #a6e22e; background: #fbfef0; }
    .tbp-link { display: flex; align-items: flex-start; gap: 14px; flex: 1; min-width: 0; color: #1b2a1b; text-decoration: none; }
    .tbp-ic { width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .tbp-ok    { background: #dff3d0; color: #2e6b12; }
    .tbp-reply { background: #d9f3e6; color: #0e7a5a; }
    .tbp-new   { background: #fff3c9; color: #c98a00; }
    .tbp-body { flex: 1; min-width: 0; }
    .tbp-body b { display: block; font-size: 15px; margin-bottom: 3px; }
    .tbp-body p { margin: 0 0 6px; color: #44503a; font-size: 14px; line-height: 1.55; }
    .tbp-body time { color: #8a947c; font-size: 12px; }
    .tbp-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: #e53935; margin-left: 8px; vertical-align: middle; }
    .tbp-del { position: absolute; top: 12px; right: 12px; margin: 0; }
    .tbp-del button { width: 30px; height: 30px; border: 0; border-radius: 50%; background: transparent; color: #8a947c; font-size: 18px; cursor: pointer; }
    .tbp-del button:hover { background: #fde7e7; color: #d32f2f; }

    .tbp-empty { text-align: center; background: #fff; border-radius: 16px; padding: 48px 20px; color: #8a947c; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
    .tbp-empty i { font-size: 46px; color: #c9dd98; margin-bottom: 10px; }
    .tbp-empty p { margin: 0; font-size: 15px; }

    .tbp-pager { display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 18px; }
    .tbp-pager a, .tbp-pager span { padding: 8px 16px; border-radius: 9px; background: #fff; border: 1px solid #dbe8bd; font-size: 14px; font-weight: 600; color: #1a7a2e; text-decoration: none; }
    .tbp-pager a:hover { background: linear-gradient(90deg, #a6e22e, #f1d62a); color: #1b2a1b; border-color: transparent; }
    .tbp-pager .disabled { color: #aab39b; background: #f6f8ee; }
    .tbp-pager .current { background: transparent; border-color: transparent; color: #5d6b52; }
</style>

<div class="tbp-page">
    <div class="tbp-head">
        <div>
            <h2 class="tbp-title">Thông báo</h2>
            <p class="tbp-sub">Câu hỏi được duyệt, có người trả lời và câu hỏi gửi đến bạn.</p>
        </div>
        @if ($stats['chua'] > 0)
            <form action="{{ route('thongbao.readall') }}" method="POST">
                @csrf
                <button type="submit" class="tbp-btn"><i class="fa-solid fa-check-double"></i> Đánh dấu tất cả đã đọc</button>
            </form>
        @endif
    </div>

    <div class="tbp-tabs">
        <a href="{{ route('thongbao.index', ['tab' => 'tat-ca']) }}" class="{{ $tab === 'tat-ca' ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> Tất cả <span>{{ $stats['tong'] }}</span>
        </a>
        <a href="{{ route('thongbao.index', ['tab' => 'chua-doc']) }}" class="{{ $tab === 'chua-doc' ? 'active' : '' }}">
            <i class="fa-solid fa-star"></i> Chưa đọc <span>{{ $stats['chua'] }}</span>
        </a>
        <a href="{{ route('thongbao.index', ['tab' => 'da-doc']) }}" class="{{ $tab === 'da-doc' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-check"></i> Đã đọc <span>{{ $stats['da'] }}</span>
        </a>
    </div>

    @forelse ($items as $t)
        @php [$ic, $lop] = $tbIcon($t); @endphp
        <div class="tbp-row {{ $t->da_doc ? '' : 'unread' }}">
            <a href="{{ route('thongbao.open', $t->id) }}" class="tbp-link">
                <span class="tbp-ic {{ $lop }}"><i class="fa-solid {{ $ic }}"></i></span>
                <span class="tbp-body">
                    <b>{{ $t->tieu_de }}@if (!$t->da_doc)<i class="tbp-dot"></i>@endif</b>
                    <p>{{ $t->noi_dung }}</p>
                    <time>{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '' }}
                        · {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->locale('vi')->diffForHumans() : '' }}</time>
                </span>
            </a>
            <form method="POST" action="{{ route('thongbao.destroy', $t->id) }}" class="tbp-del"
                  onsubmit="return confirm('Bỏ qua thông báo này?');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Bỏ qua" aria-label="Bỏ qua">&times;</button>
            </form>
        </div>
    @empty
        <div class="tbp-empty">
            <i class="fa-regular fa-bell-slash"></i>
            <p>Không có thông báo nào.</p>
        </div>
    @endforelse

    @if ($items->hasPages())
        <div class="tbp-pager">
            @if ($items->onFirstPage())
                <span class="disabled">&laquo; Trước</span>
            @else
                <a href="{{ $items->previousPageUrl() }}">&laquo; Trước</a>
            @endif

            <span class="current">Trang {{ $items->currentPage() }} / {{ $items->lastPage() }}</span>

            @if ($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}">Sau &raquo;</a>
            @else
                <span class="disabled">Sau &raquo;</span>
            @endif
        </div>
    @endif
</div>
@endsection