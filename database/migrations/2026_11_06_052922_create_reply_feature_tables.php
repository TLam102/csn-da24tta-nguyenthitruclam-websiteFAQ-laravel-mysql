<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Câu trả lời mẫu
        Schema::create('mau_tra_loi', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable()->index(); // null = mẫu chung
            $t->string('tieu_de', 120);
            $t->text('noi_dung');
            $t->timestamp('created_at')->useCurrent();
        });

        // File đính kèm của câu trả lời
        Schema::create('cau_tra_loi_tep', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tra_loi_id')->index();
            $t->string('ten_goc');
            $t->string('duong_dan');
            $t->unsignedBigInteger('kich_thuoc')->default(0);
            $t->timestamp('created_at')->useCurrent();
        });

        // File đính kèm của câu hỏi (hiển thị nếu có)
        Schema::create('cau_hoi_tep', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('cau_hoi_id')->index();
            $t->string('ten_goc');
            $t->string('duong_dan');
            $t->unsignedBigInteger('kich_thuoc')->default(0);
            $t->timestamp('created_at')->useCurrent();
        });

        // Thông báo gửi cho người dùng
        Schema::create('thong_bao', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->string('tieu_de');
            $t->string('noi_dung', 500)->nullable();
            $t->string('lien_ket')->nullable();
            $t->boolean('da_doc')->default(false);
            $t->timestamp('created_at')->useCurrent();
        });

        // Mẫu có sẵn
        DB::table('mau_tra_loi')->insert([
            ['user_id' => null, 'tieu_de' => 'Xác nhận đã nhận câu hỏi',
             'noi_dung' => '<p>Chào em,</p><p>Cảm ơn em đã gửi câu hỏi. Khoa đã tiếp nhận và sẽ phản hồi chi tiết trong thời gian sớm nhất.</p><p>Trân trọng.</p>'],
            ['user_id' => null, 'tieu_de' => 'Liên hệ phòng Đào tạo',
             'noi_dung' => '<p>Chào em,</p><p>Vấn đề này thuộc thẩm quyền của Phòng Đào tạo. Em vui lòng liên hệ trực tiếp Phòng Đào tạo để được hướng dẫn cụ thể.</p><p>Trân trọng.</p>'],
            ['user_id' => null, 'tieu_de' => 'Đăng ký học phần',
             'noi_dung' => '<p>Chào em,</p><p>Em thực hiện theo các bước sau:</p><ol><li>Đăng nhập cổng thông tin sinh viên.</li><li>Chọn mục <b>Đăng ký học phần</b>.</li><li>Chọn học phần và lớp phù hợp, sau đó xác nhận.</li></ol><p>Nếu gặp lỗi, em gửi lại ảnh chụp màn hình để thầy/cô hỗ trợ.</p>'],
            ['user_id' => null, 'tieu_de' => 'Cần bổ sung thông tin',
             'noi_dung' => '<p>Chào em,</p><p>Để hỗ trợ chính xác, em vui lòng bổ sung các thông tin sau:</p><ul><li>Mã số sinh viên và lớp</li><li>Mô tả cụ thể vấn đề đang gặp</li><li>Ảnh chụp minh chứng (nếu có)</li></ul>'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
        Schema::dropIfExists('cau_hoi_tep');
        Schema::dropIfExists('cau_tra_loi_tep');
        Schema::dropIfExists('mau_tra_loi');
    }
};