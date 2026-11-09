@extends('layouts.admin')
@section('title', 'Xem xét câu hỏi')
@section('admin')
<a href="{{ route('admin.cauhoi.index') }}" class="btn btn-link px-0">&larr; Quay lại</a>
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between"><h5>{{ $cauHoi->tieu_de }}</h5>
        <span class="badge bg-{{ \App\Models\Faq::badge($cauHoi->trang_thai) }}">{{ \App\Models\Faq::TRANG_THAI[$cauHoi->trang_thai] ?? $cauHoi->trang_thai }}</span></div>
    <small class="text-muted">Gửi bởi {{ $cauHoi->nguoiGui->name ?? '—' }} ({{ $cauHoi->nguoiGui->email ?? '' }}) lúc {{ $cauHoi->ngay_gui?->format('d/m/Y H:i') }}</small>
    <hr><div style="white-space: pre-line">{{ $cauHoi->noi_dung }}</div>
    @if($cauHoi->trang_thai === 'bi_tu_choi')<div class="alert alert-danger mt-3 mb-0">Lý do từ chối: {{ $cauHoi->ly_do_tu_choi }}</div>@endif
</div></div>

@foreach($cauHoi->cauTraLois as $tl)
    <div class="alert alert-success"><strong>{{ $tl->nguoiTraLoi->name ?? 'Quản trị viên' }}</strong> · {{ $tl->ngay_tra_loi?->format('d/m/Y H:i') }}
        <div style="white-space: pre-line">{{ $tl->noi_dung }}</div></div>
@endforeach

<div class="row">
    <div class="col-md-7"><div class="card"><div class="card-body">
        <h6>Soạn trả lời & duyệt</h6>
        <form method="POST" action="{{ route('admin.cauhoi.traloi', $cauHoi) }}">@csrf
            <textarea name="noi_dung" rows="5" class="form-control mb-2" placeholder="Nội dung trả lời..." required>{{ old('noi_dung') }}</textarea>
            <button class="btn btn-success">Gửi trả lời</button>
        </form>
    </div></div></div>
    <div class="col-md-5"><div class="card"><div class="card-body">
        <h6>Từ chối câu hỏi</h6>
        <form method="POST" action="{{ route('admin.cauhoi.tuchoi', $cauHoi) }}">@csrf
            <textarea name="ly_do_tu_choi" rows="3" class="form-control mb-2" placeholder="Lý do từ chối..." required>{{ old('ly_do_tu_choi') }}</textarea>
            <button class="btn btn-danger">Từ chối</button>
        </form>
    </div></div></div>
</div>
@endsection
