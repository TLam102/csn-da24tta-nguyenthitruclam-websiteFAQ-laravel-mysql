<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_muc', function (Blueprint $table) {
            $table->id('danh_muc_id');
            $table->string('ten_danh_muc', 100);
            $table->text('mo_ta')->nullable();
            $table->string('slug', 150)->unique();
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamp('ngay_tao')->useCurrent();
        });

        Schema::create('cau_hoi', function (Blueprint $table) {
            $table->id('cau_hoi_id');
            $table->foreignId('nguoi_gui_id')->constrained('users');
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('users');
            $table->string('tieu_de', 255);
            $table->text('noi_dung');
            $table->enum('trang_thai', ['cho_duyet', 'da_tra_loi', 'bi_tu_choi'])->default('cho_duyet');
            $table->text('ly_do_tu_choi')->nullable();
            $table->timestamp('ngay_gui')->useCurrent();
            $table->timestamp('ngay_duyet')->nullable();
        });

        Schema::create('cau_tra_loi', function (Blueprint $table) {
            $table->id('tra_loi_id');
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi', 'cau_hoi_id')->cascadeOnDelete();
            $table->foreignId('nguoi_tra_loi_id')->constrained('users');
            $table->text('noi_dung');
            $table->timestamp('ngay_tra_loi')->useCurrent();
        });

        Schema::create('faq', function (Blueprint $table) {
            $table->id('faq_id');
            $table->foreignId('danh_muc_id')->constrained('danh_muc', 'danh_muc_id');
            $table->foreignId('nguoi_tao_id')->constrained('users');
            $table->foreignId('cau_hoi_id')->nullable()->constrained('cau_hoi', 'cau_hoi_id')->nullOnDelete();
            $table->string('cau_hoi', 500);
            $table->text('cau_tra_loi');
            $table->string('slug', 200)->unique();
            $table->enum('trang_thai', ['nhap', 'da_xuat_ban'])->default('nhap');
            $table->unsignedInteger('luot_xem')->default(0);
            $table->timestamp('ngay_tao')->useCurrent();
            $table->timestamp('ngay_cap_nhat')->nullable();
        });

        Schema::create('tap_dinh_kem', function (Blueprint $table) {
            $table->id('tep_id');
            $table->foreignId('faq_id')->constrained('faq', 'faq_id')->cascadeOnDelete();
            $table->foreignId('nguoi_tao_id')->constrained('users');
            $table->string('ten_tep', 255);
            $table->string('duong_dan', 500);
            $table->string('loai_tep', 100)->nullable();
            $table->unsignedBigInteger('dung_luong')->default(0);
            $table->timestamp('ngay_tao')->useCurrent();
        });

        Schema::create('the_tag', function (Blueprint $table) {
            $table->id('the_id');
            $table->string('ten_the', 80)->unique();
            $table->string('slug', 100)->unique();
        });

        Schema::create('faq_the_tag', function (Blueprint $table) {
            $table->foreignId('faq_id')->constrained('faq', 'faq_id')->cascadeOnDelete();
            $table->foreignId('the_id')->constrained('the_tag', 'the_id')->cascadeOnDelete();
            $table->primary(['faq_id', 'the_id']);
        });

        Schema::create('tu_khoa_tim_kiem', function (Blueprint $table) {
            $table->id('tu_khoa_id');
            $table->foreignId('nguoi_dung_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tu_khoa', 255);
            $table->timestamp('ngay_tim')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tu_khoa_tim_kiem');
        Schema::dropIfExists('faq_the_tag');
        Schema::dropIfExists('the_tag');
        Schema::dropIfExists('tap_dinh_kem');
        Schema::dropIfExists('faq');
        Schema::dropIfExists('cau_tra_loi');
        Schema::dropIfExists('cau_hoi');
        Schema::dropIfExists('danh_muc');
    }
};