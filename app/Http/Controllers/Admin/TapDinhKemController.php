<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TapDinhKem;
use Illuminate\Support\Facades\Storage;

class TapDinhKemController extends Controller
{
    public function index()
    {
        return view('admin.tapdinhkem.index', [
            'teps' => TapDinhKem::with(['faq', 'nguoiTao'])->orderByDesc('ngay_tao')->paginate(15),
        ]);
    }

    public function destroy(TapDinhKem $tep)
    {
        Storage::disk('public')->delete($tep->duong_dan);
        $tep->delete();
        return back()->with('ok', 'Đã xóa tệp đính kèm.');
    }
}
