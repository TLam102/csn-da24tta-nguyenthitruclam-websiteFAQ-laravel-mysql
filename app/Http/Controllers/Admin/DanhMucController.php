<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DanhMucController extends Controller
{
    // ===== CHỈ CẦN SỬA Ở ĐÂY NẾU TÊN CỘT TRONG BẢNG danh_muc KHÁC =====
    private const COT_TEN   = 'ten_danh_muc';
    private const COT_MO_TA = 'mo_ta';

    public function index()
    {
        return view('admin.danhmuc.index', [
            'danhMucs' => DanhMuc::orderBy('danh_muc_id')->paginate(15),
        ]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);

        (new DanhMuc())->forceFill($data)->save();

        return redirect()->route('admin.danh-muc.index')->with('ok', 'Đã thêm danh mục.');
    }

    public function update(Request $r, $id)
    {
        $danhMuc = DanhMuc::findOrFail($id);
        $data = $this->validated($r, $danhMuc->getKey());

        $danhMuc->forceFill($data)->save();

        return redirect()->route('admin.danh-muc.index')->with('ok', 'Đã cập nhật danh mục.');
    }

    public function destroy($id)
    {
        $danhMuc = DanhMuc::findOrFail($id);

        // Không xóa danh mục đang có FAQ để tránh mất dữ liệu / lỗi khóa ngoại
        if (Faq::where('danh_muc_id', $danhMuc->getKey())->exists()) {
            return back()->with('loi', 'Không thể xóa: danh mục này đang có FAQ. Hãy chuyển các FAQ sang danh mục khác trước.');
        }

        $danhMuc->delete();

        return redirect()->route('admin.danh-muc.index')->with('ok', 'Đã xóa danh mục.');
    }

    private function validated(Request $r, $id = null): array
    {
        $r->validate([
            'ten' => [
                'required', 'string', 'max:255',
                Rule::unique('danh_muc', self::COT_TEN)->ignore($id, 'danh_muc_id'),
            ],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
        ], [
            'ten.required' => 'Vui lòng nhập tên danh mục.',
            'ten.unique'   => 'Tên danh mục này đã tồn tại.',
            'ten.max'      => 'Tên danh mục tối đa 255 ký tự.',
        ]);

        return [
            self::COT_TEN   => $r->input('ten'),
            self::COT_MO_TA => $r->input('mo_ta') ?? '',
        ];
    }
}