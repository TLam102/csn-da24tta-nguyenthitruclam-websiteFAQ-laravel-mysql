<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danh_muc';          // đổi nếu migration đặt tên khác (vd: danh_mucs)
    protected $primaryKey = 'danh_muc_id';
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = null;
    protected $fillable = ['tendanhmuc', 'mo_ta', 'slug', 'trang_thai'];

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'danh_muc_id', 'danh_muc_id');
    }
}
