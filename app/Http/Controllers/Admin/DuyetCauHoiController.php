<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ThongBao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Duyệt nhanh: bấm "Duyệt" sẽ đăng câu hỏi của sinh viên lên danh sách FAQ,
 * gửi thông báo cho người gửi và cho giảng viên nhận câu hỏi.
 * Câu trả lời của giảng viên (bảng cau_tra_loi) tự hiện ở trang chi tiết FAQ.
 */
class DuyetCauHoiController extends Controller
{
    private const TBL_CAUHOI = 'cau_hoi';
    private const PK         = 'cau_hoi_id';
    private const TBL_FAQ    = 'faq';
    private const XUAT_BAN   = 'da_xuat_ban';

    public function duyet(int $id)
    {
        $ch = DB::table(self::TBL_CAUHOI)->where(self::PK, $id)->first();
        abort_if(!$ch, 404);

        // Đã từ chối thì không duyệt
        if ($ch->trang_thai === 'bi_tu_choi') {
            return redirect('/admin/cau-hoi')->with('loi', 'Câu hỏi này đã bị từ chối trước đó.');
        }

        // Đã đăng lên FAQ rồi thì không đăng trùng
        if (DB::table(self::TBL_FAQ)->where('cau_hoi_id', $id)->exists()) {
            return redirect('/admin/cau-hoi')->with('loi', 'Câu hỏi này đã được đăng lên FAQ trước đó.');
        }

        try {
            $faqId = DB::transaction(function () use ($ch, $id) {
                // faq.danh_muc_id là NOT NULL: câu hỏi chưa có danh mục thì dùng "khac" hoặc danh mục đầu tiên
                $danhMucId = $ch->danh_muc_id
                    ?? DB::table('danh_muc')->where('slug', 'khac')->value('danh_muc_id')
                    ?? DB::table('danh_muc')->orderBy('danh_muc_id')->value('danh_muc_id');

                $faqId = DB::table(self::TBL_FAQ)->insertGetId([
                    'danh_muc_id'   => $danhMucId,
                    'nguoi_tao_id'  => auth()->id(),
                    'cau_hoi_id'    => $id,                  // liên kết FAQ với câu hỏi gốc
                    'cau_hoi'       => $ch->tieu_de,
                    'cau_tra_loi'   => '',                   // đáp án nằm ở phần "Trao đổi" bên dưới
                    'slug'          => $this->taoSlugDuyNhat($ch->tieu_de),
                    'trang_thai'    => self::XUAT_BAN,
                    'ngay_tao'      => now(),
                    'ngay_cap_nhat' => now(),
                ], 'faq_id');

                // Đưa câu hỏi ra khỏi danh sách chờ duyệt
                DB::table(self::TBL_CAUHOI)->where(self::PK, $id)->update(['trang_thai' => 'da_tra_loi']);

                return $faqId;
            });
        } catch (\Throwable $e) {
            Log::error('Duyệt câu hỏi thất bại: ' . $e->getMessage());
            $chiTiet = config('app.debug') ? ' Chi tiết: ' . Str::limit($e->getMessage(), 300) : '';
            return redirect('/admin/cau-hoi')->with('loi', 'Không duyệt được câu hỏi. Xem storage/logs/laravel.log.' . $chiTiet);
        }

        // 1) Thông báo cho người gửi: bấm vào sẽ chuyển đến danh sách FAQ
        $lienKet = Route::has('faqs.index') ? route('faqs.index', [], false) : '/faqs';

        ThongBao::gui(
            $ch->nguoi_gui_id ?? null,
            'Câu hỏi của bạn đã được duyệt',
            'Câu hỏi "' . Str::limit($ch->tieu_de, 100) . '" đã được duyệt và đăng lên danh sách FAQ. Nhấn để xem.',
            $lienKet
        );

        // 2) Thông báo cho giảng viên nhận câu hỏi: bấm vào sẽ mở trang chi tiết FAQ
        if (!empty($ch->giang_vien_id) && (int) $ch->giang_vien_id !== (int) ($ch->nguoi_gui_id ?? 0)) {
            ThongBao::gui(
                $ch->giang_vien_id,
                'Câu hỏi gửi đến bạn đã được đăng lên FAQ',
                'Câu hỏi "' . Str::limit($ch->tieu_de, 100) . '" đã được duyệt và đăng công khai. Câu trả lời của bạn sẽ hiện ở trang chi tiết.',
                route('faqs.show', $faqId, false)
            );
        }

        return redirect('/admin/cau-hoi')->with('ok', 'Đã duyệt, đăng câu hỏi lên danh sách FAQ và gửi thông báo.');
    }

    /** Slug không dấu, không trùng trong bảng faq (cột slug tối đa 200 ký tự). */
    private function taoSlugDuyNhat(?string $tieuDe): string
    {
        $goc = Str::slug(Str::limit((string) $tieuDe, 150, ''));
        if ($goc === '') {
            $goc = 'faq';
        }

        $slug = $goc;
        $i = 2;
        while (DB::table(self::TBL_FAQ)->where('slug', $slug)->exists()) {
            $slug = $goc . '-' . $i++;
        }

        return $slug;
    }
}