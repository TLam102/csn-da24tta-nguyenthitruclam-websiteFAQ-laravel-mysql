<?php

namespace App\Http\Controllers;

use App\Support\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    // ===== TÊN BẢNG / CỘT THẬT TRONG CSDL faq_it =====
    private const TBL_FAQ      = 'faq';
    private const TBL_DANHMUC  = 'danh_muc';
    private const COT_TEN_DM   = 'ten_danh_muc';
    private const TRANG_THAI_XUAT_BAN = 'da_xuat_ban';
    private const PER_PAGE     = 10;
    private const TBL_TRALOI   = 'faq_tra_loi'; // lượt trả lời cho từng FAQ
    private const TBL_TL_GV    = 'cau_tra_loi'; // câu trả lời của giảng viên (gắn với câu hỏi gốc cau_hoi)
    private const TBL_TEP_FAQ  = 'faq_tra_loi_tep';  // tệp đính kèm của lượt trả lời trên FAQ
    private const TBL_TEP_GV   = 'cau_tra_loi_tep';  // tệp đính kèm của giảng viên (khu giảng viên)

    /** Mọi tài khoản đã đăng nhập (sinh viên, giảng viên, quản trị viên) đều được trả lời */
    private function canReply(): bool
    {
        return auth()->check();
    }

    /** Làm sạch nội dung trả lời (hỗ trợ cả dữ liệu cũ dạng văn bản thuần). */
    private function lamSach(?string $s): string
    {
        $s = (string) $s;
        if ($s === strip_tags($s)) {
            return nl2br(e($s));
        }
        return GiangVienCauHoiController::cleanHtml($s);
    }

    /**
     * Gom MỌI lượt trả lời của một FAQ:
     *  - trả lời đăng trực tiếp trên FAQ (bảng faq_tra_loi)
     *  - trả lời của giảng viên cho câu hỏi gốc (bảng cau_tra_loi, qua faq.cau_hoi_id)
     * Sắp xếp theo thời gian.
     */
    private function layTraLoi(int $faqId, $cauHoiId)
    {
        $ds = collect();

        if (Schema::hasTable(self::TBL_TRALOI)) {
            $ds = $ds->concat(
                DB::table(self::TBL_TRALOI . ' as t')
                    ->leftJoin('users as u', 'u.id', '=', 't.nguoi_tra_loi_id')
                    ->where('t.faq_id', $faqId)
                    ->select('t.id', 't.noi_dung', 't.ngay_tra_loi', 'u.name as nguoi')
                    ->addSelect(DB::raw("'faq' as nguon"))
                    ->get()
            );
        }

        if ($cauHoiId && Schema::hasTable(self::TBL_TL_GV)) {
            $ds = $ds->concat(
                DB::table(self::TBL_TL_GV . ' as g')
                    ->leftJoin('users as u', 'u.id', '=', 'g.nguoi_tra_loi_id')
                    ->where('g.cau_hoi_id', $cauHoiId)
                    ->select('g.tra_loi_id as id', 'g.noi_dung', 'g.ngay_tra_loi', 'u.name as nguoi')
                    ->addSelect(DB::raw("'gv' as nguon"))
                    ->get()
            );
        }

        $ds = $ds->sortBy('ngay_tra_loi')->values();

        // Gắn tệp đính kèm cho từng lượt trả lời
        $tepFaq = $this->tepTheo(self::TBL_TEP_FAQ, $ds->where('nguon', 'faq')->pluck('id')->all());
        $tepGv  = $this->tepTheo(self::TBL_TEP_GV, $ds->where('nguon', 'gv')->pluck('id')->all());

        foreach ($ds as $r) {
            $nguon = $r->nguon === 'gv' ? $tepGv : $tepFaq;
            $r->tep = $nguon->get($r->id, collect())->values()->all();
        }

        return $ds;
    }

    /** Lấy tệp đính kèm theo danh sách id lượt trả lời, gom nhóm theo tra_loi_id. */
    private function tepTheo(string $bang, array $ids)
    {
        if (!$ids || !Schema::hasTable($bang)) {
            return collect();
        }

        return DB::table($bang)->whereIn('tra_loi_id', $ids)->orderBy('id')->get()
            ->map(fn ($f) => $this->dinhDangTep($f))
            ->groupBy('tra_loi_id');
    }

    /** Một dòng bảng tệp -> mảng dùng cho view / JSON. */
    private function dinhDangTep($f): array
    {
        return [
            'tra_loi_id' => $f->tra_loi_id,
            'ten'        => $f->ten_goc,
            'url'        => asset('storage/' . ltrim($f->duong_dan, '/')),
            'kich_thuoc' => (int) ($f->kich_thuoc ?? 0),
            'la_anh'     => (bool) preg_match('/\.(jpe?g|png|webp)$/i', (string) $f->duong_dan),
        ];
    }

    /** Biểu thức SQL đếm tổng lượt trả lời của một FAQ (cả hai nguồn). */
    private function bieuThucDemTraLoi(): string
    {
        $phan = [];
        if (Schema::hasTable(self::TBL_TRALOI)) {
            $phan[] = '(select count(*) from ' . self::TBL_TRALOI . ' where ' . self::TBL_TRALOI . '.faq_id = f.faq_id)';
        }
        if (Schema::hasTable(self::TBL_TL_GV)) {
            $phan[] = '(select count(*) from ' . self::TBL_TL_GV . ' where ' . self::TBL_TL_GV . '.cau_hoi_id = f.cau_hoi_id)';
        }
        return $phan ? implode(' + ', $phan) : '0';
    }

    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $slug = (string) $request->query('danh_muc', '');

        try {
            $categories = DB::table(self::TBL_DANHMUC)
                ->where('trang_thai', 1)
                ->where('slug', '!=', 'khac')
                ->select('danh_muc_id', self::COT_TEN_DM . ' as tendanhmuc', 'slug')
                ->orderBy('danh_muc_id')
                ->get();

            $query = DB::table(self::TBL_FAQ . ' as f')
                ->leftJoin(self::TBL_DANHMUC . ' as d', 'd.danh_muc_id', '=', 'f.danh_muc_id')
                ->where('f.trang_thai', self::TRANG_THAI_XUAT_BAN);

            if ($slug !== '') {
                $query->where('d.slug', $slug);
            }
            if ($q !== '') {
                $query->where('f.cau_hoi', 'like', '%' . $q . '%');
            }

            $query->select('f.faq_id', 'f.cau_hoi', 'f.ngay_cap_nhat', 'f.ngay_tao',
                           'd.' . self::COT_TEN_DM . ' as tendanhmuc', 'd.slug');

            // Số lượt trả lời của mỗi FAQ (gồm cả trả lời của giảng viên cho câu hỏi gốc)
            $query->selectRaw('(' . $this->bieuThucDemTraLoi() . ') as so_tra_loi');

            $faqs = $query
                ->orderByRaw('COALESCE(f.ngay_cap_nhat, f.ngay_tao) DESC')
                ->paginate(self::PER_PAGE)
                ->withQueryString();
        } catch (\Throwable $e) {
            Log::error('FaqController@index: ' . $e->getMessage());
            $categories = collect();
            $faqs = new LengthAwarePaginator([], 0, self::PER_PAGE, 1, ['path' => $request->url()]);
        }

        $canReply = $this->canReply();

        return view('faq.index', compact('faqs', 'categories', 'q', 'slug', 'canReply'));
    }

    // ===== LỊCH SỬ TRAO ĐỔI CỦA MỘT FAQ (JSON, mở trong cửa sổ "Trả lời") =====
    public function replies(int $id)
    {
        $faq = DB::table(self::TBL_FAQ . ' as f')
            ->leftJoin('users as u', 'u.id', '=', 'f.nguoi_tao_id')
            ->leftJoin('cau_hoi as c', 'c.cau_hoi_id', '=', 'f.cau_hoi_id')
            ->leftJoin('users as s', 's.id', '=', 'c.nguoi_gui_id')
            ->where('f.faq_id', $id)
            ->where('f.trang_thai', self::TRANG_THAI_XUAT_BAN)
            ->select('f.faq_id', 'f.cau_hoi_id', 'f.cau_hoi', 'f.ngay_tao', 'f.ngay_cap_nhat',
                     'u.name as nguoi_dang', 's.name as nguoi_gui')
            ->first();

        abort_if(!$faq, 404);

        $replies = $this->layTraLoi($id, $faq->cau_hoi_id)->map(fn ($r) => [
            'nguoi'     => $r->nguoi ?? 'Người dùng',
            'thoi_gian' => $r->ngay_tra_loi ? \Carbon\Carbon::parse($r->ngay_tra_loi)->format('d/m/Y H:i') : '',
            'noi_dung'  => $this->lamSach($r->noi_dung),
            'tep'       => $r->tep,
        ]);

        $ngay = $faq->ngay_tao ?: $faq->ngay_cap_nhat;

        return response()->json([
            'id'         => $faq->faq_id,
            'cau_hoi'    => $faq->cau_hoi,
            // Ưu tiên tên tài khoản đã gửi câu hỏi; không có thì dùng người tạo FAQ
            'nguoi_dang' => $faq->nguoi_gui ?? $faq->nguoi_dang ?? 'Khoa CNTT',
            'ngay'       => $ngay ? \Carbon\Carbon::parse($ngay)->format('d/m/Y H:i') : '',
            'can_reply'  => $this->canReply(),
            'detail_url' => route('faqs.show', $faq->faq_id),
            'replies'    => $replies->values(),
        ]);
    }

    // ===== GỬI THÊM MỘT LƯỢT TRẢ LỜI (mọi tài khoản đã đăng nhập, có thể đính kèm tệp) =====
    public function reply(Request $request, int $id)
    {
        abort_unless($this->canReply(), 403, 'Bạn cần đăng nhập để trả lời.');
        abort_unless(Schema::hasTable(self::TBL_TRALOI), 503, 'Chưa tạo bảng faq_tra_loi. Hãy chạy: php artisan migrate');

        $request->validate([
            'noi_dung' => ['required', 'string', 'max:20000'],
            'tep'      => ['nullable', 'array', 'max:5'],
            'tep.*'    => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt'],
        ], [
            'noi_dung.required' => 'Vui lòng nhập nội dung trả lời.',
            'tep.max'           => 'Chỉ được đính kèm tối đa 5 tệp.',
            'tep.*.max'         => 'Mỗi tệp không được vượt quá 5MB.',
            'tep.*.mimes'       => 'Tệp chỉ nhận: ảnh, PDF, Word, Excel, PowerPoint, TXT.',
            'tep.*.uploaded'    => 'Không tải được tệp (vượt giới hạn của máy chủ).',
        ]);

        $files = array_filter((array) $request->file('tep', []));
        if ($files && !Schema::hasTable(self::TBL_TEP_FAQ)) {
            return response()->json([
                'message' => 'Chưa tạo bảng faq_tra_loi_tep nên chưa đính kèm được tệp.',
                'errors'  => ['tep' => ['Chưa tạo bảng faq_tra_loi_tep (chạy php artisan migrate).']],
            ], 422);
        }

        $faq = DB::table(self::TBL_FAQ)
            ->where('faq_id', $id)
            ->where('trang_thai', self::TRANG_THAI_XUAT_BAN)
            ->first();
        abort_if(!$faq, 404);

        $html = GiangVienCauHoiController::cleanHtml($request->input('noi_dung'));
        $text = trim(str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8')));

        if (mb_strlen($text) < 5) {
            return response()->json([
                'message' => 'Câu trả lời quá ngắn.',
                'errors'  => ['noi_dung' => ['Câu trả lời quá ngắn (ít nhất 5 ký tự).']],
            ], 422);
        }

        $now = now();
        $dsTep = [];
        $daLuu = []; // đường dẫn tệp đã ghi, để xóa nếu có lỗi giữa chừng

        try {
            DB::beginTransaction();

            $traLoiId = DB::table(self::TBL_TRALOI)->insertGetId([
                'faq_id'           => $id,
                'nguoi_tra_loi_id' => auth()->id(),
                'noi_dung'         => $html,
                'ngay_tra_loi'     => $now,
            ], 'id');

            // Lưu tệp vào storage/app/public/tra-loi (ghi trực tiếp, không qua lớp Storage)
            $thuMuc = storage_path('app/public/tra-loi');
            if (!is_dir($thuMuc)) {
                @mkdir($thuMuc, 0775, true);
            }

            foreach ($files as $file) {
                if (!$file->isValid()) {
                    throw new \RuntimeException('Tệp "' . $file->getClientOriginalName() . '" tải lên không hợp lệ: ' . $file->getErrorMessage());
                }

                $ten  = $file->getClientOriginalName();
                $size = $file->getSize();
                $ext  = strtolower($file->getClientOriginalExtension() ?: ($file->guessExtension() ?: 'bin'));
                $tenLuu = Str::random(40) . '.' . $ext;

                $file->move($thuMuc, $tenLuu);
                $path = 'tra-loi/' . $tenLuu;
                $daLuu[] = $thuMuc . DIRECTORY_SEPARATOR . $tenLuu;

                DB::table(self::TBL_TEP_FAQ)->insert([
                    'tra_loi_id' => $traLoiId,
                    'ten_goc'    => $ten,
                    'duong_dan'  => $path,
                    'kich_thuoc' => $size,
                    'created_at' => $now,
                ]);

                $dsTep[] = $this->dinhDangTep((object) [
                    'tra_loi_id' => $traLoiId, 'ten_goc' => $ten, 'duong_dan' => $path, 'kich_thuoc' => $size,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($daLuu as $f) { @unlink($f); }
            Log::error('FaqController@reply: ' . $e);

            return response()->json([
                'message' => 'Không lưu được câu trả lời: ' . $e->getMessage(),
            ], 500);
        }

        // Thông báo cho người đã gửi câu hỏi gốc (không tự báo cho chính mình)
        $nguoiHoi = !empty($faq->cau_hoi_id)
            ? DB::table('cau_hoi')->where('cau_hoi_id', $faq->cau_hoi_id)->value('nguoi_gui_id')
            : null;

        if ($nguoiHoi && (int) $nguoiHoi !== (int) auth()->id()) {
            ThongBao::gui(
                $nguoiHoi,
                'Có người trả lời câu hỏi của bạn',
                auth()->user()->name . ' đã trả lời: "' . Str::limit($faq->cau_hoi, 80) . '"',
                route('faqs.show', $id, false)
            );
        }

        $soTraLoi = DB::table(self::TBL_TRALOI)->where('faq_id', $id)->count();

        return response()->json([
            'ok'         => true,
            'so_tra_loi' => $soTraLoi,
            'reply'      => [
                'nguoi'     => auth()->user()->name,
                'thoi_gian' => $now->format('d/m/Y H:i'),
                'noi_dung'  => $html,
                'tep'       => $dsTep,
            ],
        ]);
    }

    // ===== TRANG CHI TIẾT =====
    public function show(int $id)
    {
        $faq = DB::table(self::TBL_FAQ . ' as f')
            ->leftJoin(self::TBL_DANHMUC . ' as d', 'd.danh_muc_id', '=', 'f.danh_muc_id')
            ->leftJoin('users as u', 'u.id', '=', 'f.nguoi_tao_id')
            // Tài khoản đã gửi câu hỏi gốc (cau_hoi.nguoi_gui_id)
            ->leftJoin('cau_hoi as c', 'c.cau_hoi_id', '=', 'f.cau_hoi_id')
            ->leftJoin('users as s', 's.id', '=', 'c.nguoi_gui_id')
            // Giảng viên được chọn nhận câu hỏi (nếu có)
            ->leftJoin('users as g', 'g.id', '=', 'c.giang_vien_id')
            ->where('f.faq_id', $id)
            ->where('f.trang_thai', self::TRANG_THAI_XUAT_BAN)
            ->select('f.faq_id', 'f.cau_hoi_id', 'f.danh_muc_id', 'f.cau_hoi', 'f.cau_tra_loi',
                     'f.ngay_cap_nhat', 'f.ngay_tao', 'u.name as nguoi_dang',
                     's.name as nguoi_gui', 's.anh_dai_dien as nguoi_gui_anh',
                     'g.name as giang_vien', 'g.anh_dai_dien as giang_vien_anh',
                     'd.' . self::COT_TEN_DM . ' as tendanhmuc', 'd.slug')
            ->first();

        if (!$faq) {
            abort(404);
        }

        // Tăng lượt xem (bỏ qua nếu lỗi)
        try { DB::table(self::TBL_FAQ)->where('faq_id', $id)->increment('luot_xem'); } catch (\Throwable $e) {}

        $related = DB::table(self::TBL_FAQ)
            ->where('trang_thai', self::TRANG_THAI_XUAT_BAN)
            ->where('danh_muc_id', $faq->danh_muc_id)
            ->where('faq_id', '!=', $id)
            ->orderByDesc('luot_xem')
            ->limit(5)
            ->select('faq_id', 'cau_hoi')
            ->get();

        $voted = session('faq_voted_' . $id); // 'up' | 'down' | null

        // Tất cả lượt trả lời: trả lời trên FAQ + trả lời của giảng viên cho câu hỏi gốc
        $traLois = $this->layTraLoi($id, $faq->cau_hoi_id)->map(function ($r) {
            $r->noi_dung = $this->lamSach($r->noi_dung);
            return $r;
        });

        $canReply = $this->canReply();

        return view('faq.show', compact('faq', 'related', 'voted', 'traLois', 'canReply'));
    }

    public function vote(Request $request, int $id)
    {
        $request->validate(['value' => ['required', 'in:up,down']]);

        $key = 'faq_voted_' . $id;
        if (session()->has($key)) {
            return back()->with('vote_msg', 'Bạn đã đánh giá câu hỏi này rồi.');
        }

        try {
            $col = $request->value === 'up' ? 'huu_ich' : 'khong_huu_ich';
            DB::table(self::TBL_FAQ)->where('faq_id', $id)->increment($col);
        } catch (\Throwable $e) {
            Log::warning('FaqController@vote: ' . $e->getMessage());
        }

        session([$key => $request->value]);

        return back()->with('vote_msg', 'Cảm ơn bạn đã đánh giá!');
    }
}