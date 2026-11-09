<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Nạp 10 danh mục + 30 câu hỏi FAQ của Khoa CNTT.
 * - Tự nhận tên cột thật trong bảng danh_muc / faq (không cần sửa tay).
 * - Chạy nhiều lần vẫn an toàn: danh mục / câu hỏi đã có thì bỏ qua.
 * - Nội dung trả lời là bản mẫu chung; hãy chỉnh lại theo quy chế hiện hành của Trường/Khoa.
 */
class FaqSeeder extends Seeder
{
    private function pick(array $cols, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (in_array($c, $cols, true)) return $c;
        }
        return null;
    }

    private function data(): array
    {
        return [
            'Đào tạo' => [
                ['Điều kiện để được xét tốt nghiệp là gì?',
                 "Sinh viên được xét tốt nghiệp khi đáp ứng đồng thời các điều kiện theo quy chế đào tạo hiện hành:\n- Tích lũy đủ số tín chỉ và các học phần của chương trình đào tạo.\n- Đạt điểm trung bình chung tích lũy tối thiểu theo quy định.\n- Đạt chuẩn đầu ra (ngoại ngữ, tin học) và hoàn thành các học phần điều kiện.\n- Không bị truy cứu trách nhiệm hình sự, không trong thời gian bị kỷ luật đình chỉ học tập.\nThời gian và hồ sơ xét tốt nghiệp được Phòng Đào tạo thông báo mỗi đợt."],
                ['Làm sao để xem thời khóa biểu và lịch thi của học kỳ?',
                 "Sinh viên đăng nhập cổng thông tin sinh viên của Trường để xem thời khóa biểu theo học phần đã đăng ký và lịch thi từng học kỳ. Lịch thi cũng được Phòng Đào tạo và Khoa thông báo trên website, bảng tin và nhóm lớp. Nếu thấy sai sót hoặc trùng lịch, liên hệ Văn phòng Khoa để được hỗ trợ."],
                ['Điểm học phần được tính như thế nào (chuyên cần, giữa kỳ, cuối kỳ)?',
                 "Điểm học phần là tổng điểm các thành phần (chuyên cần, giữa kỳ, thực hành, cuối kỳ…) nhân với trọng số ghi trong đề cương chi tiết của từng học phần, sau đó quy đổi sang thang điểm chữ và thang điểm 4. Sinh viên xem trọng số cụ thể trong đề cương học phần hoặc hỏi giảng viên phụ trách ngay buổi học đầu tiên."],
            ],
            'Đăng ký học phần' => [
                ['Khi nào mở đăng ký học phần và đăng ký ở đâu?',
                 "Thời gian đăng ký học phần được Phòng Đào tạo thông báo trước mỗi học kỳ. Sinh viên đăng ký trực tuyến trên cổng thông tin sinh viên của Trường bằng tài khoản cá nhân. Nên theo dõi thông báo và đăng ký đúng đợt để không bị hết chỗ hoặc phải đăng ký bổ sung."],
                ['Mỗi học kỳ được đăng ký tối thiểu và tối đa bao nhiêu tín chỉ?',
                 "Số tín chỉ tối thiểu và tối đa mỗi học kỳ được quy định trong quy chế đào tạo của Trường và có thể khác nhau tùy học lực, khóa học, học kỳ chính hay học kỳ hè. Sinh viên xem mức cụ thể trong thông báo đăng ký học phần của từng học kỳ hoặc hỏi cố vấn học tập."],
                ['Muốn hủy hoặc đổi học phần sau khi đã đăng ký thì làm thế nào?',
                 "Trong thời gian đăng ký điều chỉnh do Phòng Đào tạo thông báo, sinh viên có thể hủy hoặc đổi học phần trực tiếp trên cổng thông tin sinh viên. Quá thời hạn này, sinh viên làm đơn nêu lý do, có ý kiến của cố vấn học tập và nộp tại Văn phòng Khoa để được xem xét."],
            ],
            'Học vụ' => [
                ['Khi nào sinh viên bị cảnh báo học vụ?',
                 "Sinh viên bị cảnh báo học vụ khi kết quả học tập không đạt mức tối thiểu theo quy chế, ví dụ điểm trung bình học kỳ hoặc điểm trung bình tích lũy thấp, hoặc số tín chỉ không đạt vượt mức cho phép. Mức cụ thể ghi trong quy chế đào tạo. Khi nhận cảnh báo, sinh viên nên gặp cố vấn học tập để lập kế hoạch học tập phù hợp."],
                ['Điều kiện và thủ tục để bảo lưu kết quả học tập hoặc tạm nghỉ học là gì?',
                 "Sinh viên có lý do chính đáng (sức khỏe, hoàn cảnh gia đình, nghĩa vụ…) có thể xin tạm nghỉ học và bảo lưu kết quả đã tích lũy.\nThủ tục: làm đơn có xác nhận của cố vấn học tập/Khoa, kèm giấy tờ minh chứng, nộp tại Phòng Đào tạo trong thời hạn quy định. Thời gian tạm nghỉ tối đa và điều kiện trở lại học theo quy chế hiện hành."],
                ['Làm sao để học cải thiện điểm hoặc học lại học phần?',
                 "Sinh viên đăng ký học lại (học phần chưa đạt) hoặc học cải thiện (học phần đã đạt nhưng muốn nâng điểm) trong đợt đăng ký học phần của các học kỳ sau, kể cả học kỳ hè nếu có mở lớp. Điểm cao nhất hoặc điểm mới nhất sẽ được tính theo quy chế của Trường. Mức học phí học lại và học cải thiện theo thông báo của Trường."],
            ],
            'Thực tập' => [
                ['Điều kiện để được đăng ký thực tập là gì?',
                 "Sinh viên được đăng ký thực tập khi đã tích lũy đủ số tín chỉ hoặc các học phần tiên quyết theo chương trình đào tạo và không nợ các học phần điều kiện. Khoa sẽ thông báo danh sách sinh viên đủ điều kiện, thời gian đăng ký và kế hoạch thực tập mỗi đợt."],
                ['Sinh viên tự tìm nơi thực tập hay Khoa giới thiệu?',
                 "Sinh viên có thể tự liên hệ đơn vị thực tập phù hợp với ngành học và nộp giấy tiếp nhận có xác nhận của đơn vị cho Khoa. Ngoài ra Khoa có danh sách doanh nghiệp, đơn vị đối tác để giới thiệu cho sinh viên chưa tìm được nơi thực tập. Vui lòng theo dõi thông báo của Khoa và liên hệ giảng viên phụ trách thực tập."],
            ],
            'Đồ án' => [
                ['Khi nào và bằng cách nào đăng ký đề tài đồ án?',
                 "Khoa thông báo thời gian đăng ký đề tài đồ án vào đầu học kỳ có học phần đồ án. Sinh viên chọn đề tài theo danh sách do giảng viên đề xuất hoặc tự đề xuất đề tài, sau đó nộp phiếu đăng ký đề tài theo mẫu của Khoa cho bộ môn để được duyệt."],
                ['Làm sao để chọn giảng viên hướng dẫn?',
                 "Sinh viên tham khảo lĩnh vực chuyên môn của giảng viên, liên hệ trao đổi trực tiếp và được giảng viên đồng ý hướng dẫn trong thời gian đăng ký. Trường hợp chưa liên hệ được, Bộ môn sẽ phân công giảng viên hướng dẫn dựa trên đề tài và số lượng sinh viên mỗi giảng viên."],
                ['Quyển báo cáo đồ án cần trình bày theo mẫu nào, nộp khi nào?',
                 "Quyển báo cáo trình bày theo mẫu và quy định định dạng (khổ giấy, font chữ, cỡ chữ, giãn dòng, cách trình bày mục lục và tài liệu tham khảo) do Khoa ban hành. Mẫu được đăng trên website Khoa hoặc gửi qua giảng viên hướng dẫn. Thời hạn nộp được thông báo trong kế hoạch đồ án của từng học kỳ."],
            ],
            'Khóa luận' => [
                ['Điều kiện để được làm khóa luận tốt nghiệp là gì?',
                 "Sinh viên được xét làm khóa luận tốt nghiệp khi đạt điểm trung bình chung tích lũy và số tín chỉ tối thiểu theo quy định của Khoa tại thời điểm đăng ký, không nợ học phần điều kiện. Danh sách sinh viên đủ điều kiện được Khoa thông báo hằng năm."],
                ['Khóa luận được bảo vệ trước hội đồng như thế nào?',
                 "Sau khi hoàn thành khóa luận và được giảng viên hướng dẫn đồng ý, sinh viên nộp quyển khóa luận đúng hạn. Hội đồng do Khoa thành lập gồm chủ tịch, thư ký, ủy viên và phản biện. Sinh viên trình bày kết quả, trả lời câu hỏi của hội đồng; điểm được tổng hợp từ điểm hướng dẫn, phản biện và hội đồng."],
                ['Nếu không đạt thì được bảo vệ lại hay phải chuyển sang học phần thay thế?',
                 "Tùy quy định của Khoa, sinh viên chưa đạt có thể được xem xét chỉnh sửa và bảo vệ lại một lần hoặc chuyển sang học các học phần thay thế khóa luận để đủ tín chỉ tốt nghiệp. Sinh viên liên hệ Văn phòng Khoa để được hướng dẫn phương án phù hợp."],
            ],
            'Nghiên cứu khoa học' => [
                ['Tham gia nghiên cứu khoa học có được cộng điểm hoặc khen thưởng không?',
                 "Sinh viên có đề tài nghiên cứu khoa học đạt kết quả có thể được khen thưởng, cộng điểm rèn luyện, được ưu tiên xét học bổng hoặc ghi nhận trong hồ sơ học tập theo quy định của Trường và Khoa. Mức khen thưởng cụ thể theo thông báo từng năm."],
                ['Sinh viên được tham gia nghiên cứu khoa học bằng cách nào?',
                 "Sinh viên có thể đăng ký đề tài theo thông báo hằng năm của Khoa, hoặc tham gia nhóm nghiên cứu cùng giảng viên. Cần có giảng viên hướng dẫn, nộp thuyết minh đề tài và được hội đồng xét duyệt. Sinh viên theo dõi thông báo tại Văn phòng Khoa hoặc website Khoa."],
            ],
            'Học bổng' => [
                ['Có những loại học bổng nào dành cho sinh viên Khoa CNTT?',
                 "Sinh viên có thể tham gia xét các loại học bổng như: học bổng khuyến khích học tập của Trường, học bổng của doanh nghiệp và tổ chức tài trợ, học bổng dành cho sinh viên có hoàn cảnh khó khăn. Thông tin từng loại học bổng được Khoa và Phòng chức năng thông báo theo từng học kỳ."],
                ['Điều kiện xét học bổng khuyến khích học tập là gì?',
                 "Sinh viên cần đạt kết quả học tập và điểm rèn luyện từ mức quy định trở lên trong học kỳ xét, đăng ký đủ số tín chỉ tối thiểu và không bị kỷ luật. Học bổng được xét theo thứ tự từ cao xuống thấp trong chỉ tiêu của từng ngành. Mức điểm cụ thể theo thông báo của Trường."],
                ['Hồ sơ xin học bổng gồm những gì và nộp ở đâu?',
                 "Hồ sơ thường gồm: đơn đề nghị xét học bổng, bảng điểm học kỳ, giấy xác nhận (nếu là học bổng cho sinh viên khó khăn) và các minh chứng thành tích khác (nếu có). Hồ sơ nộp tại Văn phòng Khoa hoặc đơn vị phụ trách đúng thời hạn thông báo."],
            ],
            'Thủ tục' => [
                ['Thủ tục xin cấp bảng điểm là gì?',
                 "Sinh viên làm đơn xin cấp bảng điểm (hoặc đăng ký trực tuyến nếu có), nộp tại Phòng Đào tạo/bộ phận một cửa và nộp lệ phí theo quy định. Thời gian giải quyết theo thông báo của Trường; sinh viên nên nêu rõ cần bảng điểm tiếng Việt hay tiếng Anh và số bản cần cấp."],
                ['Làm sao để xin chuyển ngành hoặc chuyển lớp?',
                 "Sinh viên làm đơn nêu lý do, có ý kiến của cố vấn học tập và Khoa nơi đi và nơi đến, nộp tại Phòng Đào tạo trong thời gian Trường thông báo. Việc chuyển ngành, chuyển lớp phụ thuộc vào chỉ tiêu và điều kiện xét của từng ngành."],
                ['Cần làm gì khi mất thẻ sinh viên?',
                 "Sinh viên làm đơn xin cấp lại thẻ, kèm giấy tờ tùy thân và ảnh theo yêu cầu, nộp tại bộ phận phụ trách thẻ sinh viên của Trường và nộp lệ phí cấp lại. Trong thời gian chờ, có thể xin giấy xác nhận sinh viên tạm thời."],
                ['Thủ tục xin giấy xác nhận vay vốn sinh viên hoặc giấy xác nhận hoãn nghĩa vụ quân sự là gì?',
                 "Sinh viên điền mẫu đơn xin xác nhận (mẫu đăng tại bộ phận một cửa hoặc website của Trường), nộp kèm bản sao giấy tờ cần thiết tại Phòng Công tác sinh viên hoặc bộ phận được phân công. Thời gian nhận kết quả theo hẹn trên phiếu; nên nộp trước thời hạn cần dùng ít nhất vài ngày làm việc."],
                ['Cách làm đơn gia hạn chi phí học tập?',
                 "Sinh viên có khó khăn tài chính làm đơn gia hạn nộp học phí nêu rõ lý do và thời gian đề nghị gia hạn, có xác nhận của Khoa hoặc cố vấn học tập, kèm giấy tờ minh chứng nếu có, nộp tại Phòng Kế hoạch – Tài chính trước hạn nộp học phí."],
            ],
            'Hoạt động sinh viên' => [
                ['Điểm rèn luyện được tính như thế nào?',
                 "Điểm rèn luyện được đánh giá theo thang 100 điểm gồm các tiêu chí: ý thức học tập, chấp hành nội quy và quy chế, tham gia hoạt động chính trị – xã hội – văn hóa – thể thao, quan hệ cộng đồng và công tác lớp. Sinh viên tự đánh giá, sau đó lớp, cố vấn học tập và Khoa xét duyệt vào cuối mỗi học kỳ."],
                ['Có những cuộc thi học thuật, lập trình nào dành cho sinh viên?',
                 "Hằng năm Khoa và Trường tổ chức hoặc giới thiệu sinh viên tham gia các cuộc thi như: thi lập trình, Olympic tin học, thi ý tưởng khởi nghiệp, hackathon và các cuộc thi học thuật do doanh nghiệp, tổ chức tài trợ. Thông tin cụ thể được đăng trên website Khoa và các kênh thông báo của lớp."],
                ['Tham gia hoạt động có được tính điểm rèn luyện hoặc ngày công tác xã hội không?',
                 "Các hoạt động do Trường, Khoa, Đoàn – Hội tổ chức có thể được cộng điểm rèn luyện và ghi nhận ngày công tác xã hội theo hướng dẫn của từng hoạt động. Sinh viên giữ lại giấy xác nhận hoặc đăng ký trong danh sách tham gia để được ghi nhận."],
            ],
        ];
    }

    public function run(): void
    {
        $dmCols  = Schema::getColumnListing('danh_muc');
        $faqCols = Schema::getColumnListing('faq');

        $dmPk   = $this->pick($dmCols, ['danh_muc_id', 'id']);
        $dmTen  = $this->pick($dmCols, ['ten_danh_muc', 'tendanhmuc', 'ten', 'ten_dm', 'name']);
        $dmSlug = $this->pick($dmCols, ['slug']);

        if (!$dmPk || !$dmTen) {
            throw new \RuntimeException('Không nhận ra cột khóa/tên của bảng danh_muc. Các cột hiện có: ' . implode(', ', $dmCols));
        }

        // Giá trị trạng thái "đã đăng" của FAQ
        $trangThai = 'da_xuat_ban';
        if (defined('App\Models\Faq::TRANG_THAI')) {
            $keys = array_keys(\App\Models\Faq::TRANG_THAI);
            $found = null;
            foreach (['da_xuat_ban', 'xuat_ban', 'cong_khai', 'da_dang', 'published', 'hien_thi'] as $k) {
                if (in_array($k, $keys, true)) { $found = $k; break; }
            }
            $trangThai = $found
                ?? (collect($keys)->first(fn ($k) => $k !== 'nhap') ?? $trangThai);
        }
        $this->command?->info("Trạng thái FAQ sẽ dùng: {$trangThai}");

        // Người tạo: ưu tiên tài khoản quản trị
        $userId = null;
        if (Schema::hasTable('users')) {
            $uPk = Schema::hasColumn('users', 'id') ? 'id' : 'nguoi_dung_id';
            if (Schema::hasColumn('users', 'vai_tro')) {
                $userId = DB::table('users')->whereIn('vai_tro', ['admin', 'quan_tri', 'quan_tri_vien'])->value($uPk);
            }
            $userId = $userId ?: DB::table('users')->orderBy($uPk)->value($uPk);
        }

        $now = now();

        // Danh mục đã có (khớp theo tên hoặc slug)
        $existing = DB::table('danh_muc')->get();
        $findCat = function (string $name) use ($existing, $dmTen, $dmSlug) {
            $slug = Str::slug($name);
            foreach ($existing as $row) {
                if (mb_strtolower(trim($row->{$dmTen})) === mb_strtolower($name)) return $row;
                if ($dmSlug && ($row->{$dmSlug} ?? null) === $slug) return $row;
            }
            return null;
        };

        $nCat = 0; $nFaq = 0; $stt = 0;

        foreach ($this->data() as $catName => $items) {
            $row = $findCat($catName);

            if ($row) {
                $catId = $row->{$dmPk};
            } else {
                $ins = [$dmTen => $catName];
                if ($dmSlug)                          $ins[$dmSlug] = Str::slug($catName);
                $ins['trang_thai'] = 1;          // 1 = đang hiển thị
                $ins['ngay_tao']   = $now;
                $ins['created_at'] = $now;
                $ins['updated_at'] = $now;
                $ins = array_intersect_key($ins, array_flip($dmCols));
                $catId = DB::table('danh_muc')->insertGetId($ins, $dmPk);
                $nCat++;
            }

            foreach ($items as [$q, $a]) {
                $stt++;
                if (DB::table('faq')->where('cau_hoi', $q)->exists()) continue;

                $row = [
                    'cau_hoi'     => $q,
                    'cau_tra_loi' => $a,
                    'danh_muc_id' => $catId,
                    'trang_thai'  => $trangThai,
                    'nguoi_tao_id'=> $userId,
                    'slug'        => Str::slug(Str::limit($q, 70, '')) . '-' . $stt,
                    'luot_xem'    => random_int(20, 300),
                    'ngay_tao'    => $now,
                    'ngay_cap_nhat' => $now,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
                // chỉ giữ những cột thật sự tồn tại trong bảng faq
                $row = array_intersect_key($row, array_flip($faqCols));

                DB::table('faq')->insert($row);
                $nFaq++;
            }
        }

        $this->command?->info("Đã thêm {$nCat} danh mục mới và {$nFaq} câu hỏi FAQ.");
    }
}