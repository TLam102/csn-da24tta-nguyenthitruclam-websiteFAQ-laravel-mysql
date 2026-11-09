<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DuLieuMauSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            ['name' => 'Quản trị viên', 'email' => 'admin@faq.test', 'password' => Hash::make('admin123'), 'vai_tro' => 'quan_tri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sinh viên A', 'email' => 'sinhvien@faq.test', 'password' => Hash::make('123456'), 'vai_tro' => 'sinh_vien', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Giảng viên B', 'email' => 'giangvien@faq.test', 'password' => Hash::make('123456'), 'vai_tro' => 'giang_vien', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('danh_muc')->insert([
            ['ten_danh_muc' => 'Học vụ', 'mo_ta' => 'Đăng ký học phần, bảng điểm, quy chế', 'slug' => 'hoc-vu'],
            ['ten_danh_muc' => 'Đồ án - Thực tập', 'mo_ta' => 'Quy định đồ án, thực tập doanh nghiệp', 'slug' => 'do-an-thuc-tap'],
            ['ten_danh_muc' => 'Cơ sở vật chất', 'mo_ta' => 'Phòng máy, phòng học, thiết bị', 'slug' => 'co-so-vat-chat'],
            ['ten_danh_muc' => 'Tài khoản và hệ thống', 'mo_ta' => 'Tài khoản sinh viên, Wi-Fi, email', 'slug' => 'tai-khoan-he-thong'],
        ]);

        DB::table('the_tag')->insert([
            ['ten_the' => 'đăng ký học phần', 'slug' => 'dang-ky-hoc-phan'],
            ['ten_the' => 'đồ án', 'slug' => 'do-an'],
            ['ten_the' => 'phòng máy', 'slug' => 'phong-may'],
        ]);

        DB::table('faq')->insert([
            ['danh_muc_id' => 1, 'nguoi_tao_id' => 1, 'cau_hoi' => 'Làm thế nào để đăng ký học phần?', 'cau_tra_loi' => 'Nội dung mẫu: đăng nhập cổng thông tin sinh viên và chọn học phần trong thời gian đăng ký.', 'slug' => 'dang-ky-hoc-phan', 'trang_thai' => 'da_xuat_ban', 'luot_xem' => 0],
            ['danh_muc_id' => 2, 'nguoi_tao_id' => 1, 'cau_hoi' => 'Điều kiện để làm đồ án tốt nghiệp là gì?', 'cau_tra_loi' => 'Nội dung mẫu: sinh viên cần đủ số tín chỉ theo quy định của khoa.', 'slug' => 'dieu-kien-lam-do-an', 'trang_thai' => 'da_xuat_ban', 'luot_xem' => 0],
            ['danh_muc_id' => 3, 'nguoi_tao_id' => 1, 'cau_hoi' => 'Mượn phòng máy ngoài giờ học như thế nào?', 'cau_tra_loi' => 'Nội dung mẫu: liên hệ văn phòng khoa để đăng ký trước.', 'slug' => 'muon-phong-may', 'trang_thai' => 'da_xuat_ban', 'luot_xem' => 0],
            ['danh_muc_id' => 4, 'nguoi_tao_id' => 1, 'cau_hoi' => 'Quên mật khẩu tài khoản sinh viên thì làm sao?', 'cau_tra_loi' => 'Nội dung mẫu: liên hệ bộ phận hỗ trợ để được cấp lại mật khẩu.', 'slug' => 'quen-mat-khau', 'trang_thai' => 'nhap', 'luot_xem' => 0],
        ]);

        DB::table('faq_the_tag')->insert([
            ['faq_id' => 1, 'the_id' => 1],
            ['faq_id' => 2, 'the_id' => 2],
            ['faq_id' => 3, 'the_id' => 3],
        ]);

        DB::table('cau_hoi')->insert([
            ['nguoi_gui_id' => 2, 'tieu_de' => 'Học lại môn đã rớt', 'noi_dung' => 'Em muốn hỏi thủ tục đăng ký học lại môn đã rớt.', 'trang_thai' => 'cho_duyet'],
            ['nguoi_gui_id' => 3, 'tieu_de' => 'Đăng ký phòng máy cho lớp thực hành', 'noi_dung' => 'Tôi cần đăng ký phòng máy cho buổi thực hành ngoài giờ.', 'trang_thai' => 'cho_duyet'],
        ]);
    }
}