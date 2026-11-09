@extends('layouts.giangvien')

@section('title', 'Câu hỏi gửi đến tôi')
@section('gv_breadcrumb') <span>Câu hỏi gửi đến tôi</span> @endsection

@section('gv_content')
<div class="gv-head">
    <div>
        <h2 class="gv-title">Câu hỏi gửi đến tôi</h2>
        <p class="gv-sub">Xem và trả lời các câu hỏi sinh viên gửi riêng cho bạn.</p>
    </div>
    <form action="{{ route('giangvien.cauhoi.readall') }}" method="POST">
        @csrf
        <button type="submit" class="gv-btn-outline">
            <i class="fa-solid fa-check-double"></i> Đánh dấu tất cả đã xem
        </button>
    </form>
</div>

<div class="gv-tabs">
    <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'tat-ca']) }}" class="{{ $tab === 'tat-ca' ? 'active' : '' }}">
        <i class="fa-solid fa-layer-group"></i> Tất cả <span>{{ $stats['tong'] }}</span>
    </a>
    <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'moi']) }}" class="{{ $tab === 'moi' ? 'active' : '' }}">
        <i class="fa-solid fa-star"></i> Mới <span>{{ $stats['moi'] }}</span>
    </a>
    <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'chua-tra-loi']) }}" class="{{ $tab === 'chua-tra-loi' ? 'active' : '' }}">
        <i class="fa-regular fa-clock"></i> Chưa trả lời <span>{{ $stats['chua'] }}</span>
    </a>
    <a href="{{ route('giangvien.cauhoi.index', ['tab' => 'da-tra-loi']) }}" class="{{ $tab === 'da-tra-loi' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-check"></i> Đã trả lời <span>{{ $stats['da'] }}</span>
    </a>
</div>

<form class="gv-search" action="{{ route('giangvien.cauhoi.index') }}" method="GET">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <input type="text" name="q" value="{{ $q }}" placeholder="Tìm theo tiêu đề hoặc nội dung...">
    <button type="submit" aria-label="Tìm kiếm"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>

@forelse ($cauHois as $c)
    @include('giangvien.cauhoi.item', ['c' => $c])
@empty
    <div class="gv-empty">
        <i class="fa-regular fa-envelope-open"></i>
        <p>Không có câu hỏi nào phù hợp.</p>
    </div>
@endforelse

@if ($cauHois->hasPages())
    <div class="gv-pager">
        @if ($cauHois->onFirstPage())
            <span class="disabled">&laquo; Trước</span>
        @else
            <a href="{{ $cauHois->previousPageUrl() }}">&laquo; Trước</a>
        @endif

        <span class="current">Trang {{ $cauHois->currentPage() }} / {{ $cauHois->lastPage() }}</span>

        @if ($cauHois->hasMorePages())
            <a href="{{ $cauHois->nextPageUrl() }}">Sau &raquo;</a>
        @else
            <span class="disabled">Sau &raquo;</span>
        @endif
    </div>
@endif
@endsection