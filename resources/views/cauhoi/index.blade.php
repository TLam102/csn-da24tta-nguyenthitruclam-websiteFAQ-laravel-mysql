@extends('layouts.app')

@section('title', 'Câu hỏi của tôi')

@section('content')
@php
    $nhan = ['cho_duyet' => 'Chờ xử lý', 'da_tra_loi' => 'Đã trả lời', 'bi_tu_choi' => 'Bị từ chối'];
@endphp
<div class="container list-page">

    <div class="breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i> <span>Câu hỏi của tôi</span>
    </div>

    <div class="sq-head">
        <h2>Câu hỏi của tôi</h2>
        <a href="{{ route('cauhoi.create') }}" class="btn-submit sq-new"><i class="fa-solid fa-plus"></i> Gửi câu hỏi mới</a>
    </div>

    @forelse ($cauHois as $c)
        <article class="sq-card" id="ch-{{ $c->cau_hoi_id }}">
            <div class="sq-top">
                <h3>{{ $c->tieu_de }}</h3>
                <span class="st st-{{ $c->trang_thai }}">{{ $nhan[$c->trang_thai] ?? $c->trang_thai }}</span>
            </div>

            <div class="sq-body">{!! nl2br(e($c->noi_dung)) !!}</div>

            <div class="sq-meta">
                <span><i class="fa-regular fa-clock"></i> Gửi lúc {{ optional($c->ngay_gui)->format('d/m/Y H:i') }}</span>
                @if ($c->giangVien)
                    <span><i class="fa-solid fa-user-tie"></i> Gửi đến: {{ $c->giangVien->name }}</span>
                @endif
            </div>

            @if ($c->trang_thai === 'bi_tu_choi')
                <div class="sq-reject"><i class="fa-solid fa-circle-exclamation"></i> Lý do từ chối: {{ $c->ly_do_tu_choi }}</div>
            @endif

            @foreach ($c->cauTraLois as $tl)
                <div class="sq-answer">
                    <div class="sq-answer-head">
                        <strong><i class="fa-solid fa-reply"></i> {{ $tl->nguoiTraLoi->name ?? 'Giảng viên' }}</strong>
                        <span>{{ optional($tl->ngay_tra_loi)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="sq-answer-body">
                        @if ($tl->noi_dung !== strip_tags($tl->noi_dung))
                            {!! \App\Http\Controllers\GiangVienCauHoiController::cleanHtml($tl->noi_dung) !!}
                        @else
                            {!! nl2br(e($tl->noi_dung)) !!}
                        @endif
                    </div>
                    @if (isset($tep[$tl->tra_loi_id]))
                        @include('giangvien.cauhoi._tep', ['files' => $tep[$tl->tra_loi_id]])
                    @endif
                </div>
            @endforeach
        </article>
    @empty
        <div class="sq-empty">
            <i class="fa-regular fa-envelope-open"></i>
            <p>Bạn chưa gửi câu hỏi nào.</p>
        </div>
    @endforelse

    @if ($cauHois->hasPages())
        <div class="gv-pager">
            @if ($cauHois->onFirstPage()) <span class="disabled">&laquo; Trước</span>
            @else <a href="{{ $cauHois->previousPageUrl() }}">&laquo; Trước</a> @endif
            <span class="current">Trang {{ $cauHois->currentPage() }} / {{ $cauHois->lastPage() }}</span>
            @if ($cauHois->hasMorePages()) <a href="{{ $cauHois->nextPageUrl() }}">Sau &raquo;</a>
            @else <span class="disabled">Sau &raquo;</span> @endif
        </div>
    @endif
</div>
@endsection