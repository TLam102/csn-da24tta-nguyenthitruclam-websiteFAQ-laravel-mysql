<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Câu hỏi chờ duyệt (QT6) + Soạn trả lời & tạo FAQ (QT7).
 *
 * QT6  index()    danh sách câu hỏi chờ duyệt
 *      tuChoi()   từ chối câu hỏi
 * QT7  soan()     mở trang soạn trả lời cho một câu hỏi
 *      luu()      tạo FAQ từ câu hỏi + câu trả lời, rồi đánh dấu câu hỏi đã xử lý
 *
 * Controller TỰ NHẬN TÊN CỘT: với mỗi thông tin cần dùng, nó thử lần lượt các tên
 * trong danh sách UNG_VIEN bên dưới và lấy tên đầu tiên có thật trong database.
 * Nếu bảng của bạn dùng tên khác, chỉ cần thêm tên đó vào đúng dòng.
 */
class CauHoiController extends Controller
{
    private const BANG = 'cau_hoi';
    private const BANG_DANH_MUC = 'danh_muc';

    // =============== DANH SÁCH TÊN CỘT CÓ THỂ GẶP (thêm tên khác nếu cần) ===============
    private const UNG_VIEN = [
        'id'          => ['cau_hoi_id', 'id', 'ch_id', 'ma_cau_hoi'],
        'tieu_de'     => ['tieu_de', 'tieude', 'tieu_de_cau_hoi', 'cau_hoi', 'ten_cau_hoi', 'noi_dung_cau_hoi'],
        'noi_dung'    => ['noi_dung', 'noidung', 'chi_tiet', 'noi_dung_chi_tiet', 'mo_ta'],
        'nguoi_gui'   => ['ho_ten', 'ten_nguoi_gui', 'nguoi_gui', 'ten_nguoi_hoi', 'nguoi_hoi', 'ten', 'email_nguoi_gui', 'email'],
        'danh_muc_id' => ['danh_muc_id', 'dm_id', 'id_danh_muc', 'ma_danh_muc', 'danhmuc_id'],
        'trang_thai'  => ['trang_thai', 'tinh_trang', 'status'],
        'ngay_gui'    => ['ngay_gui', 'created_at', 'ngay_tao', 'thoi_gian_gui', 'ngay_hoi'],
    ];

    private const UNG_VIEN_DM = [
        'id'  => ['danh_muc_id', 'id', 'dm_id'],
        'ten' => ['ten_danh_muc', 'tendanhmuc', 'ten_dm', 'ten', 'name'],
    ];

    // Các giá trị của cột trạng thái được coi là "đang chờ duyệt"
    private const TT_CHO_DUYET = ['cho_duyet', 'chờ duyệt', 'Chờ duyệt', 'cho duyet', 'chua_duyet', 'chua_xu_ly', 'moi', 'pending'];
    // Giá trị ghi vào khi từ chối / khi đã tạo FAQ từ câu hỏi
    private const TT_TU_CHOI   = 'tu_choi';
    private const TT_DA_XU_LY  = 'da_xu_ly';
    // ========================================================================================

    /* =====================================================================
     *  QT6 – DANH SÁCH CÂU HỎI CHỜ DUYỆT
     * ===================================================================== */

    public function index(Request $r)
    {
        $b = self::BANG;
        $c = $this->cotCauHoi();
        $dm = $this->cotDanhMuc();

        $q = $this->truyVanCauHoi($c, $dm)
            ->whereIn("$b.{$c['trang_thai']}", self::TT_CHO_DUYET);

        if ($r->filled('q')) {
            $tuKhoa = '%' . $r->q . '%';
            $q->where(function ($w) use ($b, $c, $tuKhoa) {
                $w->where("$b.{$c['tieu_de']}", 'like', $tuKhoa);
                if ($c['noi_dung']) {
                    $w->orWhere("$b.{$c['noi_dung']}", 'like', $tuKhoa);
                }
            });
        }

        if ($r->filled('danh_muc') && $c['danh_muc_id']) {
            $q->where("$b.{$c['danh_muc_id']}", $r->danh_muc);
        }

        $q->orderByDesc($c['ngay_gui'] ? "$b.{$c['ngay_gui']}" : "$b.{$c['id']}");

        return view('admin.cauhoi.index', [
            'cauHois'  => $q->paginate(10)->withQueryString(),
            'danhMucs' => $this->dsDanhMuc($dm),
        ]);
    }

