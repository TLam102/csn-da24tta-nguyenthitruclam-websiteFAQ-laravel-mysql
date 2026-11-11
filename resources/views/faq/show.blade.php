@extends('layouts.app')

@section('title', $faq->cau_hoi)

@section('content')
<style>
    .rp-box { margin-top: 28px; }
    .rp-box h3 { display: flex; align-items: center; gap: 10px; margin: 0 0 16px; color: #1b5e20; }
    .rp-badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 13px; font-weight: 600; background: #e3f4d6; color: #1b5e20; }
    .rp-badge.none { background: #fff4d6; color: #8a6a00; }

    /* Dòng thời gian */
    .tl { position: relative; padding-left: 62px; }
    .tl:before { content: ""; position: absolute; left: 22px; top: 22px; bottom: 22px; width: 3px; background: #e3ebcf; border-radius: 2px; }
    .tl-row { position: relative; margin-bottom: 20px; }
    .tl-ico { position: absolute; left: -62px; top: 0; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #e3ebcf; background: #fff; }
    .tl-ico.q { background: #fdeeb4; color: #a06d00; }
    .tl-ico.r { background: #c5dc2a; color: #1b3a00; }
    .tl-card { background: #fff; border: 1px solid #e3ebcf; border-radius: 14px; padding: 14px 18px; }
    .tl-card.r { background: #f6faee; border-color: #dbe8c1; }
    .tl-head { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .tl-who { font-weight: 700; color: #16301a; }
    .tl-time { font-size: 13px; color: #6b7a5c; }
    .tl-body { margin-top: 6px; line-height: 1.6; }
    .tl-body ul, .tl-body ol { padding-left: 22px; }
    .rp-sender { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #4a5a43; }
    .rp-sender strong { color: #16301a; }
    .rp-sender-av { position: relative; width: 26px; height: 26px; border-radius: 50%; overflow: hidden; flex-shrink: 0; background: linear-gradient(135deg, #b6dc2a, #f2d216); color: #1b3a00; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
    .rp-sender-av img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .tl-att { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .tl-att a { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; padding: 6px 12px; border: 1px solid #cfe3b5; border-radius: 8px; background: #fff; color: #1b5e20; font-size: 13px; text-decoration: none; }
    .tl-att a span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 280px; }
    .tl-att a small { color: #6b7a5c; }
    .tl-att a:hover { background: #f1f8e6; }
    .tl-att img { display: block; max-height: 140px; max-width: 240px; border-radius: 8px; border: 1px solid #cfe3b5; }
    .rp-add { display: inline-block; margin-top: 4px; padding: 9px 20px; border-radius: 999px; background: linear-gradient(90deg,#9ccc2b,#f2d216); color: #1b3a00; font-weight: 700; text-decoration: none; }
</style>

<div class="container list-page">

    <div class="breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('faqs.index') }}">Danh sách FAQ</a> <i class="fa-solid fa-chevron-right"></i>
        <span>Chi tiết FAQ</span>
    </div>

    <article class="detail-card">
        <h1>{{ $faq->cau_hoi }}</h1>

        <div class="detail-meta">
            @if (!empty($faq->tendanhmuc))
                <a href="{{ route('faqs.index', ['danh_muc' => $faq->slug]) }}" class="tag">{{ $faq->tendanhmuc }}</a>
            @endif
            @if (!empty($faq->ngay_cap_nhat))
                <span class="faq-date">{{ \Carbon\Carbon::parse($faq->ngay_cap_nhat)->format('d/m/Y') }}</span>
            @endif
            @php
                // Ưu tiên tài khoản đã gửi câu hỏi; FAQ do quản trị tự soạn thì hiện người đăng
                $tenNguoi = $faq->nguoi_gui ?? $faq->nguoi_dang ?? null;
                $nhanNguoi = !empty($faq->nguoi_gui) ? 'Gửi bởi' : 'Đăng bởi';
            @endphp
            @if (!empty($tenNguoi))
                <span class="rp-sender">
                    <span class="rp-sender-av">
                        <span>{{ mb_strtoupper(mb_substr(trim(preg_replace('/^(GS\.|PGS\.|TS\.|ThS\.|CN\.|KS\.)\s*/u', '', $tenNguoi)), 0, 1)) }}</span>
                        @if (!empty($faq->nguoi_gui) && !empty($faq->nguoi_gui_anh))
                            <img src="{{ file_url($faq->nguoi_gui_anh) }}" alt="" onerror="this.remove()">
                        @endif
                    </span>
                    {{ $nhanNguoi }} <strong>{{ $tenNguoi }}</strong>
                </span>
            @endif
            @if ($traLois->isNotEmpty())
                <span class="rp-badge"><i class="fa-solid fa-circle-check"></i> Đã trả lời</span>
            @else
                <span class="rp-badge none">Chưa trả lời</span>
            @endif
        </div>

        @if (trim((string) $faq->cau_tra_loi) !== '')
            <div class="answer-box">{!! nl2br(e($faq->cau_tra_loi)) !!}</div>
        @endif

        {{-- Dòng thời gian trao đổi --}}
        <section class="rp-box">
            <h3>Trao đổi</h3>

            @if ($traLois->isNotEmpty())
            <div class="tl">
                @foreach ($traLois as $t)
                    <div class="tl-row">
                        <span class="tl-ico r"><i class="fa-solid fa-reply"></i></span>
                        <div class="tl-card r">
                            <div class="tl-head">
                                <span class="tl-who">{{ $t->nguoi ?? 'Người dùng' }}</span>
                                <span class="tl-time">{{ $t->ngay_tra_loi ? \Carbon\Carbon::parse($t->ngay_tra_loi)->format('d/m/Y H:i') : '' }}</span>
                            </div>
                            <div class="tl-body">{!! $t->noi_dung !!}</div>
                            @if (!empty($t->tep))
                                <div class="tl-att">
                                    @foreach ($t->tep as $tep)
                                        @if ($tep['la_anh'])
                                            <a href="{{ $tep['url'] }}" target="_blank" rel="noopener"><img src="{{ $tep['url'] }}" alt="{{ $tep['ten'] }}"></a>
                                        @else
                                            @php
                                                $duoi = strtolower(pathinfo($tep['ten'], PATHINFO_EXTENSION));
                                                $bieuTuong = match (true) {
                                                    $duoi === 'pdf'                     => ['fa-file-pdf', '#d32f2f'],
                                                    in_array($duoi, ['doc', 'docx'])    => ['fa-file-word', '#1565c0'],
                                                    in_array($duoi, ['xls', 'xlsx'])    => ['fa-file-excel', '#2e7d32'],
                                                    in_array($duoi, ['ppt', 'pptx'])    => ['fa-file-powerpoint', '#e65100'],
                                                    default                             => ['fa-file-lines', '#6b7a5c'],
                                                };
                                            @endphp
                                            <a href="{{ $tep['url'] }}" target="_blank" rel="noopener" download>
                                                <i class="fa-solid {{ $bieuTuong[0] }}" style="color: {{ $bieuTuong[1] }}"></i>
                                                <span>{{ $tep['ten'] }}</span>
                                                <small>{{ $tep['kich_thuoc'] >= 1048576 ? number_format($tep['kich_thuoc'] / 1048576, 1) . ' MB' : max(1, round($tep['kich_thuoc'] / 1024)) . ' KB' }}</small>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            @if ($canReply)
                <a class="rp-add" href="{{ route('faqs.index', ['tra_loi' => $faq->faq_id]) }}">
                    <i class="fa-solid fa-reply"></i> Trả lời
                </a>
            @endif
        </section>

        <div class="vote-box">
            <p>Câu hỏi này có hữu ích không?</p>

            <form action="{{ route('faqs.vote', $faq->faq_id) }}" method="POST" class="vote-buttons">
                @csrf
                <button type="submit" name="value" value="up"
                        class="vote-btn {{ $voted === 'up' ? 'active' : '' }}" {{ $voted ? 'disabled' : '' }}>
                    <i class="fa-regular fa-thumbs-up"></i> Hữu ích
                </button>
                <button type="submit" name="value" value="down"
                        class="vote-btn {{ $voted === 'down' ? 'active' : '' }}" {{ $voted ? 'disabled' : '' }}>
                    <i class="fa-regular fa-thumbs-down"></i> Không hữu ích
                </button>
            </form>

            @if (session('vote_msg'))
                <p class="vote-msg">{{ session('vote_msg') }}</p>
            @endif
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="related">
            <h3>Các câu hỏi liên quan</h3>
            <ul>
                @foreach ($related as $r)
                    <li><a href="{{ route('faqs.show', $r->faq_id) }}">{{ $r->cau_hoi }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection