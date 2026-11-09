@extends('layouts.admin')
@section('title', 'Quản lý tệp đính kèm')
@section('admin')
<h4 class="mb-3">Quản lý tệp đính kèm</h4>
<div class="alert alert-info py-2">Để tải tệp lên, hãy vào <a href="{{ route('admin.faq.index') }}">Quản lý FAQ</a> &rarr; Sửa FAQ &rarr; chọn tệp.</div>
<table class="table table-bordered bg-white align-middle">
    <thead class="table-light"><tr><th>Tên tệp</th><th>Thuộc FAQ</th><th>Loại</th><th>Dung lượng</th><th>Người tải</th><th>Ngày</th><th></th></tr></thead>
    <tbody>
    @forelse($teps as $t)
        <tr>
            <td><a href="{{ $t->url() }}" target="_blank">{{ $t->ten_tep }}</a></td>
            <td>{{ $t->faq ? \Illuminate\Support\Str::limit($t->faq->cau_hoi, 40) : 'Tệp mồ côi' }}</td>
            <td>{{ $t->loai_tep }}</td>
            <td>{{ number_format($t->dung_luong / 1024, 1) }} KB</td>
            <td>{{ $t->nguoiTao->name ?? '—' }}</td>
            <td>{{ $t->ngay_tao?->format('d/m/Y') }}</td>
            <td><form method="POST" action="{{ route('admin.tapdinhkem.destroy', $t) }}" onsubmit="return confirm('Xóa tệp này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Xóa</button></form></td>
        </tr>
    @empty <tr><td colspan="7" class="text-center text-muted">Chưa có tệp đính kèm.</td></tr> @endforelse
    </tbody>
</table>
{{ $teps->links('pagination::bootstrap-5') }}
@endsection
