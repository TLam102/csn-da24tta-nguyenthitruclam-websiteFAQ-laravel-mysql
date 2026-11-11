<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gửi thông báo vào bảng `thong_bao`
 * (cột: id, user_id, tieu_de, noi_dung, lien_ket, da_doc, created_at).
 *
 * Cách dùng:
 *   ThongBao::gui($userId, 'Tiêu đề', 'Nội dung', '/faqs?tra_loi=39');
 *   ThongBao::gui($user, 'Tiêu đề', 'Nội dung');            // truyền model User
 *   ThongBao::gui([1, 2, 3], 'Tiêu đề', 'Nội dung');        // nhiều người nhận
 *   ThongBao::gui(['user_id' => 5, 'tieu_de' => '...', 'noi_dung' => '...', 'lien_ket' => '/...']);
 */
class ThongBao
{
    /** Cache việc kiểm tra cột updated_at */
    protected static ?bool $coUpdatedAt = null;

    public static function gui($nguoiNhan, $tieuDe = null, $noiDung = null, $lienKet = null): void
    {
        // Cho phép truyền 1 mảng duy nhất: ['user_id'=>..., 'tieu_de'=>..., ...]
        if (is_array($nguoiNhan) && array_key_exists('user_id', $nguoiNhan)) {
            $tieuDe  = $nguoiNhan['tieu_de']  ?? $tieuDe;
            $noiDung = $nguoiNhan['noi_dung'] ?? $noiDung;
            $lienKet = $nguoiNhan['lien_ket'] ?? $lienKet;
            $nguoiNhan = $nguoiNhan['user_id'];
        }

        $ids = static::layDanhSachId($nguoiNhan);
        if (empty($ids)) {
            return;
        }

        $lienKet = static::chuanHoaLienKet($lienKet);
        $now     = now();

        $rows = [];
        foreach ($ids as $id) {
            $row = [
                'user_id'    => $id,
                'tieu_de'    => (string) $tieuDe,
                'noi_dung'   => $noiDung,
                'lien_ket'   => $lienKet,
                'da_doc'     => false,
                'created_at' => $now,
            ];

            if (static::coCotUpdatedAt()) {
                $row['updated_at'] = $now;
            }

            $rows[] = $row;
        }

        DB::table('thong_bao')->insert($rows);
    }

    /** Chấp nhận: id, model User, mảng/collection id hoặc model */
    protected static function layDanhSachId($nguoiNhan): array
    {
        if ($nguoiNhan instanceof Collection) {
            $nguoiNhan = $nguoiNhan->all();
        }

        if (!is_array($nguoiNhan)) {
            $nguoiNhan = [$nguoiNhan];
        }

        $ids = [];
        foreach ($nguoiNhan as $n) {
            $id = is_object($n) ? ($n->id ?? null) : $n;
            if ($id !== null && $id !== '') {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * ThongBaoController::open() chỉ chấp nhận đường dẫn nội bộ bắt đầu bằng "/".
     * Nếu truyền URL đầy đủ (route(...)) thì tự cắt về dạng /path?query.
     */
    protected static function chuanHoaLienKet($lienKet): ?string
    {
        $lienKet = trim((string) $lienKet);
        if ($lienKet === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $lienKet)) {
            $p = parse_url($lienKet);
            $lienKet = ($p['path'] ?? '/')
                . (isset($p['query']) ? '?' . $p['query'] : '')
                . (isset($p['fragment']) ? '#' . $p['fragment'] : '');
        }

        if (!str_starts_with($lienKet, '/')) {
            $lienKet = '/' . $lienKet;
        }

        // Chặn "//" (open redirect)
        return str_starts_with($lienKet, '//') ? '/' : $lienKet;
    }

    protected static function coCotUpdatedAt(): bool
    {
        return static::$coUpdatedAt ??= Schema::hasColumn('thong_bao', 'updated_at');
    }
}