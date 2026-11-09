<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TapDinhKem extends Model
{
    protected $table = 'tap_dinh_kem';
    protected $primaryKey = 'tep_id';
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = null;
    protected $fillable = ['faq_id', 'nguoi_tao_id', 'ten_tep', 'duong_dan', 'loai_tep', 'dung_luong'];

    public function faq()      { return $this->belongsTo(Faq::class, 'faq_id', 'faq_id'); }
    public function nguoiTao() { return $this->belongsTo(User::class, 'nguoi_tao_id'); }
    public function url(): string { return asset('storage/' . $this->duong_dan); }
}
