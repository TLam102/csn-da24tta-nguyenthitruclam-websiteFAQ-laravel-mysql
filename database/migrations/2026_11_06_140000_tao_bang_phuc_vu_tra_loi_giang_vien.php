<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo các bảng phụ cho trang "Trả lời câu hỏi" của giảng viên.
 * Bảng nào đã có thì bỏ qua (an toàn khi chạy lại).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tệp đính kèm của câu hỏi (do sinh viên gửi)
        if (!Schema::hasTable('cau_hoi_tep')) {
            Schema::create('cau_hoi_tep', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('cau_hoi_id')->index();
                $t->string('ten_goc');
                $t->string('duong_dan');
                $t->unsignedBigInteger('kich_thuoc')->default(0);
                $t->timestamp('created_at')->nullable();
            });
        }

        // Tệp đính kèm của câu trả lời (do giảng viên gửi)
        if (!Schema::hasTable('cau_tra_loi_tep')) {
            Schema::create('cau_tra_loi_tep', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('tra_loi_id')->index();
                $t->string('ten_goc');
                $t->string('duong_dan');
                $t->unsignedBigInteger('kich_thuoc')->default(0);
                $t->timestamp('created_at')->nullable();
            });
        }

        // Câu trả lời mẫu (user_id NULL = mẫu chung)
        if (!Schema::hasTable('mau_tra_loi')) {
            Schema::create('mau_tra_loi', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('tieu_de', 120);
                $t->text('noi_dung');
                $t->timestamp('created_at')->nullable();
            });

            DB::table('mau_tra_loi')->insert([
                ['user_id' => null, 'created_at' => now(),
                 'tieu_de'  => 'Đã tiếp nhận, sẽ phản hồi',
                 'noi_dung' => '<p>Chào em, thầy/cô đã nhận được câu hỏi của em và sẽ phản hồi chi tiết trong thời gian sớm nhất.</p>'],
                ['user_id' => null, 'created_at' => now(),
                 'tieu_de'  => 'Hướng dẫn liên hệ Văn phòng Khoa',
                 'noi_dung' => '<p>Chào em, vấn đề này em vui lòng:</p><ul><li>Mang theo thẻ sinh viên và giấy tờ liên quan.</li><li>Liên hệ trực tiếp Văn phòng Khoa trong giờ hành chính để được hỗ trợ.</li></ul>'],
                ['user_id' => null, 'created_at' => now(),
                 'tieu_de'  => 'Xem quy chế đào tạo',
                 'noi_dung' => '<p>Chào em, nội dung này được quy định trong <b>Quy chế đào tạo</b> hiện hành của Trường. Em vui lòng đọc kỹ quy chế, nếu còn thắc mắc hãy gửi thêm câu hỏi để thầy/cô hỗ trợ.</p>'],
            ]);
        }

        // Thông báo cho người dùng
        if (!Schema::hasTable('thong_bao')) {
            Schema::create('thong_bao', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('user_id')->index();
                $t->string('tieu_de');
                $t->text('noi_dung')->nullable();
                $t->string('lien_ket')->nullable();
                $t->boolean('da_doc')->default(false);
                $t->timestamp('created_at')->nullable();
            });
        }

        // Cho phép cột trạng thái câu hỏi nhận thêm giá trị 'da_tra_loi' (đổi ENUM -> VARCHAR)
        if (Schema::hasTable('cau_hoi') && Schema::hasColumn('cau_hoi', 'trang_thai')) {
            try {
                DB::statement("ALTER TABLE cau_hoi MODIFY trang_thai VARCHAR(30) NOT NULL DEFAULT 'cho_duyet'");
            } catch (\Throwable $e) {
                // bỏ qua: controller vẫn đăng được câu trả lời nếu không đổi được cột
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hoi_tep');
        Schema::dropIfExists('cau_tra_loi_tep');
        Schema::dropIfExists('mau_tra_loi');
        Schema::dropIfExists('thong_bao');
    }
};