    public function tuChoi($id)
    {
        $c = $this->cotCauHoi();

        $soDong = DB::table(self::BANG)
            ->where($c['id'], $id)
            ->whereIn($c['trang_thai'], self::TT_CHO_DUYET)
            ->update([$c['trang_thai'] => self::TT_TU_CHOI]);

        if (! $soDong) {
            return back()->with('loi', 'Câu hỏi không còn ở trạng thái chờ duyệt.');
        }

        return back()->with('ok', 'Đã từ chối câu hỏi.');
    }

    /* =====================================================================
     *  QT7 – SOẠN TRẢ LỜI & TẠO FAQ
     * ===================================================================== */

    public function soan($id)
    {
        $cauHoi = $this->layCauHoiChoDuyet($id);
        if (! $cauHoi) {
            return redirect()->route('admin.cau-hoi.index')
                ->with('loi', 'Câu hỏi không tồn tại hoặc đã được xử lý.');
        }

        return view('admin.cauhoi.tra-loi', [
            'cauHoi'      => $cauHoi,
            'danhMucs'    => $this->dsDanhMuc($this->cotDanhMuc()),
            'theTags'     => DB::table('the_tag')->orderBy('ten_the')->get(),
            'dsTrangThai' => Faq::TRANG_THAI,
            'khoaDang'    => $this->khoaDang(),
        ]);
    }

    public function luu(Request $r, $id)
    {
        $cauHoi = $this->layCauHoiChoDuyet($id);
        if (! $cauHoi) {
            return redirect()->route('admin.cau-hoi.index')
                ->with('loi', 'Câu hỏi không tồn tại hoặc đã được xử lý.');
        }

        $data = $r->validate([
            'cau_hoi'     => 'required|string|max:500',
            'cau_tra_loi' => 'required|string',
            'danh_muc_id' => 'required|exists:danh_muc,danh_muc_id',
            'trang_thai'  => 'required|in:' . implode(',', array_keys(Faq::TRANG_THAI)),
            'hanh_dong'   => 'required|in:nhap,dang',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:the_tag,the_id',
        ], [
            'cau_hoi.required'     => 'Vui lòng nhập tiêu đề.',
            'cau_tra_loi.required' => 'Vui lòng nhập nội dung trả lời.',
            'danh_muc_id.required' => 'Vui lòng chọn danh mục.',
        ]);

        // "Lưu nháp" luôn là bản nháp; "Đăng" dùng trạng thái đã chọn (không để là nháp)
        $trangThai = $data['hanh_dong'] === 'nhap'
            ? 'nhap'
            : ($data['trang_thai'] === 'nhap' ? $this->khoaDang() : $data['trang_thai']);

        $canhBao = null;

        $faq = DB::transaction(function () use ($data, $trangThai, $r, $cauHoi, &$canhBao) {
            $faq = Faq::create([
                'cau_hoi'      => $data['cau_hoi'],
                'cau_tra_loi'  => $data['cau_tra_loi'],
                'danh_muc_id'  => $data['danh_muc_id'],
                'trang_thai'   => $trangThai,
                'nguoi_tao_id' => auth()->id(),
                'slug'         => Str::slug(Str::limit($data['cau_hoi'], 150, '')) . '-' . Str::lower(Str::random(5)),
                'luot_xem'     => 0,
            ]);

            $faq->theTags()->sync($r->input('tags', []));

            // Đánh dấu câu hỏi đã xử lý để không còn trong danh sách chờ duyệt
            try {
                $c = $this->cotCauHoi();
                DB::table(self::BANG)
                    ->where($c['id'], $cauHoi->id)
                    ->update([$c['trang_thai'] => self::TT_DA_XU_LY]);
            } catch (\Throwable $e) {
                $canhBao = 'Đã tạo FAQ nhưng chưa đổi được trạng thái câu hỏi (cột trạng thái không nhận giá trị "'
                    . self::TT_DA_XU_LY . '"). Hãy đổi hằng số TT_DA_XU_LY trong CauHoiController.';
            }

            return $faq;
        });

        $thongBao = $data['hanh_dong'] === 'nhap' ? 'Đã lưu bản nháp FAQ.' : 'Đã tạo và đăng FAQ từ câu hỏi.';
        $ketQua = $data['hanh_dong'] === 'nhap'
            ? redirect()->route('admin.faq.edit', $faq)   // nháp: sang trang sửa FAQ để làm tiếp
            : redirect()->route('admin.faq.index');       // đăng: về danh sách FAQ

        $ketQua->with('ok', $thongBao);
        if ($canhBao) {
            $ketQua->with('loi', $canhBao);
        }

        return $ketQua;
    }

