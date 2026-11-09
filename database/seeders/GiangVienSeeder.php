<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Nạp nhiều tài khoản giảng viên (kèm ảnh đại diện) vào bảng users.
 *
 * - Chỉ THÊM mới. Email đã tồn tại thì BỎ QUA, không sửa tài khoản cũ.
 * - Ảnh: copy file vào storage/app/public/giang-vien/ (tên file khớp cột 'anh' bên dưới)
 *   rồi chạy một lần: php artisan storage:link
 * - Giảng viên chưa có ảnh: để 'anh' => null (hoặc '#').
 * - Giảng viên chưa có email: để 'email' => '' thì seeder sẽ bỏ qua và báo.
 *
 * Chạy: php artisan db:seed --class=GiangVienSeeder
 */
class GiangVienSeeder extends Seeder
{
    /** Mật khẩu mặc định cho tất cả tài khoản bên dưới (nên đổi sau khi đăng nhập) */
    private const MAT_KHAU = 'GiangVien@123';

    /** Thư mục lưu ảnh (tính từ storage/app/public) */
    private const THU_MUC_ANH = 'giang-vien';

    public function run(): void
    {
        // ====== DANH SÁCH GIẢNG VIÊN ======
        $danhSach = [
            ['name' => 'TS. Nguyễn Nhứt Lam',          'email' => 'lamnn@tvu.edu.vn',              'anh' => 'Lam.jpg'],
            ['name' => 'TS. Thạch Kọng Saoane',        'email' => 'oane@tvu.edu.vn',               'anh' => 'Kong.jpg'],
            ['name' => 'TS. Nguyễn Trần Diễm Hạnh',    'email' => 'diemhanh_tvu@tvu.edu.vn',       'anh' => 'Hanh.jpg'],
            ['name' => 'ThS. Nguyễn Bá Nhiệm',         'email' => 'nhiemnb@tvu.edu.vn',            'anh' => 'Nhiem.jpg'],
            ['name' => 'ThS. Lê Phong Dũ',             'email' => 'lpdu@tvu.edu.vn',               'anh' => null],
            ['name' => 'TS. Đoàn Phước Miền',          'email' => 'phuocmien@tvu.edu.vn',          'anh' => null],
            ['name' => 'ThS. Võ Thành C',              'email' => 'vothanhc@tvu.edu.vn',           'anh' => null],
            ['name' => 'ThS. Trịnh Quốc Việt',         'email' => 'tqviettv@tvu.edu.vn',           'anh' => null],
            ['name' => 'ThS. Trần Văn Nam',            'email' => 'namtv@tvu.edu.vn',              'anh' => null],
            ['name' => 'ThS. Trầm Hoàng Nam',          'email' => 'tramhoangnam@tvu.edu.vn',       'anh' => null],
            ['name' => 'ThS. Phan Thị Phương Nam',     'email' => 'ptpnam@tvu.edu.vn',             'anh' => null],
            ['name' => 'ThS. Phạm Thị Trúc Mai',       'email' => 'pttmai@tvu.edu.vn',             'anh' => null],
            ['name' => 'ThS. Phạm Minh Đương',         'email' => 'duongminh@tvu.edu.vn',          'anh' => null], 
            ['name' => 'ThS. Nguyễn Thừa Phát Tài',    'email' => 'phattai@tvu.edu.vn',            'anh' => null],
            ['name' => 'ThS. Nguyễn Ngọc Đan Thanh',   'email' => 'ngocdanthanhdt@tvu.edu.vn',     'anh' => null],
            ['name' => 'ThS. Nguyễn Mộng Hiền',        'email' => 'hientvu@tvu.edu.vn',            'anh' => null],
            ['name' => 'ThS. Nguyễn Hoàng Duy Thiện',  'email' => 'thiennhd@tvu.edu.vn',           'anh' => null],
            ['name' => 'ThS. Lê Minh Tự',              'email' => 'lmtu@tvu.edu.vn',               'anh' => null],
        ];
        // ===================================

        // Tên cột ảnh đại diện trong bảng users
        $cotAnh = collect(['anh_dai_dien', 'avatar', 'anh'])
            ->first(fn ($c) => Schema::hasColumn('users', $c));

        $coTrangThai = Schema::hasColumn('users', 'trang_thai');

        foreach ($danhSach as $gv) {
            $email = mb_strtolower(trim((string) ($gv['email'] ?? '')));

            // Bỏ qua dòng thiếu email hoặc email sai định dạng
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->command?->warn("Bỏ qua (thiếu/sai email): {$gv['name']}");
                continue;
            }

            // Email đã có thì không đụng vào
            if (DB::table('users')->where('email', $email)->exists()) {
                $this->command?->warn("Bỏ qua (đã có): {$email}");
                continue;
            }

            $row = [
                'name'              => trim($gv['name']),
                'email'             => $email,
                'password'          => Hash::make(self::MAT_KHAU),
                'vai_tro'           => 'giang_vien',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            if ($coTrangThai) {
                $row['trang_thai'] = 1; // 1 = đang hoạt động
            }

            // Chỉ ghi ảnh khi có tên file thật (bỏ qua null, rỗng, '#')
            $anh = trim((string) ($gv['anh'] ?? ''));
            if ($cotAnh && $anh !== '' && $anh !== '#') {
                $row[$cotAnh] = self::THU_MUC_ANH . '/' . $anh;
            }

            DB::table('users')->insert($row);
            $this->command?->info("Đã thêm: {$gv['name']} ({$email})");
        }
    }
}