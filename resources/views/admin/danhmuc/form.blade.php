@extends('layouts.admin')
@section('title', $dm->exists ? 'Sửa danh mục' : 'Thêm danh mục')
@section('admin')
<h4 class="mb-3">{{ $dm->exists ? 'Sửa danh mục' : 'Thêm danh mục' }}</h4>
<form method="POST" action="{{ $dm->exists ? route('admin.danhmuc.update', $dm) : route('admin.danhmuc.store') }}">
    @csrf @if($dm->exists) @method('PUT') @endif
    <div class="card"><div class="card-body">
        <div class="mb-3"><label class="form-label">Tên danh mục</label><input name="tendanhmuc" maxlength="100" value="{{ old('tendanhmuc', $dm->tendanhmuc) }}" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mô tả</label><textarea name="mo_ta" rows="3" class="form-control">{{ old('mo_ta', $dm->mo_ta) }}</textarea></div>
        <div class="mb-3"><label class="form-label">Trạng thái</label>
            <select name="trang_thai" class="form-select">
                <option value="1" @selected(old('trang_thai', $dm->trang_thai) == 1)>Hiển thị</option>
                <option value="0" @selected(old('trang_thai', $dm->trang_thai) == 0)>Ẩn</option>
            </select></div>
        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary">Hủy</a>
    </div></div>
</form>
@endsection
