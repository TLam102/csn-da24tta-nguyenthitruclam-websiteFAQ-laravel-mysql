<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GiangVienCauHoiController extends Controller
{
    private const ROLE_GV     = 'giang_vien';

    private const TBL_CAUHOI  = 'cau_hoi';
    private const PK          = 'cau_hoi_id';
    private const TBL_DANHMUC = 'danh_muc';
    private const DM_NAME_COL = 'ten_danh_muc';
    private const TT_TU_CHOI  = 'bi_tu_choi';
    private const TT_DA_TRA_LOI = 'da_tra_loi';

    private const TBL_TRALOI  = 'cau_tra_loi';
    private const TL_FK       = 'cau_hoi_id';
    private const TL_USER     = 'nguoi_tra_loi_id';

    private function authorizeGv(): void
    {
        abort_unless(auth()->check() && auth()->user()->vai_tro === self::ROLE_GV, 403);
    }

    /** Chỉ giữ lại thẻ định dạng an toàn, xóa mọi thuộc tính (chống chèn mã độc) */
    public static function cleanHtml(?string $html): string
    {
        $html = strip_tags((string) $html, '<b><strong><i><em><u><ul><ol><li><br><p><div>');
        $html = preg_replace('/<(b|strong|i|em|u|ul|ol|li|br|p|div)\b[^>]*>/i', '<$1>', $html);
        return trim($html);
    }

    private function mine()
    {
        return DB::table(self::TBL_CAUHOI . ' as c')
            ->where('c.giang_vien_id', auth()->id())
            ->where('c.trang_thai', '!=', self::TT_TU_CHOI);
    }

    private function traLoiExists(): \Closure
    {
        return function ($sub) {
            $sub->select(DB::raw(1))
                ->from(self::TBL_TRALOI)
                ->whereColumn(self::TL_FK, 'c.' . self::PK);
        };
    }

    private function base()
    {
        return $this->mine()
            ->leftJoin(self::TBL_DANHMUC . ' as d', 'd.danh_muc_id', '=', 'c.danh_muc_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.nguoi_gui_id')
            ->select(
                'c.*',
                'c.' . self::PK . ' as cau_hoi_pk',
                'c.ngay_gui as created_at',
                'd.' . self::DM_NAME_COL . ' as tendanhmuc',
                'u.name as nguoi_gui'
            )
            ->selectSub(
                DB::table(self::TBL_TRALOI)->selectRaw('count(*)')
                    ->whereColumn(self::TL_FK, 'c.' . self::PK),
                'so_tra_loi'
            );
    }

    private function stats(): array
    {
        return [
            'tong' => $this->mine()->count(),
            'moi'  => $this->mine()->where('c.giang_vien_da_xem', false)->count(),
            'chua' => $this->mine()->whereNotExists($this->traLoiExists())->count(),
            'da'   => $this->mine()->whereExists($this->traLoiExists())->count(),
        ];
    }

    private function faqInfo(): array
    {
        $empty = ['total' => 0, 'published' => 0, 'list' => collect()];

        try {
            if (!Schema::hasTable('faq')) {
                return $empty;
            }

            $pk = collect(['faq_id', 'id'])->first(fn ($c) => Schema::hasColumn('faq', $c));
            $titleCol = collect(['tieu_de', 'cau_hoi', 'tieu_de_faq', 'question'])
                ->first(fn ($c) => Schema::hasColumn('faq', $c));

            $total = DB::table('faq')->count();

            $published = Schema::hasColumn('faq', 'trang_thai')
                ? DB::table('faq')->whereIn('trang_thai', ['1', 'da_dang', 'da_xuat_ban', 'hien_thi', 'xuat_ban'])->count()
                : $total;

            $query = DB::table('faq');
            if ($pk) {
                $query->orderByDesc($pk);
            }

            $list = $query->limit(5)->get()->map(function ($f) use ($pk, $titleCol) {
                return (object) [
                    'id'         => $pk ? $f->$pk : null,
                    'title'      => $titleCol ? $f->$titleCol : '(FAQ)',
                    'trang_thai' => $f->trang_thai ?? null,
                ];
            });

            return ['total' => $total, 'published' => $published, 'list' => $list];
        } catch (\Throwable $e) {
            return $empty;
        }
    }

    // ---------- Trang tổng quan ----------
    public function dashboard()
    {
        $this->authorizeGv();

        $stats  = $this->stats();
        $recent = $this->base()->orderByDesc('c.ngay_gui')->limit(8)->get();
        $faq    = $this->faqInfo();
        $rate   = $stats['tong'] > 0 ? (int) round($stats['da'] / $stats['tong'] * 100) : 0;

        $showNotice = false;
        if ($stats['moi'] > 0 && !session()->has('gv_notice_shown')) {
            $showNotice = true;
            session()->put('gv_notice_shown', true);
        }

        $students = $recent->unique('nguoi_gui_id')->take(4)->values();

        return view('giangvien.dashboard', compact('stats', 'recent', 'faq', 'rate', 'showNotice', 'students'));
    }

    // ---------- Danh sách ----------
    public function index(Request $request)
    {
        $this->authorizeGv();

        $tab = $request->query('tab', 'tat-ca');
        $q   = trim((string) $request->query('q', ''));

        $query = $this->base();

        if ($tab === 'moi') {
            $query->where('c.giang_vien_da_xem', false);
        } elseif ($tab === 'chua-tra-loi') {
            $query->whereNotExists($this->traLoiExists());
        } elseif ($tab === 'da-tra-loi') {
            $query->whereExists($this->traLoiExists());
        } else {
            $tab = 'tat-ca';
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('c.tieu_de', 'like', "%{$q}%")
                  ->orWhere('c.noi_dung', 'like', "%{$q}%");
            });
        }

        $cauHois = $query->orderBy('c.giang_vien_da_xem')
            ->orderByDesc('c.ngay_gui')
            ->paginate(10)
            ->withQueryString();

        // Nhận cả hai kiểu đặt tên file view: index.blade.php hoặc indexcauhoi.blade.php
        return view()->first(['giangvien.cauhoi.index', 'giangvien.cauhoi.indexcauhoi'], [
            'cauHois' => $cauHois,
            'tab'     => $tab,
            'q'       => $q,
            'stats'   => $this->stats(),
        ]);
    }

    // ---------- Chi tiết + trả lời ----------
    public function show($id)
    {
        $this->authorizeGv();

        $cauHoi = $this->base()->where('c.' . self::PK, $id)->first();
        abort_if(!$cauHoi, 404);

        if (!$cauHoi->giang_vien_da_xem) {
            DB::table(self::TBL_CAUHOI)
                ->where(self::PK, $id)
                ->where('giang_vien_id', auth()->id())
                ->update(['giang_vien_da_xem' => true, 'giang_vien_xem_luc' => now()]);
        }

        $traLois = DB::table(self::TBL_TRALOI . ' as t')
            ->leftJoin('users as u', 'u.id', '=', 't.' . self::TL_USER)
            ->where('t.' . self::TL_FK, $id)
            ->orderBy('t.ngay_tra_loi')
            ->select('t.*', 't.ngay_tra_loi as created_at', 'u.name as nguoi_tra_loi')
            ->get();

        // Các bảng phụ: nếu chưa tạo (chưa chạy migrate) thì trang vẫn mở được, chỉ trống phần đó
        $tepTraLoi = ($traLois->isEmpty() || !Schema::hasTable('cau_tra_loi_tep'))
            ? collect()
            : DB::table('cau_tra_loi_tep')->whereIn('tra_loi_id', $traLois->pluck('tra_loi_id'))->get()->groupBy('tra_loi_id');

        $tepCauHoi = Schema::hasTable('cau_hoi_tep')
            ? DB::table('cau_hoi_tep')->where('cau_hoi_id', $id)->get()
            : collect();

        // Thông tin sinh viên
        $sv = DB::table('users')->where('id', $cauHoi->nguoi_gui_id)->first();
        $svStats = [
            'gui_den_toi' => DB::table(self::TBL_CAUHOI)->where('nguoi_gui_id', $cauHoi->nguoi_gui_id)->where('giang_vien_id', auth()->id())->count(),
            'tong'        => DB::table(self::TBL_CAUHOI)->where('nguoi_gui_id', $cauHoi->nguoi_gui_id)->count(),
        ];

        // Câu trả lời mẫu: mẫu chung + mẫu của riêng giảng viên
        $mau = Schema::hasTable('mau_tra_loi')
            ? DB::table('mau_tra_loi')
                ->where(function ($q) {
                    $q->whereNull('user_id')->orWhere('user_id', auth()->id());
                })
                ->orderByRaw('user_id is null desc')
                ->orderBy('id')
                ->get()
            : collect();

        // Nhận cả hai kiểu đặt tên file view: show.blade.php hoặc showcauhoi.blade.php
        return view()->first(['giangvien.cauhoi.show', 'giangvien.cauhoi.showcauhoi'], [
            'cauHoi'    => $cauHoi,
            'traLois'   => $traLois,
            'tepTraLoi' => $tepTraLoi,
            'tepCauHoi' => $tepCauHoi,
            'sv'        => $sv,
            'svStats'   => $svStats,
            'mau'       => $mau,
            'stats'     => $this->stats(),
        ]);
    }

    // ---------- Gửi trả lời: đăng ngay + thông báo cho sinh viên ----------
    public function reply(Request $request, $id)
    {
        $this->authorizeGv();

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

        $cauHoi = $this->mine()->where('c.' . self::PK, $id)->first();
        abort_if(!$cauHoi, 404);

        $html = self::cleanHtml($request->input('noi_dung'));
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
        $text = trim(str_replace("\xC2\xA0", ' ', $text));

        if (mb_strlen($text) < 5) {
            return back()->withInput()->withErrors(['noi_dung' => 'Câu trả lời quá ngắn (ít nhất 5 ký tự).']);
        }

        DB::transaction(function () use ($request, $id, $html, $cauHoi) {
            $traLoiId = DB::table(self::TBL_TRALOI)->insertGetId([
                self::TL_FK    => $id,
                self::TL_USER  => auth()->id(),
                'noi_dung'     => $html,
                'ngay_tra_loi' => now(),
            ], 'tra_loi_id');

            // Tệp đính kèm (lưu vào storage/app/public/tra-loi)
            if (Schema::hasTable('cau_tra_loi_tep')) {
                foreach ((array) $request->file('tep', []) as $file) {
                    $ten  = $file->getClientOriginalName();
                    $size = $file->getSize();
                    $path = $file->store('tra-loi', 'public');

                    DB::table('cau_tra_loi_tep')->insert([
                        'tra_loi_id' => $traLoiId,
                        'ten_goc'    => $ten,
                        'duong_dan'  => $path,
                        'kich_thuoc' => $size,
                        'created_at' => now(),
                    ]);
                }
            }

            // Đánh dấu đã xử lý (nếu cột trang_thai chưa nhận giá trị này thì bỏ qua, không làm hỏng việc đăng)
            try {
                DB::table(self::TBL_CAUHOI)->where(self::PK, $id)->update([
                    'trang_thai'         => self::TT_DA_TRA_LOI,
                    'giang_vien_da_xem'  => true,
                    'giang_vien_xem_luc' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('GiangVien reply - cập nhật trang_thai: ' . $e->getMessage());
                DB::table(self::TBL_CAUHOI)->where(self::PK, $id)->update([
                    'giang_vien_da_xem'  => true,
                    'giang_vien_xem_luc' => now(),
                ]);
            }

            // Thông báo cho sinh viên gửi câu hỏi (lỗi ở bước này không làm mất câu trả lời)
            try {
                if (Schema::hasTable('thong_bao')) {
                    // Nếu câu hỏi đã được đăng lên FAQ thì mở thẳng trang chi tiết FAQ (có phần trả lời)
                    $faqId = Schema::hasTable('faq')
                        ? DB::table('faq')->where('cau_hoi_id', $id)->where('trang_thai', 'da_xuat_ban')->value('faq_id')
                        : null;
                    $lienKet = $faqId
                        ? route('faqs.show', $faqId, false)
                        : route('cauhoi.index', [], false) . '#ch-' . $id;

                    DB::table('thong_bao')->insert([
                        'user_id'    => $cauHoi->nguoi_gui_id,
                        'tieu_de'    => 'Câu hỏi của bạn đã được trả lời',
                        'noi_dung'   => auth()->user()->name . ' đã trả lời câu hỏi: ' . \Illuminate\Support\Str::limit($cauHoi->tieu_de, 120),
                        'lien_ket'   => $lienKet,
                        'da_doc'     => false,
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('GiangVien reply - thông báo: ' . $e->getMessage());
            }
        });

        return redirect()->route('giangvien.cauhoi.show', $id)
            ->with('success', 'Đã đăng câu trả lời và gửi thông báo cho sinh viên.');
    }

    // ---------- Lưu / xóa câu trả lời mẫu ----------
    public function storeTemplate(Request $request)
    {
        $this->authorizeGv();

        $data = $request->validate([
            'tieu_de'  => ['required', 'string', 'max:120'],
            'noi_dung' => ['required', 'string', 'max:20000'],
        ], [
            'tieu_de.required'  => 'Vui lòng đặt tên cho mẫu.',
            'noi_dung.required' => 'Nội dung mẫu đang trống.',
        ]);

        DB::table('mau_tra_loi')->insert([
            'user_id'    => auth()->id(),
            'tieu_de'    => $data['tieu_de'],
            'noi_dung'   => self::cleanHtml($data['noi_dung']),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã lưu câu trả lời mẫu.');
    }

    public function destroyTemplate($id)
    {
        $this->authorizeGv();

        DB::table('mau_tra_loi')->where('id', $id)->where('user_id', auth()->id())->delete();

        return back()->with('success', 'Đã xóa câu trả lời mẫu.');
    }

    // ---------- Đánh dấu tất cả đã xem ----------
    public function markAllRead()
    {
        $this->authorizeGv();

        DB::table(self::TBL_CAUHOI)
            ->where('giang_vien_id', auth()->id())
            ->where('giang_vien_da_xem', false)
            ->update(['giang_vien_da_xem' => true, 'giang_vien_xem_luc' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả câu hỏi là đã xem.');
    }
}