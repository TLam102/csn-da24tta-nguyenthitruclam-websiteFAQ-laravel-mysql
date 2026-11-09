@php $isGv = auth()->user()->vai_tro === 'giang_vien'; @endphp
@extends($isGv ? 'layouts.giangvien' : 'layouts.app')

@section('title', 'Đổi mật khẩu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ filemtime(public_path('css/profile.css')) }}">
@endpush

@section('gv_breadcrumb') <span>Đổi mật khẩu</span> @endsection

@section($isGv ? 'gv_content' : 'content')
<div class="pf-wrap">

    @unless ($isGv)
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i> <span>Đổi mật khẩu</span>
        </div>
    @endunless

    @if (session('pf_success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('pf_success') }}</div>
    @endif

    <div class="pf-layout">
        @include('profile._side')

        <div class="pf-stack">
            <section class="pf-card pf-main">
                <h2 class="pf-title"><i class="fa-solid fa-key"></i> Đổi mật khẩu</h2>
                <p class="pf-note">Nhập mật khẩu hiện tại để xác nhận, sau đó đặt mật khẩu mới.</p>

                <form method="POST" action="{{ route('profile.password') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="pf-group">
                        <label for="current_password">Mật khẩu hiện tại <span class="req">*</span></label>
                        <div class="pf-pass">
                            <input id="current_password" name="current_password" type="password"
                                   class="pf-input {{ $errors->updatePassword->has('current_password') ? 'invalid' : '' }}"
                                   autocomplete="current-password">
                            <button type="button" class="pf-eye" data-target="current_password" aria-label="Hiện mật khẩu"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        @if ($errors->updatePassword->has('current_password'))
                            <p class="pf-error">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="pf-row">
                        <div class="pf-group">
                            <label for="password_new">Mật khẩu mới <span class="req">*</span></label>
                            <div class="pf-pass">
                                <input id="password_new" name="password" type="password"
                                       class="pf-input {{ $errors->updatePassword->has('password') ? 'invalid' : '' }}"
                                       autocomplete="new-password">
                                <button type="button" class="pf-eye" data-target="password_new" aria-label="Hiện mật khẩu"><i class="fa-regular fa-eye"></i></button>
                            </div>
                            @if ($errors->updatePassword->has('password'))
                                <p class="pf-error">{{ $errors->updatePassword->first('password') }}</p>
                            @endif
                        </div>

                        <div class="pf-group">
                            <label for="password_confirmation">Nhập lại mật khẩu mới <span class="req">*</span></label>
                            <div class="pf-pass">
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       class="pf-input" autocomplete="new-password">
                                <button type="button" class="pf-eye" data-target="password_confirmation" aria-label="Hiện mật khẩu"><i class="fa-regular fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="pf-actions">
                        <a href="{{ route('profile.edit') }}" class="pf-btn-outline"><i class="fa-solid fa-arrow-left"></i> Quay lại hồ sơ</a>
                        <button type="submit" class="pf-btn">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </section>

            <section class="pf-card pf-tips">
                <h2 class="pf-title"><i class="fa-solid fa-shield-halved"></i> Lưu ý bảo mật</h2>
                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> Mật khẩu có ít nhất 6 ký tự, nên kết hợp chữ và số.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Mật khẩu mới phải khác mật khẩu hiện tại.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Không chia sẻ mật khẩu cho người khác.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Đăng xuất khi dùng máy tính chung.</li>
                </ul>
            </section>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.pf-eye').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.target);
            var show = i.type === 'password';
            i.type = show ? 'text' : 'password';
            b.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    });
</script>
@endsection