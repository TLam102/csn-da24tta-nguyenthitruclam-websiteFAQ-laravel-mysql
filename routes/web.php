<?php

use App\Http\Controllers\CauHoiController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GiangVienCauHoiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Middleware\QuanTriVien;
use Illuminate\Support\Facades\Route;

// ===== Controller khu vực quản trị (đặt bí danh để không trùng tên) =====
use App\Http\Controllers\Admin\CauHoiController as AdminCauHoiController;
use App\Http\Controllers\Admin\DanhMucController as AdminDanhMucController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DuyetCauHoiController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;

// =====================================================
// KHU VỰC QUẢN TRỊ  (đường dẫn bắt đầu bằng /admin)
// Phải đăng nhập VÀ là quản trị viên mới vào được
// (danh sách vai trò quản trị nằm trong app/Http/Middleware/QuanTriVien.php)
// =====================================================
Route::middleware(['auth', QuanTriVien::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Có đủ: index, create, store, show (xem chi tiết), edit, update, destroy
    Route::resource('faq', AdminFaqController::class);
    Route::resource('danh-muc', AdminDanhMucController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('cau-hoi', [AdminCauHoiController::class, 'index'])->name('cau-hoi.index');
    Route::post('cau-hoi/{id}/duyet', [DuyetCauHoiController::class, 'duyet'])->whereNumber('id')->name('cau-hoi.duyet');
    Route::post('cau-hoi/{id}/tu-choi', [AdminCauHoiController::class, 'tuChoi'])->name('cau-hoi.tu-choi');
    Route::get('cau-hoi/{id}/tra-loi', [AdminCauHoiController::class, 'soan'])->name('cau-hoi.tra-loi');
    Route::post('cau-hoi/{id}/tra-loi', [AdminCauHoiController::class, 'luu'])->name('cau-hoi.tra-loi.luu');
});

// =====================================================
// KHU VỰC GIẢNG VIÊN  (đường dẫn bắt đầu bằng /giang-vien)
// =====================================================
Route::middleware('auth')->prefix('giang-vien')->name('giangvien.')->group(function () {
    Route::get('/', [GiangVienCauHoiController::class, 'dashboard'])->name('dashboard');
    Route::get('/cau-hoi', [GiangVienCauHoiController::class, 'index'])->name('cauhoi.index');
    Route::post('/cau-hoi/doc-tat-ca', [GiangVienCauHoiController::class, 'markAllRead'])->name('cauhoi.readall');
    Route::get('/cau-hoi/{id}', [GiangVienCauHoiController::class, 'show'])->whereNumber('id')->name('cauhoi.show');
    Route::post('/cau-hoi/{id}/tra-loi', [GiangVienCauHoiController::class, 'reply'])->whereNumber('id')->name('cauhoi.reply');
    Route::post('/mau-tra-loi', [GiangVienCauHoiController::class, 'storeTemplate'])->name('mau.store');
    Route::delete('/mau-tra-loi/{id}', [GiangVienCauHoiController::class, 'destroyTemplate'])->whereNumber('id')->name('mau.destroy');
});

// =====================================================
// TRANG CÔNG KHAI
// =====================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/faqs/{id}', [FaqController::class, 'show'])->whereNumber('id')->name('faqs.show');
Route::post('/faqs/{id}/vote', [FaqController::class, 'vote'])->whereNumber('id')->name('faqs.vote');

//===== Bắt buộc đăng nhập: xem / tìm kiếm FAQ =====
Route::middleware('auth')->group(function () {
    Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');

    // Cửa sổ "Trả lời" trên danh sách FAQ: xem lịch sử trao đổi + gửi thêm lượt trả lời
    Route::get('/faqs/{id}/tra-loi', [FaqController::class, 'replies'])->whereNumber('id')->name('faqs.replies');
    Route::post('/faqs/{id}/tra-loi', [FaqController::class, 'reply'])->whereNumber('id')->name('faqs.reply');
});

// ===== Sinh viên gửi câu hỏi =====
Route::get('/gui-cau-hoi', [CauHoiController::class, 'create'])->name('cauhoi.create');
Route::post('/gui-cau-hoi', [CauHoiController::class, 'store'])->name('cauhoi.store');
Route::middleware('auth')->get('/cau-hoi-cua-toi', [CauHoiController::class, 'index'])->name('cauhoi.index');

// ===== Sau đăng nhập: quản trị viên → /admin, giảng viên → trang giảng viên, sinh viên → trang chủ =====
Route::get('/dashboard', function () {
    $vaiTro = auth()->user()->vai_tro ?? '';

    if (in_array($vaiTro, QuanTriVien::VAI_TRO, true)) {
        return redirect()->route('admin.dashboard');
    }
    if ($vaiTro === 'giang_vien') {
        return redirect()->route('giangvien.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'avatarRemove'])->name('profile.avatar.remove');

    Route::get('/profile/password', [ProfileController::class, 'passwordForm'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== Thông báo (mọi vai trò đã đăng nhập) =====
Route::middleware('auth')->group(function () {
    Route::get('/thong-bao', [ThongBaoController::class, 'index'])->name('thongbao.index');
    Route::get('/thong-bao/{id}', [ThongBaoController::class, 'open'])->whereNumber('id')->name('thongbao.open');
    Route::post('/thong-bao/doc-tat-ca', [ThongBaoController::class, 'readAll'])->name('thongbao.readall');
    Route::delete('/thong-bao/{id}', [ThongBaoController::class, 'destroy'])->whereNumber('id')->name('thongbao.destroy');
});

require __DIR__.'/auth.php';