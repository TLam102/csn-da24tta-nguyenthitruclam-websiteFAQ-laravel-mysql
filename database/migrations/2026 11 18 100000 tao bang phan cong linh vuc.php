<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('phan_cong_linh_vuc')) {
            Schema::create('phan_cong_linh_vuc', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('giang_vien_id');
                $t->unsignedBigInteger('danh_muc_id');
                $t->unsignedBigInteger('nguoi_phan_cong_id')->nullable();
                $t->timestamp('ngay_phan_cong')->nullable();
                $t->unique(['giang_vien_id', 'danh_muc_id']);
                $t->index('danh_muc_id');
            });
        }

        // Giữ dữ liệu cũ: giảng viên đã được sinh viên chọn ở danh mục nào thì phân công sẵn danh mục đó
        if (Schema::hasTable('cau_hoi')) {
            $cap = DB::table('cau_hoi')
                ->whereNotNull('giang_vien_id')->whereNotNull('danh_muc_id')
                ->distinct()->get(['giang_vien_id', 'danh_muc_id']);
            foreach ($cap as $r) {
                DB::table('phan_cong_linh_vuc')->insertOrIgnore([
                    'giang_vien_id' => $r->giang_vien_id,
                    'danh_muc_id'   => $r->danh_muc_id,
                    'ngay_phan_cong' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_cong_linh_vuc');
    }
};  