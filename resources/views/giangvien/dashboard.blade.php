@extends('layouts.giangvien')

@section('title', 'Tổng quan giảng viên')

@section('gv_content')
<div class="gvx-grid">

    {{-- ================= CỘT TRÁI ================= --}}
    <div class="gvx-col">

        {{-- Banner chào --}}
        <section class="gvx-banner">
            <div>
                <h2>Xin chào, {{ auth()->user()->name }}</h2>
                <p>
                    @if ($stats['chua'] > 0)
                        Bạn có <b>{{ $stats['chua'] }}</b> câu hỏi đang chờ trả lời.
                    @else
                        Bạn đã trả lời hết các câu hỏi. Làm tốt lắm!
                    @endif
                </p>
                <div class="gvx-banner-actions">
                    <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'chua-tra-loi']) }}" class="gvx-btn-white">Xem câu hỏi</a>
                    <a href="{{ route('cauhoi.create') }}" class="gvx-btn-ghost"><i class="fa-solid fa-plus"></i> Đặt câu hỏi</a>
                </div>
            </div>
            <i class="fa-solid fa-chalkboard-user gvx-banner-icon"></i>
        </section>

        {{-- 4 thẻ thống kê --}}
        <section class="gvx-stats">
            <a class="gvx-stat" href="{{ route('faqs.index') }}">
                <span class="gvx-stat-ic ic-blue"><i class="fa-solid fa-book"></i></span>
                <div><b>{{ $faq['total'] }}</b><span>Tổng FAQ</span></div>
            </a>
            <a class="gvx-stat" href="{{ route('giangvien.cauhoi.index', ['tab' => 'moi']) }}">
                <span class="gvx-stat-ic ic-red"><i class="fa-solid fa-bell"></i></span>
                <div><b>{{ $stats['moi'] }}</b><span>Câu hỏi mới</span></div>
            </a>
            <a class="gvx-stat" href="{{ route('giangvien.cauhoi.index', ['tab' => 'chua-tra-loi']) }}">
                <span class="gvx-stat-ic ic-orange"><i class="fa-regular fa-clock"></i></span>
                <div><b>{{ $stats['chua'] }}</b><span>Chờ xử lý</span></div>
            </a>
            <a class="gvx-stat" href="{{ route('faqs.index') }}">
                <span class="gvx-stat-ic ic-green"><i class="fa-solid fa-circle-check"></i></span>
                <div><b>{{ $faq['published'] }}</b><span>FAQ đã đăng</span></div>
            </a>
        </section>

        {{-- Bảng câu hỏi sinh viên (giãn đầy cột) --}}
        <section class="gvx-panel gvx-grow">
            <div class="gvx-panel-head">
                <h3>Câu hỏi của sinh viên</h3>
                <a href="{{ route('giangvien.cauhoi.index') }}">Xem tất cả</a>
            </div>

            <div class="gvx-table-wrap">
                <table class="gvx-table">
                    <thead>
                        <tr>
                            <th>Câu hỏi</th>
                            <th>Sinh viên</th>
                            <th>Danh mục</th>
                            <th>Ngày gửi</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($recent as $c)
                        <tr>
                            <td class="t-title">
                                <a href="{{ route('giangvien.cauhoi.show', $c->cau_hoi_pk) }}">{{ \Illuminate\Support\Str::limit($c->tieu_de, 48) }}</a>
                            </td>
                            <td>{{ $c->nguoi_gui ?? 'Ẩn danh' }}</td>
                            <td>{{ $c->tendanhmuc ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($c->so_tra_loi > 0)
                                    <span class="pill pill-done">Đã trả lời</span>
                                @elseif (!$c->giang_vien_da_xem)
                                    <span class="pill pill-new">Mới</span>
                                @else
                                    <span class="pill pill-wait">Chưa trả lời</span>
                                @endif
                            </td>
                            <td class="t-act">
                                <a href="{{ route('giangvien.cauhoi.show', $c->cau_hoi_pk) }}" class="gvx-mini">
                                    {{ $c->so_tra_loi > 0 ? 'Xem' : 'Trả lời' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="t-empty">Chưa có câu hỏi nào được gửi đến bạn.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- ================= CỘT PHẢI ================= --}}
    <aside class="gvx-col">

        <section class="gvx-panel">
            <div class="gvx-panel-head"><h3>Tỷ lệ đã trả lời</h3></div>
            <div class="gvx-donut" style="--p: {{ $rate }}">
                <div><b>{{ $rate }}%</b></div>
            </div>
            <p class="gvx-donut-note">{{ $stats['da'] }} / {{ $stats['tong'] }} câu hỏi đã được trả lời</p>
        </section>

        <section class="gvx-panel">
            <div class="gvx-panel-head">
                <h3>Truy cập gần đây</h3>
                <a href="{{ route('giangvien.cauhoi.index') }}">Xem tất cả</a>
            </div>
            @forelse ($students as $s)
                <a href="{{ route('giangvien.cauhoi.show', $s->cau_hoi_pk) }}" class="gvx-student">
                    <span class="gvx-avatar">{{ mb_strtoupper(mb_substr($s->nguoi_gui ?? '?', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $s->nguoi_gui ?? 'Ẩn danh' }}</strong>
                        <small>{{ \Illuminate\Support\Str::limit($s->tieu_de, 30) }}</small>
                    </div>
                    @if (!$s->giang_vien_da_xem)<i class="gvx-dot"></i>@endif
                </a>
            @empty
                <p class="gvx-muted">Chưa có sinh viên nào gửi câu hỏi.</p>
            @endforelse
        </section>

        {{-- Tiện ích nhanh (giãn đầy cột) --}}
        <section class="gvx-panel gvx-grow">
            <div class="gvx-panel-head"><h3>Tiện ích nhanh</h3></div>
            <div class="gvx-quick">
                <a href="{{ route('cauhoi.create') }}" class="gvx-quick-main"><i class="fa-solid fa-circle-question"></i> Đặt câu hỏi</a>
                <a href="{{ route('admin.faq.create') }}"><i class="fa-solid fa-plus"></i> Thêm FAQ</a>
               
                <form action="{{ route('giangvien.cauhoi.readall') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fa-solid fa-check-double"></i> Đánh dấu tất cả đã xem</button>
                </form>
            </div>
        </section>
    </aside>
</div>

{{-- Popup thông báo (1 lần mỗi lần đăng nhập) --}}
@if ($showNotice)
    <div id="gvNotice" class="gv-notice">
        <div class="gv-notice-icon"><i class="fa-solid fa-bell"></i></div>
        <div class="gv-notice-body">
            <strong>Bạn có {{ $stats['moi'] }} câu hỏi mới</strong>
            <p>Sinh viên đã gửi câu hỏi trực tiếp đến bạn.</p>
            <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'moi']) }}" class="gv-notice-btn">Xem ngay</a>
        </div>
        <button type="button" class="gv-notice-close" aria-label="Đóng"
                onclick="document.getElementById('gvNotice').remove()">&times;</button>
    </div>
    <script>setTimeout(function(){var e=document.getElementById('gvNotice');if(e)e.remove();},15000);</script>
@endif
@endsection