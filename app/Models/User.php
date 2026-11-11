<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const VAI_TRO_QUAN_TRI   = 'quan_tri';
    const VAI_TRO_SINH_VIEN  = 'sinh_vien';
    const VAI_TRO_GIANG_VIEN = 'giang_vien';

    protected $fillable = ['name', 'email', 'password', 'vai_tro', 'trang_thai', 'anh_dai_dien'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    /** auth()->user()->role đọc cột vai_tro */
    public function getRoleAttribute(): ?string
    {
        return $this->attributes['vai_tro'] ?? null;
    }

    /** auth()->user()->avatar_url: đường dẫn ảnh đại diện hoặc null */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->anh_dai_dien) {
            return null;
        }

        // Ảnh giảng viên có sẵn trong code: public/giang-vien/...
        if (str_starts_with($this->anh_dai_dien, 'giang-vien/')) {
            return asset($this->anh_dai_dien);
        }

        // Ảnh người dùng tải lên: storage/app/public/avatars/...
        return asset('storage/' . $this->anh_dai_dien);
    }

    public function isQuanTri(): bool
    {
        return $this->vai_tro === self::VAI_TRO_QUAN_TRI;
    }

    public function isGiangVien(): bool
    {
        return $this->vai_tro === self::VAI_TRO_GIANG_VIEN;
    }

    public function isSinhVien(): bool
    {
        return $this->vai_tro === self::VAI_TRO_SINH_VIEN;
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoiSinhVien::class, 'nguoi_gui_id');
    }

    public function cauHoiGuiDen()
    {
        return $this->hasMany(CauHoiSinhVien::class, 'giang_vien_id');
    }
}