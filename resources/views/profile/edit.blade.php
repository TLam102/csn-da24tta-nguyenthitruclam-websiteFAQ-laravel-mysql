@php $isGv = auth()->user()->vai_tro === 'giang_vien'; @endphp
@extends($isGv ? 'layouts.giangvien' : 'layouts.app')

@section('title', 'Hồ sơ cá nhân')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ filemtime(public_path('css/profile.css')) }}">
@endpush

@section('gv_breadcrumb') <span>Hồ sơ cá nhân</span> @endsection

@section($isGv ? 'gv_content' : 'content')
<div class="pf-wrap">

    @unless ($isGv)
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i> <span>Hồ sơ cá nhân</span>
        </div>
    @endunless

    @if (session('pf_success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('pf_success') }}</div>
    @endif

    <div class="pf-layout">
        @include('profile._side')

        <section class="pf-card pf-main">
            <h2 class="pf-title"><i class="fa-regular fa-id-card"></i> Thông tin cá nhân</h2>
            <p class="pf-note">Cập nhật họ tên và email dùng để đăng nhập hệ thống.</p>

            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div class="pf-row">
                    <div class="pf-group">
                        <label for="name">Họ và tên <span class="req">*</span></label>
                        <input id="name" name="name" type="text" class="pf-input {{ $errors->has('name') ? 'invalid' : '' }}"
                               value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name">
                        @error('name') <p class="pf-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pf-group">
                        <label for="email">Email <span class="req">*</span></label>
                        <input id="email" name="email" type="email" class="pf-input {{ $errors->has('email') ? 'invalid' : '' }}"
                               value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email">
                        @error('email') <p class="pf-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label>Vai trò</label>
                        <input type="text" class="pf-input" value="{{ $vaiTro }}" disabled>
                    </div>
                    <div class="pf-group">
                        <label>Trạng thái tài khoản</label>
                        <input type="text" class="pf-input" value="{{ $user->trang_thai ? 'Đang hoạt động' : 'Bị khóa' }}" disabled>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label>Ngày tham gia</label>
                        <input type="text" class="pf-input" value="{{ optional($user->created_at)->format('d/m/Y H:i') ?? '—' }}" disabled>
                    </div>
                    <div class="pf-group">
                        <label>Cập nhật gần nhất</label>
                        <input type="text" class="pf-input" value="{{ optional($user->updated_at)->format('d/m/Y H:i') ?? '—' }}" disabled>
                    </div>
                </div>

                <div class="pf-actions">
                    <a href="{{ route('profile.password.edit') }}" class="pf-btn-outline"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a>
                    <button type="submit" class="pf-btn">Lưu thay đổi</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@if ($isGv)
    @push('gv_footer')
        @include('partials.footer')
    @endpush
@endif