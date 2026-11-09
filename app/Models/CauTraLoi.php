<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauTraLoi extends Model
{
    protected $table = 'cau_tra_loi';
    protected $primaryKey = 'tra_loi_id';
    const CREATED_AT = 'ngay_tra_loi';
    const UPDATED_AT = null;
    protected $fillable = ['cau_hoi_id', 'nguoi_tra_loi_id', 'noi_dung', 'trang_thai'];

    public function cauHoi()      { return $this->belongsTo(CauHoiSinhVien::class, 'cau_hoi_id', 'cau_hoi_id'); }
    public function nguoiTraLoi() { return $this->belongsTo(User::class, 'nguoi_tra_loi_id'); }
}
