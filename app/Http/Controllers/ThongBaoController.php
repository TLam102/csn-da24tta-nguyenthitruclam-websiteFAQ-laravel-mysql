<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongBaoController extends Controller
{
    public function index(Request $request)
    {
        $tab = (string) $request->query('tab', 'tat-ca');
        if (!in_array($tab, ['tat-ca', 'chua-doc', 'da-doc'], true)) {
            $tab = 'tat-ca';
        }

        $goc = fn () => DB::table('thong_bao')->where('user_id', auth()->id());

        $stats = [
            'tong' => $goc()->count(),
            'chua' => $goc()->where('da_doc', false)->count(),
            'da'   => $goc()->where('da_doc', true)->count(),
        ];

        $items = $goc()
            ->when($tab === 'chua-doc', fn ($q) => $q->where('da_doc', false))
            ->when($tab === 'da-doc', fn ($q) => $q->where('da_doc', true))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('thongbao.index', compact('items', 'tab', 'stats'));
    }

    /** Bấm vào thông báo: đánh dấu đã đọc rồi chuyển tới nội dung */
    public function open($id)
    {
        $tb = DB::table('thong_bao')->where('id', $id)->where('user_id', auth()->id())->first();
        abort_if(!$tb, 404);

        DB::table('thong_bao')->where('id', $id)->update(['da_doc' => true]);

        $lk    = (string) ($tb->lien_ket ?? '');
        $noiBo = $lk !== '' && str_starts_with($lk, '/') && !str_starts_with($lk, '//');

        return redirect($noiBo ? $lk : route('thongbao.index'));
    }

    public function readAll()
    {
        DB::table('thong_bao')->where('user_id', auth()->id())->where('da_doc', false)->update(['da_doc' => true]);

        return back();
    }

    /** Bỏ qua (xóa) một thông báo của chính mình */
    public function destroy(Request $request, $id)
    {
        DB::table('thong_bao')->where('id', $id)->where('user_id', auth()->id())->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}