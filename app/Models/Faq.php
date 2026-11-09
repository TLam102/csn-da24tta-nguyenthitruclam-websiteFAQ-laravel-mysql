<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faq';               // đổi nếu migration đặt tên khác (vd: faqs)
    protected $primaryKey = 'faq_id';
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';     // tự động "Cập nhật ngày cập nhật"
    protected $fillable = ['danh_muc_id', 'nguoi_tao_id', 'cau_hoi', 'cau_tra_loi', 'slug', 'trang_thai', 'luot_xem'];

    const TRANG_THAI = [
        'nhap' => 'Nháp',
        'cho_duyet' => 'Chờ duyệt',
        'da_xuat_ban' => 'Đã xuất bản',
        'bi_tu_choi' => 'Bị từ chối',
    ];

    public static function badge(?string $s): string
    {
        return match ($s) {
            'da_xuat_ban' => 'success',
            'cho_duyet' => 'warning',
            'bi_tu_choi' => 'danger',
            default => 'secondary',
        };
    }

    public function danhMuc()   { return $this->belongsTo(DanhMuc::class, 'danh_muc_id', 'danh_muc_id'); }
    public function nguoiTao()  { return $this->belongsTo(User::class, 'nguoi_tao_id'); }
    public function tapDinhKems() { return $this->hasMany(TapDinhKem::class, 'faq_id', 'faq_id'); }
    public function theTags()
    {
        return $this->belongsToMany(TheTag::class, 'faq_the_tag', 'faq_id', 'the_id', 'faq_id', 'the_id');
    }
}
