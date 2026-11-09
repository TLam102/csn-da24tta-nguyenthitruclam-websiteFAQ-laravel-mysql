<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    private const PER_PAGE = 15;

    // ===== DANH SÁCH =====
    public function index(Request $request)
    {
        $q  = trim((string) $request->query('q', ''));
        $dm = (string) $request->query('danh_muc', '');
        $tt = (string) $request->query('trang_thai', '');

        $faqs = Faq::with('danhMuc')
            ->when($q !== '', fn ($query) => $query->where('cau_hoi', 'like', '%' . $q . '%'))
            ->when($dm !== '', fn ($query) => $query->where('danh_muc_id', $dm))
            ->when($tt !== '', fn ($query) => $query->where('trang_thai', $tt))
            ->orderByRaw('COALESCE(ngay_cap_nhat, ngay_tao) DESC')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.faq.index', [
            'faqs'     => $faqs,
            'danhMucs' => DanhMuc::orderBy('danh_muc_id')->get(),
        ]);
    }

    // ===== FORM THÊM =====
    public function create()
    {
        return view('admin.faq.create', [
            'faq'      => new Faq(),
            'danhMucs' => DanhMuc::orderBy('danh_muc_id')->get(),
            'theTags'  => $this->theTags(),
        ]);
    }

    // ===== XEM CHI TIẾT =====
    public function show(Faq $faq)
    {
        $faq->load('danhMuc');

        // Thẻ và tệp đính kèm: nếu bảng chưa khớp thì bỏ qua, không làm sập trang
        try {
            $theTags = $faq->theTags;
        } catch (\Throwable $e) {
            Log::warning('Admin\FaqController@show theTags: ' . $e->getMessage());
            $theTags = collect();
        }

        try {
            $tepDinhKems = $faq->tapDinhKems;
        } catch (\Throwable $e) {
            Log::warning('Admin\FaqController@show tapDinhKems: ' . $e->getMessage());
            $tepDinhKems = collect();
        }

        $nguoiTao = $faq->nguoi_tao_id
            ? DB::table('users')->where('id', $faq->nguoi_tao_id)->value('name')
            : null;

        // Các lượt trả lời bên dưới FAQ (rỗng nếu chưa có bảng)
        $traLois = collect();
        if (Schema::hasTable('faq_tra_loi')) {
            $traLois = DB::table('faq_tra_loi as t')
                ->leftJoin('users as u', 'u.id', '=', 't.nguoi_tra_loi_id')
                ->where('t.faq_id', $faq->getKey())
                ->orderBy('t.ngay_tra_loi')
                ->orderBy('t.id')
                ->select('t.noi_dung', 't.ngay_tra_loi', 'u.name as nguoi')
                ->get();
        }

        return view('admin.faq.show', compact('faq', 'nguoiTao', 'traLois', 'theTags', 'tepDinhKems'));
    }

    // ===== LƯU FAQ MỚI =====
    public function store(Request $request)
    {
        $data = $this->validated($request);

        try {
            DB::transaction(function () use ($data, $request) {
                $faq = new Faq();
                $faq->forceFill([
                    'danh_muc_id'   => $data['danh_muc_id'],
                    'nguoi_tao_id'  => auth()->id(),
                    'cau_hoi'       => $data['cau_hoi'],
                    'cau_tra_loi'   => $data['cau_tra_loi'],
                    'slug'          => $this->taoSlugDuyNhat($data['cau_hoi']),
                    'trang_thai'    => $data['trang_thai'],
                    'ngay_tao'      => now(),
                    'ngay_cap_nhat' => now(),
                ])->save();

                $this->luuThe($faq, $request->input('tags', []));
                $this->luuTep($faq, $request);
            });
        } catch (\Throwable $e) {
            Log::error('Admin\FaqController@store: ' . $e->getMessage());
            $chiTiet = config('app.debug') ? ' Chi tiết: ' . Str::limit($e->getMessage(), 300) : '';
            return back()->withInput()->withErrors(['cau_hoi' => 'Không thêm được FAQ.' . $chiTiet]);
        }

        return redirect('/admin/faq')->with('ok', 'Đã thêm FAQ.');
    }

    // ===== FORM SỬA =====
    public function edit(Faq $faq)
    {
        return view('admin.faq.edit', [
            'faq'      => $faq,
            'danhMucs' => DanhMuc::orderBy('danh_muc_id')->get(),
            'theTags'  => $this->theTags(),
        ]);
    }

    // ===== CẬP NHẬT =====
    public function update(Request $request, Faq $faq)
    {
        $data = $this->validated($request);

        try {
            DB::transaction(function () use ($data, $request, $faq) {
                $row = [
                    'danh_muc_id'   => $data['danh_muc_id'],
                    'cau_hoi'       => $data['cau_hoi'],
                    'cau_tra_loi'   => $data['cau_tra_loi'],
                    'trang_thai'    => $data['trang_thai'],
                    'ngay_cap_nhat' => now(),
                ];

                // Giữ nguyên slug cũ (URL không đổi); chỉ tạo nếu đang rỗng
                if (empty($faq->slug)) {
                    $row['slug'] = $this->taoSlugDuyNhat($data['cau_hoi'], $faq->getKey());
                }

                $faq->forceFill($row)->save();

                $this->luuThe($faq, $request->input('tags', []));
                $this->luuTep($faq, $request);
            });
        } catch (\Throwable $e) {
            Log::error('Admin\FaqController@update: ' . $e->getMessage());
            $chiTiet = config('app.debug') ? ' Chi tiết: ' . Str::limit($e->getMessage(), 300) : '';
            return back()->withInput()->withErrors(['cau_hoi' => 'Không cập nhật được FAQ.' . $chiTiet]);
        }

        return redirect('/admin/faq')->with('ok', 'Đã cập nhật FAQ.');
    }

    // ===== XÓA =====
    public function destroy(Faq $faq)
    {
        try {
            DB::transaction(function () use ($faq) {
                $id = $faq->getKey();

                // Xóa tệp đính kèm (cả file thật lẫn dòng dữ liệu)
                if (method_exists($faq, 'tapDinhKems')) {
                    foreach ($faq->tapDinhKems as $tep) {
                        if (!empty($tep->duong_dan)) {
                            Storage::disk('public')->delete($tep->duong_dan);
                        }
                        $tep->delete();
                    }
                }

                // Xóa dữ liệu liên quan để không vướng khóa ngoại
                foreach (['faq_the_tag', 'faq_tra_loi'] as $bang) {
                    if (Schema::hasTable($bang) && Schema::hasColumn($bang, 'faq_id')) {
                        DB::table($bang)->where('faq_id', $id)->delete();
                    }
                }

                $faq->delete();
            });
        } catch (\Throwable $e) {
            Log::error('Admin\FaqController@destroy: ' . $e->getMessage());
            return back()->with('loi', 'Không xóa được FAQ. Xem storage/logs/laravel.log.');
        }

        return redirect('/admin/faq')->with('ok', 'Đã xóa FAQ.');
    }

    // ===== HÀM PHỤ =====
    private function theTags()
    {
        return Schema::hasTable('the_tag')
            ? DB::table('the_tag')->orderBy('the_id')->get()
            : collect();
    }

    private function luuThe(Faq $faq, $tags): void
    {
        if (!method_exists($faq, 'theTags')) {
            return;
        }

        // Bảng thẻ hoặc bảng nối không tồn tại thì bỏ qua, không làm hỏng việc lưu FAQ
        $relation = $faq->theTags();
        if (!Schema::hasTable($relation->getRelated()->getTable())
            || !Schema::hasTable($relation->getTable())) {
            return;
        }

        $ids = collect((array) $tags)
            ->filter(fn ($v) => ctype_digit((string) $v))
            ->map(fn ($v) => (int) $v)
            ->all();

        $relation->sync($ids);
    }

    private function luuTep(Faq $faq, Request $request): void
    {
        if (!$request->hasFile('files') || !method_exists($faq, 'tapDinhKems')) {
            return;
        }

        $relation = $faq->tapDinhKems();
        $bang     = $relation->getRelated()->getTable();

        foreach ($request->file('files') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $row = [
                'ten_tep'   => $file->getClientOriginalName(),
                'duong_dan' => $file->store('faq', 'public'),
            ];

            // Chỉ ghi các cột phụ nếu bảng có
            if (Schema::hasColumn($bang, 'kich_thuoc')) {
                $row['kich_thuoc'] = $file->getSize();
            }
            if (Schema::hasColumn($bang, 'loai_tep')) {
                $row['loai_tep'] = $file->getClientMimeType();
            }
            if (Schema::hasColumn($bang, 'ngay_tao')) {
                $row['ngay_tao'] = now();
            }

            $tep = $relation->getRelated()->newInstance();
            $tep->forceFill($row + [$relation->getForeignKeyName() => $faq->getKey()])->save();
        }
    }

    private function validated(Request $request): array
    {
        $dsTrangThai = array_keys(Faq::TRANG_THAI);

        return $request->validate([
            'danh_muc_id' => ['required', 'integer', 'exists:danh_muc,danh_muc_id'],
            'cau_hoi'     => ['required', 'string', 'max:500'],
            'cau_tra_loi' => ['required', 'string', 'max:50000'],
            'trang_thai'  => ['required', 'in:' . implode(',', $dsTrangThai)],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['integer'],
            'files'       => ['nullable', 'array'],
            'files.*'     => ['file', 'max:10240'],   // 10MB / tệp
        ], [
            'danh_muc_id.required' => 'Vui lòng chọn danh mục.',
            'danh_muc_id.exists'   => 'Danh mục không tồn tại.',
            'cau_hoi.required'     => 'Vui lòng nhập tiêu đề.',
            'cau_hoi.max'          => 'Tiêu đề tối đa 500 ký tự.',
            'cau_tra_loi.required' => 'Vui lòng nhập nội dung trả lời.',
            'trang_thai.required'  => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'        => 'Trạng thái không hợp lệ.',
            'files.*.max'          => 'Mỗi tệp tối đa 10MB.',
        ]);
    }

    /** Slug không dấu, không trùng (cột slug tối đa 200 ký tự). */
    private function taoSlugDuyNhat(?string $tieuDe, $boQuaId = null): string
    {
        $goc = Str::slug(Str::limit((string) $tieuDe, 150, ''));
        if ($goc === '') {
            $goc = 'faq';
        }

        $slug = $goc;
        $i = 2;
        while (
            DB::table('faq')
                ->where('slug', $slug)
                ->when($boQuaId, fn ($q) => $q->where('faq_id', '!=', $boQuaId))
                ->exists()
        ) {
            $slug = $goc . '-' . $i++;
        }

        return $slug;
    }
}