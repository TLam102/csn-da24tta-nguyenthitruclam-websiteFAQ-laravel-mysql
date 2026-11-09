<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lượt trả lời của giảng viên / quản trị viên cho từng FAQ (hiển thị ở trang Danh sách FAQ).
 * FAQ có từ 1 lượt trả lời trở lên sẽ hiện trạng thái "Đã trả lời".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('faq_tra_loi')) {
            Schema::create('faq_tra_loi', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('faq_id')->index();
                $t->unsignedBigInteger('nguoi_tra_loi_id')->index();
                $t->longText('noi_dung');
                $t->timestamp('ngay_tra_loi')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_tra_loi');
    }
};