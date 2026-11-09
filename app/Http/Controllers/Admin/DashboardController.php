<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dashboard quản trị (QT1): 4 số liệu tổng quan + hoạt động gần đây.
 *
 * Hoạt động gần đây được gom từ dữ liệu có sẵn (bảng faq và cau_hoi),
 * nên không cần tạo thêm bảng nhật ký.
 * Tên cột được tự nhận như CauHoiController; nếu khác, thêm tên đúng vào danh sách UNG_VIEN.
 */
class DashboardController extends Controller
{
    private const SO_DONG_HOAT_DONG = 8;

    // Giá trị cột trạng thái của câu hỏi được coi là "đang chờ duyệt"
    private const TT_CHO_DUYET = ['cho_duyet', 'chờ duyệt', 'Chờ duyệt', 'cho duyet', 'chua_duyet', 'chua_xu_ly', 'moi', 'pending'];

    private const UNG_VIEN = [
        // bảng cau_hoi
        'cau_hoi' => [
            'id'         => ['cau_hoi_id', 'id', 'ch_id', 'ma_cau_hoi'],
            'tieu_de'    => ['tieu_de', 'tieude', 'tieu_de_cau_hoi', 'cau_hoi', 'ten_cau_hoi', 'noi_dung_cau_hoi'],
            'nguoi_gui'  => ['ho_ten', 'ten_nguoi_gui', 'nguoi_gui', 'ten_nguoi_hoi', 'nguoi_hoi', 'ten', 'email_nguoi_gui', 'email'],
            'trang_thai' => ['trang_thai', 'tinh_trang', 'status'],
            'ngay_gui'   => ['ngay_gui', 'created_at', 'ngay_tao', 'thoi_gian_gui', 'ngay_hoi'],
        ],
        // bảng faq (cột ngày tạo, nếu có, để phân biệt "Thêm" và "Sửa")
        'faq' => [
            'ngay_tao' => ['ngay_tao', 'created_at', 'ngay_gui'],
        ],
        // bảng users (cột tên hiển thị)
        'users' => [
            'ten' => ['name', 'ho_ten', 'ten', 'full_name', 'email'],
        ],
    ];

    public function index()
    {
        $c = $this->cotCauHoi();

        // ===== 4 số liệu tổng quan =====
        $soLieu = [
            'faq'      => DB::table('faq')->count(),
            'cho_duyet' => ($c['trang_thai'] ?? null)
                ? DB::table('cau_hoi')->whereIn($c['trang_thai'], self::TT_CHO_DUYET)->count()
                : 0,
            'danh_muc' => DB::table('danh_muc')->count(),
        ];

        return view('admin.dashboard', [
            'soLieu'     => $soLieu,
            'hoatDongs'  => $this->hoatDongGanDay($c),
            'dsTrangThai' => Faq::TRANG_THAI,
        ]);
    }

    /** Gom FAQ vừa thêm/sửa và câu hỏi mới gửi, sắp theo thời gian mới nhất. */
    private function hoatDongGanDay(array $c)
    {
        $ds = collect();

        // ---- FAQ ----
        $cotTen    = $this->timCot('users', self::UNG_VIEN['users']['ten']);
        $cotNgayTao = $this->timCot('faq', self::UNG_VIEN['faq']['ngay_tao']);

        $q = DB::table('faq')
            ->leftJoin('users', 'users.id', '=', 'faq.nguoi_tao_id')
            ->select('faq.faq_id', 'faq.cau_hoi', 'faq.trang_thai', 'faq.ngay_cap_nhat')
            ->addSelect(DB::raw($cotTen ? "`users`.`$cotTen` as nguoi" : 'NULL as nguoi'))
            ->addSelect(DB::raw($cotNgayTao ? "`faq`.`$cotNgayTao` as ngay_tao" : 'NULL as ngay_tao'))
            ->orderByDesc('faq.ngay_cap_nhat')
            ->limit(self::SO_DONG_HOAT_DONG)
            ->get();

        foreach ($q as $f) {
            $thoiGian = $f->ngay_cap_nhat ? Carbon::parse($f->ngay_cap_nhat) : null;
            $laMoi = $f->ngay_tao && $thoiGian && Carbon::parse($f->ngay_tao)->diffInSeconds($thoiGian, true) < 5;

            $ds->push([
                'thoi_gian' => $thoiGian,
                'nguoi'     => $f->nguoi ?: 'Quản trị viên',
                'hoat_dong' => ($laMoi ? 'Thêm FAQ: ' : (($f->ngay_tao ?? null) ? 'Sửa FAQ: ' : 'Cập nhật FAQ: ')) . $f->cau_hoi,
                'loai'      => 'faq',
                'trang_thai' => $f->trang_thai,
                'lien_ket'  => route('admin.faq.edit', $f->faq_id),
            ]);
        }

        // ---- Câu hỏi mới gửi ----
        if ($c['id'] && $c['tieu_de'] && $c['trang_thai']) {
            $chon = fn (string $k) => $c[$k] ? DB::raw("`cau_hoi`.`{$c[$k]}` as `$k`") : DB::raw("NULL as `$k`");

            // Join users qua nguoi_gui_id để lấy tên tài khoản người gửi
            $cauHois = DB::table('cau_hoi')
                ->leftJoin('users', 'users.id', '=', 'cau_hoi.nguoi_gui_id')
                ->select([$chon('id'), $chon('tieu_de'), $chon('nguoi_gui'), $chon('trang_thai'), $chon('ngay_gui')])
                ->addSelect(DB::raw($cotTen ? "`users`.`$cotTen` as ten_tai_khoan" : 'NULL as ten_tai_khoan'))
                ->orderByDesc('cau_hoi.' . ($c['ngay_gui'] ?: $c['id']))
                ->limit(self::SO_DONG_HOAT_DONG)
                ->get();

            foreach ($cauHois as $ch) {
                $choDuyet = in_array($ch->trang_thai, self::TT_CHO_DUYET, true);

                $ds->push([
                    'thoi_gian' => $ch->ngay_gui ? Carbon::parse($ch->ngay_gui) : null,
                    'nguoi'     => $ch->ten_tai_khoan ?: ($ch->nguoi_gui ?: 'Ẩn danh'),
                    'hoat_dong' => 'Gửi câu hỏi: ' . $ch->tieu_de,
                    'loai'      => 'cau_hoi',
                    'trang_thai' => $ch->trang_thai,
                    'lien_ket'  => $choDuyet ? route('admin.cau-hoi.tra-loi', $ch->id) : route('admin.cau-hoi.index'),
                ]);
            }
        }

        return $ds
            ->sortByDesc(fn ($x) => $x['thoi_gian'] ? $x['thoi_gian']->timestamp : 0)
            ->take(self::SO_DONG_HOAT_DONG)
            ->values();
    }

    private function cotCauHoi(): array
    {
        $kq = [];
        foreach (self::UNG_VIEN['cau_hoi'] as $key => $dsTen) {
            $kq[$key] = $this->timCot('cau_hoi', $dsTen);
        }

        return $kq;
    }

    /** Tên cột đầu tiên trong $ungVien có thật trong $bang, không có thì trả null. */
    private function timCot(string $bang, array $ungVien): ?string
    {
        $bangTra = [];
        foreach (Schema::getColumnListing($bang) as $cot) {
            $bangTra[mb_strtolower($cot)] = $cot;
        }

        foreach ($ungVien as $ten) {
            if (isset($bangTra[mb_strtolower($ten)])) {
                return $bangTra[mb_strtolower($ten)];
            }
        }

        return null;
    }
}