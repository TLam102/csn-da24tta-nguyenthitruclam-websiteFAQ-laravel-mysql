<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPhanCongController extends Controller
{
    public function index()
    {
        abort_unless(Schema::hasTable('phan_cong_linh_vuc'), 503, 'Chưa có bảng phan_cong_linh_vuc. Hãy chạy: php artisan migrate');

        $giangViens = DB::table('users')->where('vai_tro', 'giang_vien')->orderBy('name')->get(['id', 'name', 'email']);
        $danhMucs   = DB::table('danh_muc')->where('trang_thai', 1)->orderBy('danh_muc_id')->get(['danh_muc_id', 'ten_danh_muc']);
        $daCo       = DB::table('phan_cong_linh_vuc')->get()->groupBy('giang_vien_id')
                        ->map(fn ($g) => $g->pluck('danh_muc_id')->map(fn ($v) => (int) $v)->all());

        return view('admin.phan-cong.index', compact('giangViens', 'danhMucs', 'daCo'));
    }

    public function update(Request $request, int $id)
    {
        $gv = DB::table('users')->where('id', $id)->where('vai_tro', 'giang_vien')->first();
        abort_if(!$gv, 404);

        $data = $request->validate([
            'danh_muc'   => ['nullable', 'array'],
            'danh_muc.*' => ['integer', 'exists:danh_muc,danh_muc_id'],
        ], [
            'danh_muc.*.exists' => 'Lĩnh vực không tồn tại.',
        ]);

        DB::transaction(function () use ($id, $data) {
            DB::table('phan_cong_linh_vuc')->where('giang_vien_id', $id)->delete();
            foreach (array_unique($data['danh_muc'] ?? []) as $dm) {
                DB::table('phan_cong_linh_vuc')->insert([
                    'giang_vien_id'      => $id,
                    'danh_muc_id'        => $dm,
                    'nguoi_phan_cong_id' => auth()->id(),
                    'ngay_phan_cong'     => now(),
                ]);
            }
        });

        return back()->with('ok', 'Đã cập nhật lĩnh vực phụ trách của ' . $gv->name . '.');
    }
}