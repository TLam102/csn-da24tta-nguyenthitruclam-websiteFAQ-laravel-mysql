<style>
    /* ===== Ảnh đại diện: hình tròn nhỏ, ảnh luôn nằm gọn trong khung ===== */
    .pf-side .pf-avatar-box { display: flex; justify-content: center; margin: 4px 0 14px; }
    .pf-side .pf-avatar {
        position: relative !important;      /* khung ảnh phải là "neo" để ảnh bên trong không tràn ra ngoài */
        width: 120px !important; height: 120px !important; min-width: 0; padding: 0; margin: 0;
        border: 0 !important; border-radius: 50% !important; overflow: visible !important;
        background: linear-gradient(135deg, #b6dc2a, #f2d216); color: #1b3a00;
        font-size: 44px !important; font-weight: 700; line-height: 1; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 4px #fff, 0 6px 18px rgba(27, 94, 32, .25);
    }
    .pf-side .pf-avatar img {
        position: absolute !important; inset: 0 !important;
        width: 100% !important; height: 100% !important; max-width: none;
        object-fit: cover; border-radius: 50% !important; display: block;
    }
    .pf-side .pf-avatar img[hidden] { display: none !important; }
    .pf-side .pf-avatar-cam {
        position: absolute !important; left: auto !important; top: auto !important; right: 0 !important; bottom: 2px !important; z-index: 2;
        width: 32px; height: 32px; border-radius: 50%; margin: 0;
        background: #1b6e1b; color: #fff; font-size: 14px !important;
        display: flex; align-items: center; justify-content: center;
        border: 3px solid #fff;
    }

    /* Nút Chọn ảnh / Lưu ảnh / Xóa ảnh */
    .pf-side .pf-avatar-actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
    .pf-side .pf-mini {
        display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
        border: 1.5px solid #1b6e1b; border-radius: 999px; background: #fff; color: #1b6e1b;
        font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .pf-side .pf-mini:hover { background: #f1f8e6; }
    .pf-side .pf-mini[hidden] { display: none; }
    .pf-side .pf-mini-main { border-color: transparent; background: linear-gradient(90deg, #9ccc2b, #f2d216); color: #1b3a00; }
    .pf-side .pf-remove {
        display: block; margin: 2px auto 10px; padding: 4px 10px; border: 0; background: none;
        color: #b3261e; font: inherit; font-size: 13px; cursor: pointer;
    }
    .pf-side .pf-remove:hover { text-decoration: underline; }
    .pf-side .pf-error { color: #b3261e; font-size: 13px; text-align: center; margin: 4px 0 0; }
</style>

<aside class="pf-card pf-side">

    {{-- Ảnh đại diện + tải ảnh --}}
    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatarForm">
        @csrf
        <input type="file" name="anh_dai_dien" id="avatarInput" accept="image/png,image/jpeg,image/webp" hidden>

        <div class="pf-avatar-box">
            <button type="button" class="pf-avatar" id="avatarPick" title="Bấm để chọn ảnh mới">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Ảnh đại diện" id="avatarPreview">
                @else
                    <span id="avatarLetter">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <img src="" alt="" id="avatarPreview" hidden>
                @endif
                <i class="fa-solid fa-camera pf-avatar-cam"></i>
            </button>
        </div>

        <div class="pf-avatar-actions" id="avatarActions">
            <button type="button" class="pf-mini" id="avatarChoose"><i class="fa-solid fa-image"></i> Chọn ảnh</button>
            <button type="submit" class="pf-mini pf-mini-main" id="avatarSave" hidden><i class="fa-solid fa-upload"></i> Lưu ảnh</button>
        </div>

        @if ($errors->avatar->has('anh_dai_dien'))
            <p class="pf-error">{{ $errors->avatar->first('anh_dai_dien') }}</p>
        @endif
    </form>

    @if ($user->avatar_url)
        <form method="POST" action="{{ route('profile.avatar.remove') }}" onsubmit="return confirm('Xóa ảnh đại diện hiện tại?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="pf-remove"><i class="fa-regular fa-trash-can"></i> Xóa ảnh</button>
        </form>
    @endif

    <h3>{{ $user->name }}</h3>
    <p class="pf-mail">{{ $user->email }}</p>
    <span class="pf-role">{{ $vaiTro }}</span>

    <div class="pf-stats">
        <div>
            <b>{{ $soCauHoi }}</b>
            <span>{{ $isGv ? 'Câu hỏi nhận' : 'Câu hỏi đã gửi' }}</span>
        </div>
        <div>
            <b>{{ $soDaTraLoi }}</b>
            <span>{{ $isGv ? 'Câu đã trả lời' : 'Được trả lời' }}</span>
        </div>
    </div>

    <nav class="pf-menu">
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="fa-regular fa-id-card"></i> Hồ sơ cá nhân
        </a>
        <a href="{{ route('profile.password.edit') }}" class="{{ request()->routeIs('profile.password.edit') ? 'active' : '' }}">
            <i class="fa-solid fa-key"></i> Đổi mật khẩu
        </a>
        @if ($isGv)
            <a href="{{ route('giangvien.dashboard') }}"><i class="fa-solid fa-gauge"></i> Trang tổng quan</a>
            <a href="{{ route('giangvien.cauhoi.index') }}"><i class="fa-solid fa-comments"></i> Câu hỏi sinh viên</a>
        @else
            <a href="{{ route('cauhoi.index') }}"><i class="fa-solid fa-list"></i> Câu hỏi của tôi</a>
            <a href="{{ route('cauhoi.create') }}"><i class="fa-solid fa-circle-question"></i> Gửi câu hỏi mới</a>
        @endif
    </nav>
</aside>

<script>
    (function () {
        var input   = document.getElementById('avatarInput');
        var pick    = document.getElementById('avatarPick');
        var choose  = document.getElementById('avatarChoose');
        var save    = document.getElementById('avatarSave');
        var preview = document.getElementById('avatarPreview');
        var letter  = document.getElementById('avatarLetter');

        pick.addEventListener('click', function () { input.click(); });
        choose.addEventListener('click', function () { input.click(); });

        input.addEventListener('change', function () {
            var f = input.files && input.files[0];
            if (!f) return;

            if (f.size > 2 * 1024 * 1024) {
                alert('Ảnh vượt quá 2MB, vui lòng chọn ảnh nhỏ hơn.');
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.hidden = false;
                if (letter) letter.hidden = true;
                save.hidden = false;
            };
            reader.readAsDataURL(f);
        });
    })();
</script>