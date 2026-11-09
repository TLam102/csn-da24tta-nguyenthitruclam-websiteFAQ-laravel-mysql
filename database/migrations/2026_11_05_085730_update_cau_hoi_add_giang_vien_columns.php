<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->unsignedBigInteger('danh_muc_id')->nullable();
            $table->unsignedBigInteger('giang_vien_id')->nullable();
            $table->boolean('giang_vien_da_xem')->default(false);
            $table->timestamp('giang_vien_xem_luc')->nullable();
            $table->index('giang_vien_id');
        });
    }

    public function down(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->dropIndex(['giang_vien_id']);
            $table->dropColumn(['danh_muc_id', 'giang_vien_id', 'giang_vien_da_xem', 'giang_vien_xem_luc']);
        });
    }
};