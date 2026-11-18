<?php

namespace App\Support;

use App\Http\Middleware\QuanTriVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Quyền trả lời theo lĩnh vực (danh mục):
 *  - Quản trị viên: trả lời mọi lĩnh vực.
 *  - Giảng viên: chỉ trả lời lĩnh vực đã được phân công (bảng phan_cong_linh_vuc).
 *  - Sinh viên: không được trả lời.
 */
class PhuTrach
{
    private static array $cache = [];

    public static function laQuanTri($user): bool
    {
        return $user && in_array($user->vai_tro ?? '', QuanTriVien::VAI_TRO, true);
    }

    /** Danh sách danh_muc_id mà giảng viên phụ trách */
    public static function danhMucIds($user): array
    {
        if (!$user || ($user->vai_tro ?? '') !== 'giang_vien' || !Schema::hasTable('phan_cong_linh_vuc')) {
            return [];
        }
        return self::$cache[$user->id] ??= DB::table('phan_cong_linh_vuc')
            ->where('giang_vien_id', $user->id)
            ->pluck('danh_muc_id')->map(fn ($v) => (int) $v)->all();
    }

    public static function coQuyen($user, $danhMucId): bool
    {
        if (self::laQuanTri($user)) {
            return true;
        }
        if ($danhMucId === null || $danhMucId === '') {
            return false;
        }
        return in_array((int) $danhMucId, self::danhMucIds($user), true);
    }
}