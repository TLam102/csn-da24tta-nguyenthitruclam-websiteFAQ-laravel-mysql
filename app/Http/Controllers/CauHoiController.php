<?php

namespace App\Http\Controllers;

use App\Models\CauHoiSinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CauHoiController extends Controller
{
    private const TBL_CAUHOI  = 'cau_hoi';
    private const TBL_DANHMUC = 'danh_muc';
    private const TRANG_THAI_CHO_DUYET = 'cho_duyet';

    private const TBL_GIANGVIEN = 'users';
    private const GV_ID_COL     = 'id';
    private const GV_ROLE_COL   = 'vai_tro';
    private const GV_ROLE_VALUE = 'giang_vien';

    /** Trang "Câu hỏi của tôi" (sinh viên) */
   /** Trang "Câu hỏi của tôi" (sinh viên) */
public function index()
{
    $cauHois = CauHoiSinhVien::with(['giangVien', 'cauTraLois.nguoiTraLoi'])
        ->where('nguoi_gui_id', auth()->id())
        ->orderByDesc('ngay_gui')
        ->paginate(10);

    $traLoiIds = $cauHois->pluck('cauTraLois')->flatten()->pluck('tra_loi_id');
    $tep = $traLoiIds->isEmpty()
        ? collect()
        : DB::table('cau_tra_loi_tep')->whereIn('tra_loi_id', $traLoiIds)->get()->groupBy('tra_loi_id');

    return view('cauhoi.index', compact('cauHois', 'tep'));
}
    public function create()
    {
        return view('cauhoi.create', [
            'categories' => $this->categories(),
            'giangViens' => $this->giangViens(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'danh_muc'      => ['required', 'string'],
            'giang_vien_id' => [
                'nullable',
                'integer',
                Rule::exists(self::TBL_GIANGVIEN, self::GV_ID_COL)
                    ->where(self::GV_ROLE_COL, self::GV_ROLE_VALUE),
            ],
            'tieu_de'       => ['required', 'string', 'max:255'],
            'noi_dung'      => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'danh_muc.required'    => 'Vui lòng chọn danh mục.',
            'giang_vien_id.exists' => 'Giảng viên được chọn không hợp lệ.',
            'tieu_de.required'     => 'Vui lòng nhập tiêu đề câu hỏi.',
            'tieu_de.max'          => 'Tiêu đề không được quá 255 ký tự.',
            'noi_dung.required'    => 'Vui lòng nhập nội dung chi tiết.',
            'noi_dung.min'         => 'Nội dung quá ngắn, hãy mô tả rõ hơn (ít nhất 10 ký tự).',
            'noi_dung.max'         => 'Nội dung không được quá 3000 ký tự.',
        ]);

        // Cột nguoi_gui_id có thể bắt buộc, nên yêu cầu đăng nhập khi gửi
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi câu hỏi.');
        }

        $danhMucId = DB::table(self::TBL_DANHMUC)->where('slug', $data['danh_muc'])->value('danh_muc_id');

        // Giảng viên được chọn phải phụ trách đúng lĩnh vực (danh mục) của câu hỏi
        if (!empty($data['giang_vien_id']) && Schema::hasTable('phan_cong_linh_vuc')) {
            $phuTrach = DB::table('phan_cong_linh_vuc')
                ->where('giang_vien_id', $data['giang_vien_id'])
                ->where('danh_muc_id', $danhMucId)
                ->exists();

            if (!$phuTrach) {
                return back()->withInput()->withErrors([
                    'giang_vien_id' => 'Giảng viên này không phụ trách lĩnh vực đã chọn. Hãy chọn lại giảng viên hoặc để "Không chọn (gửi chung)".',
                ]);
            }
        }

        try {
            DB::table(self::TBL_CAUHOI)->insert([
                'nguoi_gui_id'  => auth()->id(),
                'danh_muc_id'   => $danhMucId,
                'giang_vien_id' => $data['giang_vien_id'] ?? null,
                'tieu_de'       => $data['tieu_de'],
                'noi_dung'      => $data['noi_dung'],
                'trang_thai'    => self::TRANG_THAI_CHO_DUYET,
                'ngay_gui'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('CauHoiController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Chưa gửi được câu hỏi, vui lòng thử lại sau.');
        }

        return redirect()->route('cauhoi.create')
            ->with('success', 'Gửi câu hỏi thành công! Câu hỏi sẽ được kiểm duyệt trước khi hiển thị.');
    }

    /**
     * Danh sách giảng viên kèm các lĩnh vực phụ trách:
     *  - $gv->linh_vuc: mảng slug danh mục giảng viên phụ trách (để trang lọc theo danh mục đã chọn)
     * Nếu chưa có bảng phan_cong_linh_vuc thì không lọc (giữ nguyên hành vi cũ).
     */
    private function giangViens()
    {
        try {
            $ds = DB::table(self::TBL_GIANGVIEN)
                ->where(self::GV_ROLE_COL, self::GV_ROLE_VALUE)
                ->where('trang_thai', 1)
                ->select(self::GV_ID_COL . ' as id', 'name')
                ->orderBy('name')
                ->get();

            $coPhanCong = Schema::hasTable('phan_cong_linh_vuc');
            $theoGv = $coPhanCong
                ? DB::table('phan_cong_linh_vuc as p')
                    ->join(self::TBL_DANHMUC . ' as d', 'd.danh_muc_id', '=', 'p.danh_muc_id')
                    ->get(['p.giang_vien_id', 'd.slug'])
                    ->groupBy('giang_vien_id')
                : collect();

            return $ds->map(function ($gv) use ($theoGv, $coPhanCong) {
                $gv->loc_theo_linh_vuc = $coPhanCong;
                $gv->linh_vuc = $theoGv->get($gv->id, collect())->pluck('slug')->values()->all();
                return $gv;
            });
        } catch (\Throwable $e) {
            Log::warning('CauHoiController@giangViens: ' . $e->getMessage());
            return collect();
        }
    }

    private function categories()
    {
        try {
            $list = DB::table(self::TBL_DANHMUC)
                ->where('trang_thai', 1)
                ->where('slug', '!=', 'khac')
                ->orderBy('danh_muc_id')
                ->select('ten_danh_muc as tendanhmuc', 'slug')
                ->get();
            if ($list->isNotEmpty()) {
                return $list;
            }
        } catch (\Throwable $e) {
            Log::warning('CauHoiController: ' . $e->getMessage());
        }

        return collect([
            ['tendanhmuc' => 'Đào tạo',             'slug' => 'dao-tao'],
            ['tendanhmuc' => 'Đăng ký học phần',    'slug' => 'dang-ky-hoc-phan'],
            ['tendanhmuc' => 'Học vụ',              'slug' => 'hoc-vu'],
            ['tendanhmuc' => 'Thực tập',            'slug' => 'thuc-tap'],
            ['tendanhmuc' => 'Đồ án - Khóa luận',   'slug' => 'do-an-khoa-luan'],
            ['tendanhmuc' => 'Nghiên cứu khoa học', 'slug' => 'nghien-cuu-khoa-hoc'],
            ['tendanhmuc' => 'Học bổng',            'slug' => 'hoc-bong'],
        ])->map(fn ($c) => (object) $c);
    }
}