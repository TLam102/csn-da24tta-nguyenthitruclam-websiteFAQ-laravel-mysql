<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHoiSinhVien extends Model
{
    protected $table = 'cau_hoi';
    protected $primaryKey = 'cau_hoi_id';
    public $timestamps = false;

    protected $fillable = [
        'nguoi_gui_id', 'nguoi_duyet_id', 'danh_muc_id', 'giang_vien_id',
        'tieu_de', 'noi_dung', 'trang_thai', 'ly_do_tu_choi',
        'ngay_gui', 'ngay_duyet', 'giang_vien_da_xem', 'giang_vien_xem_luc',
    ];

    protected $casts = [
        'ngay_gui' => 'datetime',
        'ngay_duyet' => 'datetime',
        'giang_vien_xem_luc' => 'datetime',
    ];

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    public function nguoiGui()
    {
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    public function cauTraLois()
    {
        return $this->hasMany(CauTraLoi::class, 'cau_hoi_id', 'cau_hoi_id');
    }
}