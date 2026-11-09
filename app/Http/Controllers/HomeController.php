<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    // ===== TÊN BẢNG / CỘT THẬT TRONG CSDL faq_it =====
    private const TBL_FAQ      = 'faq';
    private const TBL_DANHMUC  = 'danh_muc';          // có dấu gạch dưới
    private const TRANG_THAI_XUAT_BAN = 'da_xuat_ban';

    public function index()
    {
        $categories  = collect();
        $latestFaqs  = collect();
        $popularFaqs = collect();

        try {
            // Danh mục đang hiển thị + số FAQ đã xuất bản trong mỗi danh mục
            $categories = DB::table(self::TBL_DANHMUC . ' as d')
                ->leftJoin(self::TBL_FAQ . ' as f', function ($join) {
                    $join->on('f.danh_muc_id', '=', 'd.danh_muc_id')
                         ->where('f.trang_thai', self::TRANG_THAI_XUAT_BAN);
                })
                ->where('d.trang_thai', 1)
                ->groupBy('d.danh_muc_id', 'd.ten_danh_muc', 'd.mo_ta', 'd.slug')
                ->orderBy('d.danh_muc_id')
                ->select('d.danh_muc_id', 'd.ten_danh_muc', 'd.mo_ta', 'd.slug',
                         DB::raw('COUNT(f.faq_id) as faqs_count'))
                ->get();

            $base = DB::table(self::TBL_FAQ)->where('trang_thai', self::TRANG_THAI_XUAT_BAN);

            $latestFaqs  = (clone $base)->orderByRaw('COALESCE(ngay_cap_nhat, ngay_tao) DESC')->limit(6)->get();
            $popularFaqs = (clone $base)->orderByDesc('luot_xem')->limit(6)->get();
        } catch (\Throwable $e) {
            Log::error('HomeController: ' . $e->getMessage());
        }

        return view('home', compact('categories', 'latestFaqs', 'popularFaqs'));
    }
}