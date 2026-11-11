<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const VAI_TRO = [
        'quan_tri'   => 'Quản trị viên',
        'giang_vien' => 'Giảng viên',
        'sinh_vien'  => 'Sinh viên',
    ];

    private function sideData(Request $request): array
    {
        $user = $request->user();
        $soCauHoi = 0;
        $soDaTraLoi = 0;

        try {
            if ($user->vai_tro === 'giang_vien') {
                $soCauHoi = DB::table('cau_hoi')->where('giang_vien_id', $user->id)->count();
                $soDaTraLoi = DB::table('cau_tra_loi')->where('nguoi_tra_loi_id', $user->id)->count();
            } else {
                $soCauHoi = DB::table('cau_hoi')->where('nguoi_gui_id', $user->id)->count();
                $soDaTraLoi = DB::table('cau_hoi as c')
                    ->where('c.nguoi_gui_id', $user->id)
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))->from('cau_tra_loi as t')
                          ->whereColumn('t.cau_hoi_id', 'c.cau_hoi_id');
                    })->count();
            }
        } catch (\Throwable $e) {
            // bỏ qua nếu bảng chưa sẵn sàng
        }

        return [
            'user'       => $user,
            'vaiTro'     => self::VAI_TRO[$user->vai_tro] ?? 'Người dùng',
            'isGv'       => $user->vai_tro === 'giang_vien',
            'soCauHoi'   => $soCauHoi,
            'soDaTraLoi' => $soDaTraLoi,
        ];
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', $this->sideData($request));
    }

    public function passwordForm(Request $request): View
    {
        return view('profile.password', $this->sideData($request));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required'  => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.unique'   => 'Email này đã được sử dụng.',
        ]);

        $user->fill($data)->save();

        return Redirect::route('profile.edit')->with('pf_success', 'Đã cập nhật hồ sơ cá nhân.');
    }

    /** Tải ảnh đại diện lên */
    public function avatar(Request $request): RedirectResponse
    {
        $request->validateWithBag('avatar', [
            'anh_dai_dien' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'anh_dai_dien.required' => 'Vui lòng chọn một ảnh.',
            'anh_dai_dien.image'    => 'File tải lên phải là ảnh.',
            'anh_dai_dien.mimes'    => 'Ảnh chỉ nhận định dạng JPG, PNG hoặc WEBP.',
            'anh_dai_dien.max'      => 'Ảnh không được vượt quá 2MB.',
            'anh_dai_dien.uploaded' => 'Không tải được ảnh (file quá lớn so với cấu hình máy chủ).',
        ]);

        $user = $request->user();
        $path = $request->file('anh_dai_dien')->store('avatars', 'public');

        if ($user->anh_dai_dien) {
            Storage::disk('public')->delete($user->anh_dai_dien);
        }

        $user->forceFill(['anh_dai_dien' => $path])->save();

        return Redirect::route('profile.edit')->with('pf_success', 'Đã cập nhật ảnh đại diện.');
    }

    /** Xóa ảnh đại diện */
    public function avatarRemove(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->anh_dai_dien) {
            Storage::disk('public')->delete($user->anh_dai_dien);
            $user->forceFill(['anh_dai_dien' => null])->save();
        }

        return Redirect::route('profile.edit')->with('pf_success', 'Đã xóa ảnh đại diện.');
    }

    public function password(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required'         => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'password.required'                 => 'Vui lòng nhập mật khẩu mới.',
            'password.min'                      => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed'                => 'Xác nhận mật khẩu không khớp.',
            'password.different'                => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);

        return Redirect::route('profile.password.edit')->with('pf_success', 'Đổi mật khẩu thành công.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->anh_dai_dien) {
            Storage::disk('public')->delete($user->anh_dai_dien);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}