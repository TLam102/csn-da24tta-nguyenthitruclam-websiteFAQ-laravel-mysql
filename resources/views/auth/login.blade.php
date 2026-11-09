@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h2>Đăng nhập</h2>

        @if (session('url.intended') && !$errors->any())
            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info"></i> Vui lòng đăng nhập để xem và tìm kiếm câu hỏi.
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="input-icon">
                <i class="fa-solid fa-user"></i>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="Email của bạn" required autofocus autocomplete="username"
                       class="{{ $errors->has('email') ? 'invalid' : '' }}">
            </div>

            <div class="input-icon">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password"
                       placeholder="Mật khẩu" required autocomplete="current-password"
                       class="{{ $errors->has('password') ? 'invalid' : '' }}">
                <button type="button" class="toggle-pass" id="togglePass" aria-label="Hiện/ẩn mật khẩu">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn-login-submit">Đăng nhập</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('togglePass').addEventListener('click', function () {
        var input = document.getElementById('password');
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
    });
</script>
@endsection