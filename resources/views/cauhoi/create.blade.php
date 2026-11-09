@extends('layouts.app')

@section('title', 'Gửi câu hỏi mới')

@section('content')
<div class="container list-page">

    <div class="breadcrumb">
        <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i> <span>Gửi câu hỏi mới</span>
    </div>

    <div class="form-card">
        <h2>Gửi câu hỏi mới</h2>

        @if (session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif

        <form action="{{ route('cauhoi.store') }}" method="POST" novalidate>
            @csrf

            <div class="form-group">
                <label for="danh_muc">Danh mục <span class="req">*</span></label>
                <select id="danh_muc" name="danh_muc" class="{{ $errors->has('danh_muc') ? 'invalid' : '' }}">
                    <option value="">Chọn danh mục</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->slug }}" {{ old('danh_muc') === $c->slug ? 'selected' : '' }}>
                            {{ $c->tendanhmuc }}
                        </option>
                    @endforeach
                </select>
                @error('danh_muc') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="gvpBtn">Gửi đến giảng viên <span class="opt">(không bắt buộc)</span></label>
                {{-- Ô chọn có ảnh đại diện + tên giảng viên --}}
                @include('partials.chon-giang-vien')
                @error('giang_vien_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="tieu_de">Tiêu đề câu hỏi <span class="req">*</span></label>
                <input type="text" id="tieu_de" name="tieu_de" value="{{ old('tieu_de') }}"
                       placeholder="Nhập tiêu đề câu hỏi..." maxlength="255"
                       class="{{ $errors->has('tieu_de') ? 'invalid' : '' }}">
                @error('tieu_de') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="noi_dung">Nội dung chi tiết <span class="req">*</span></label>
                <textarea id="noi_dung" name="noi_dung" rows="6" maxlength="3000"
                          placeholder="Nhập nội dung chi tiết câu hỏi của bạn..."
                          class="{{ $errors->has('noi_dung') ? 'invalid' : '' }}">{{ old('noi_dung') }}</textarea>
                @error('noi_dung') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-footer">
                <p class="form-note"><i class="fa-solid fa-circle-info"></i> Câu hỏi của bạn sẽ được kiểm duyệt trước khi hiển thị.</p>
                <button type="submit" class="btn-submit">Gửi câu hỏi</button>
            </div>
        </form>
    </div>
</div>
@endsection