    /* =====================================================================
     *  HÀM DÙNG CHUNG
     * ===================================================================== */

    /** Trạng thái "đã đăng" trong Faq::TRANG_THAI (khác 'nhap'). */
    private function khoaDang(): string
    {
        $keys = array_keys(Faq::TRANG_THAI);

        foreach (['xuat_ban', 'da_xuat_ban', 'da_duyet', 'published'] as $uuTien) {
            if (in_array($uuTien, $keys, true)) {
                return $uuTien;
            }
        }

        foreach ($keys as $k) {
            if ($k !== 'nhap') {
                return (string) $k;
            }
        }

        return (string) ($keys[0] ?? 'nhap');
    }

    /** Truy vấn câu hỏi với các cột đã đổi về tên chuẩn: id, tieu_de, noi_dung, nguoi_gui, ngay_gui, danh_muc_id, danh_muc. */
    private function truyVanCauHoi(array $c, array $dm)
    {
        $b = self::BANG;

        $chon = function (string $key) use ($b, $c) {
            return $c[$key]
                ? DB::raw("`$b`.`{$c[$key]}` as `$key`")
                : DB::raw("NULL as `$key`");
        };

        $q = DB::table($b)->select([
            $chon('id'), $chon('tieu_de'), $chon('noi_dung'), $chon('nguoi_gui'), $chon('ngay_gui'), $chon('danh_muc_id'),
        ]);

        if ($c['danh_muc_id']) {
            $q->leftJoin(self::BANG_DANH_MUC, self::BANG_DANH_MUC . ".{$dm['id']}", '=', "$b.{$c['danh_muc_id']}")
              ->addSelect(DB::raw('`' . self::BANG_DANH_MUC . "`.`{$dm['ten']}` as `danh_muc`"));
        } else {
            $q->addSelect(DB::raw('NULL as `danh_muc`'));
        }

        return $q;
    }

    private function layCauHoiChoDuyet($id)
    {
        $c = $this->cotCauHoi();
        $b = self::BANG;

        return $this->truyVanCauHoi($c, $this->cotDanhMuc())
            ->where("$b.{$c['id']}", $id)
            ->whereIn("$b.{$c['trang_thai']}", self::TT_CHO_DUYET)
            ->first();
    }

    private function dsDanhMuc(array $dm)
    {
        return DB::table(self::BANG_DANH_MUC)
            ->orderBy($dm['id'])
            ->get([$dm['id'] . ' as id', $dm['ten'] . ' as ten']);
    }

    /** Tên cột thật của bảng cau_hoi (cột không bắt buộc mà không có thì để null). */
    private function cotCauHoi(): array
    {
        $batBuoc = ['id' => 'khóa chính', 'tieu_de' => 'tiêu đề câu hỏi', 'trang_thai' => 'trạng thái'];
        $kq = [];

        foreach (self::UNG_VIEN as $key => $dsTen) {
            $kq[$key] = $this->timCot(self::BANG, $dsTen, $batBuoc[$key] ?? null);
        }

        return $kq;
    }

    private function cotDanhMuc(): array
    {
        return [
            'id'  => $this->timCot(self::BANG_DANH_MUC, self::UNG_VIEN_DM['id'], 'khóa chính danh mục'),
            'ten' => $this->timCot(self::BANG_DANH_MUC, self::UNG_VIEN_DM['ten'], 'tên danh mục'),
        ];
    }

    /** Tìm cột đầu tiên trong $ungVien có thật trong $bang; $nhan khác null nghĩa là bắt buộc. */
    private function timCot(string $bang, array $ungVien, ?string $nhan = null): ?string
    {
        $cacCot = Schema::getColumnListing($bang);
        $bangTra = [];
        foreach ($cacCot as $cot) {
            $bangTra[mb_strtolower($cot)] = $cot;
        }

        foreach ($ungVien as $ten) {
            if (isset($bangTra[mb_strtolower($ten)])) {
                return $bangTra[mb_strtolower($ten)];
            }
        }

        if ($nhan !== null) {
            throw new \RuntimeException(
                "Không tìm thấy cột \"$nhan\" trong bảng `$bang`. Các cột hiện có: " . implode(', ', $cacCot)
                . '. Hãy thêm tên cột đúng vào danh sách UNG_VIEN ở đầu CauHoiController.'
            );
        }

        return null;
    }
}