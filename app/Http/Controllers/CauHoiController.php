<?php

namespace App\Http\Controllers;

use App\Models\CauHoiSinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        try {
            $danhMucId = DB::table(self::TBL_DANHMUC)->where('slug', $data['danh_muc'])->value('danh_muc_id');

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

    private function giangViens()
    {
        try {
            return DB::table(self::TBL_GIANGVIEN)
                ->where(self::GV_ROLE_COL, self::GV_ROLE_VALUE)
                ->where('trang_thai', 1)
                ->select(self::GV_ID_COL . ' as id', 'name')
                ->orderBy('name')
                ->get();
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