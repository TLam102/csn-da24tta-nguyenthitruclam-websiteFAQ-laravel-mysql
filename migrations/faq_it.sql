-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table faq_it.cau_hoi
CREATE TABLE IF NOT EXISTS `cau_hoi` (
  `cau_hoi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoi_gui_id` bigint unsigned NOT NULL,
  `nguoi_duyet_id` bigint unsigned DEFAULT NULL,
  `tieu_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cho_duyet',
  `ly_do_tu_choi` text COLLATE utf8mb4_unicode_ci,
  `ngay_gui` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_duyet` timestamp NULL DEFAULT NULL,
  `danh_muc_id` bigint unsigned DEFAULT NULL,
  `giang_vien_id` bigint unsigned DEFAULT NULL,
  `giang_vien_da_xem` tinyint(1) NOT NULL DEFAULT '0',
  `giang_vien_xem_luc` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cau_hoi_id`),
  KEY `cau_hoi_nguoi_gui_id_foreign` (`nguoi_gui_id`),
  KEY `cau_hoi_nguoi_duyet_id_foreign` (`nguoi_duyet_id`),
  KEY `idx_giang_vien_id` (`giang_vien_id`),
  CONSTRAINT `cau_hoi_nguoi_duyet_id_foreign` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `users` (`id`),
  CONSTRAINT `cau_hoi_nguoi_gui_id_foreign` FOREIGN KEY (`nguoi_gui_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.cau_hoi: ~8 rows (approximately)
INSERT INTO `cau_hoi` (`cau_hoi_id`, `nguoi_gui_id`, `nguoi_duyet_id`, `tieu_de`, `noi_dung`, `trang_thai`, `ly_do_tu_choi`, `ngay_gui`, `ngay_duyet`, `danh_muc_id`, `giang_vien_id`, `giang_vien_da_xem`, `giang_vien_xem_luc`) VALUES
	(1, 2, NULL, 'Học lại môn đã rớt', 'Em muốn hỏi thủ tục đăng ký học lại môn đã rớt.', 'tu_choi', NULL, '2026-10-28 10:32:30', NULL, NULL, 3, 1, '2026-11-05 21:32:36'),
	(2, 3, NULL, 'Đăng ký phòng máy cho lớp thực hành', 'Tôi cần đăng ký phòng máy cho buổi thực hành ngoài giờ.', 'cho_duyet', NULL, '2026-10-28 10:32:30', NULL, NULL, 3, 1, '2026-11-05 21:32:36'),
	(3, 2, NULL, 'đăng ký môn học như thế nào ?', 'môn thiết kê web e bị đầy hết slot', 'da_tra_loi', NULL, '2026-11-05 02:58:37', NULL, 1, 3, 1, '2026-11-05 23:39:07'),
	(4, 2, NULL, 'đăng ký môn học như thế nào ?', 'môn e đăng ký bị hết slot', 'da_tra_loi', NULL, '2026-11-06 01:39:56', NULL, 1, 5, 1, '2026-11-06 23:42:47'),
	(5, 5, NULL, 'có bạn nào muốn tham gia NCKH cùng với thầy không?', 'có nhiều quyền lợi lắm nha', 'da_tra_loi', NULL, '2026-11-07 00:16:20', NULL, 11, NULL, 0, NULL),
	(6, 5, NULL, 'trường sắp có hoạt động về nguồn cần khảo sát số lượng tham gia', 'hoạt động được cộng đrl', 'da_tra_loi', NULL, '2026-11-07 00:55:42', NULL, 13, NULL, 0, NULL),
	(7, 5, NULL, 'haaaaaaaaaaaaaa?', 'laaaaaaaaadbudsug', 'da_tra_loi', NULL, '2026-11-07 02:37:01', NULL, 1, 4, 1, '2026-11-07 02:39:51'),
	(8, 5, NULL, 'phòng C71.206 có máy bị hư hỏng', 'kiểm tra máy trước khi rời lớp nhé', 'da_tra_loi', NULL, '2026-11-07 23:36:28', NULL, 3, 14, 1, '2026-11-07 23:39:40');

-- Dumping structure for table faq_it.cau_hoi_tep
CREATE TABLE IF NOT EXISTS `cau_hoi_tep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cau_hoi_id` bigint unsigned NOT NULL,
  `ten_goc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duong_dan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kich_thuoc` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cau_hoi_tep_cau_hoi_id_index` (`cau_hoi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.cau_hoi_tep: ~0 rows (approximately)

-- Dumping structure for table faq_it.cau_tra_loi
CREATE TABLE IF NOT EXISTS `cau_tra_loi` (
  `tra_loi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cau_hoi_id` bigint unsigned NOT NULL,
  `nguoi_tra_loi_id` bigint unsigned NOT NULL,
  `noi_dung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tra_loi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tra_loi_id`),
  KEY `cau_tra_loi_cau_hoi_id_foreign` (`cau_hoi_id`),
  KEY `cau_tra_loi_nguoi_tra_loi_id_foreign` (`nguoi_tra_loi_id`),
  CONSTRAINT `cau_tra_loi_cau_hoi_id_foreign` FOREIGN KEY (`cau_hoi_id`) REFERENCES `cau_hoi` (`cau_hoi_id`) ON DELETE CASCADE,
  CONSTRAINT `cau_tra_loi_nguoi_tra_loi_id_foreign` FOREIGN KEY (`nguoi_tra_loi_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.cau_tra_loi: ~4 rows (approximately)
INSERT INTO `cau_tra_loi` (`tra_loi_id`, `cau_hoi_id`, `nguoi_tra_loi_id`, `noi_dung`, `ngay_tra_loi`) VALUES
	(1, 3, 3, '<p>Chào em,</p><p>Vấn đề này thuộc thẩm quyền của Phòng Đào tạo. Em vui lòng liên hệ trực tiếp Phòng Đào tạo để được hướng dẫn cụ thể.</p><p>Trân trọng.</p>', '2026-11-05 23:39:07'),
	(2, 4, 5, '<p>Chào em,</p><p>Vấn đề này thuộc thẩm quyền của Phòng Đào tạo. Em vui lòng liên hệ trực tiếp Phòng Đào tạo để được hướng dẫn cụ thể.</p><p>Trân trọng.</p>', '2026-11-06 23:42:47'),
	(3, 7, 4, 'hihihihihi', '2026-11-07 02:39:51'),
	(4, 8, 14, 'ok e nha', '2026-11-07 23:39:40');

-- Dumping structure for table faq_it.cau_tra_loi_tep
CREATE TABLE IF NOT EXISTS `cau_tra_loi_tep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_loi_id` bigint unsigned NOT NULL,
  `ten_goc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duong_dan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kich_thuoc` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cau_tra_loi_tep_tra_loi_id_index` (`tra_loi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.cau_tra_loi_tep: ~0 rows (approximately)

-- Dumping structure for table faq_it.danh_muc
CREATE TABLE IF NOT EXISTS `danh_muc` (
  `danh_muc_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` tinyint NOT NULL DEFAULT '1',
  `ngay_tao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`danh_muc_id`),
  UNIQUE KEY `danh_muc_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.danh_muc: ~13 rows (approximately)
INSERT INTO `danh_muc` (`danh_muc_id`, `ten_danh_muc`, `mo_ta`, `slug`, `trang_thai`, `ngay_tao`) VALUES
	(1, 'Học vụ', 'Đăng ký học phần, bảng điểm, quy chế', 'hoc-vu', 1, '2026-10-28 10:32:30'),
	(2, 'Đồ án - Thực tập', 'Quy định đồ án, thực tập doanh nghiệp', 'do-an-thuc-tap', 1, '2026-10-28 10:32:30'),
	(3, 'Cơ sở vật chất', 'Phòng máy, phòng học, thiết bị', 'co-so-vat-chat', 1, '2026-10-28 10:32:30'),
	(4, 'Tài khoản và hệ thống', 'Tài khoản sinh viên, Wi-Fi, email', 'tai-khoan-he-thong', 1, '2026-10-28 10:32:30'),
	(5, 'Học Bổng', 'Các loại học bổng, điều kiện xét và thời gian nộp hồ sơ....', 'hoc-bong', 1, '2026-11-05 04:14:55'),
	(6, 'Đào tạo', NULL, 'dao-tao', 1, '2026-11-04 21:45:11'),
	(7, 'Đăng ký học phần', NULL, 'dang-ky-hoc-phan', 1, '2026-11-04 21:45:11'),
	(8, 'Thực tập', NULL, 'thuc-tap', 1, '2026-11-04 21:45:11'),
	(9, 'Đồ án', NULL, 'do-an', 1, '2026-11-04 21:45:11'),
	(10, 'Khóa luận', NULL, 'khoa-luan', 1, '2026-11-04 21:45:11'),
	(11, 'Nghiên cứu khoa học', NULL, 'nghien-cuu-khoa-hoc', 1, '2026-11-04 21:45:11'),
	(12, 'Thủ tục', NULL, 'thu-tuc', 1, '2026-11-04 21:45:11'),
	(13, 'Hoạt động sinh viên', NULL, 'hoat-dong-sinh-vien', 1, '2026-11-04 21:45:11');

-- Dumping structure for table faq_it.faq
CREATE TABLE IF NOT EXISTS `faq` (
  `faq_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danh_muc_id` bigint unsigned NOT NULL,
  `nguoi_tao_id` bigint unsigned NOT NULL,
  `cau_hoi_id` bigint unsigned DEFAULT NULL,
  `cau_hoi` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cau_tra_loi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` enum('nhap','da_xuat_ban') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nhap',
  `luot_xem` int unsigned NOT NULL DEFAULT '0',
  `ngay_tao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`faq_id`),
  UNIQUE KEY `faq_slug_unique` (`slug`),
  KEY `faq_danh_muc_id_foreign` (`danh_muc_id`),
  KEY `faq_nguoi_tao_id_foreign` (`nguoi_tao_id`),
  KEY `faq_cau_hoi_id_foreign` (`cau_hoi_id`),
  CONSTRAINT `faq_cau_hoi_id_foreign` FOREIGN KEY (`cau_hoi_id`) REFERENCES `cau_hoi` (`cau_hoi_id`) ON DELETE SET NULL,
  CONSTRAINT `faq_danh_muc_id_foreign` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`danh_muc_id`),
  CONSTRAINT `faq_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.faq: ~37 rows (approximately)
INSERT INTO `faq` (`faq_id`, `danh_muc_id`, `nguoi_tao_id`, `cau_hoi_id`, `cau_hoi`, `cau_tra_loi`, `slug`, `trang_thai`, `luot_xem`, `ngay_tao`, `ngay_cap_nhat`) VALUES
	(1, 1, 1, NULL, 'Làm thế nào để đăng ký học phần?', 'Nội dung mẫu: đăng nhập cổng thông tin sinh viên và chọn học phần trong thời gian đăng ký.', 'dang-ky-hoc-phan', 'da_xuat_ban', 0, '2026-10-28 10:32:30', NULL),
	(2, 2, 1, NULL, 'Điều kiện để làm đồ án tốt nghiệp là gì?', 'Nội dung mẫu: sinh viên cần đủ số tín chỉ theo quy định của khoa.', 'dieu-kien-lam-do-an', 'da_xuat_ban', 0, '2026-10-28 10:32:30', NULL),
	(3, 3, 1, NULL, 'Mượn phòng máy ngoài giờ học như thế nào?', 'Nội dung mẫu: liên hệ văn phòng khoa để đăng ký trước.', 'muon-phong-may', 'da_xuat_ban', 0, '2026-10-28 10:32:30', NULL),
	(4, 4, 1, NULL, 'Quên mật khẩu tài khoản sinh viên thì làm sao?', 'Nội dung mẫu: liên hệ bộ phận hỗ trợ để được cấp lại mật khẩu.', 'quen-mat-khau', 'nhap', 0, '2026-10-28 10:32:30', NULL),
	(5, 6, 1, NULL, 'Điều kiện để được xét tốt nghiệp là gì?', 'Sinh viên được xét tốt nghiệp khi đáp ứng đồng thời các điều kiện theo quy chế đào tạo hiện hành:\n- Tích lũy đủ số tín chỉ và các học phần của chương trình đào tạo.\n- Đạt điểm trung bình chung tích lũy tối thiểu theo quy định.\n- Đạt chuẩn đầu ra (ngoại ngữ, tin học) và hoàn thành các học phần điều kiện.\n- Không bị truy cứu trách nhiệm hình sự, không trong thời gian bị kỷ luật đình chỉ học tập.\nThời gian và hồ sơ xét tốt nghiệp được Phòng Đào tạo thông báo mỗi đợt.', 'dieu-kien-de-duoc-xet-tot-nghiep-la-gi-1', 'da_xuat_ban', 175, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(7, 6, 1, NULL, 'Điểm học phần được tính như thế nào (chuyên cần, giữa kỳ, cuối kỳ)?', 'Điểm học phần là tổng điểm các thành phần (chuyên cần, giữa kỳ, thực hành, cuối kỳ…) nhân với trọng số ghi trong đề cương chi tiết của từng học phần, sau đó quy đổi sang thang điểm chữ và thang điểm 4. Sinh viên xem trọng số cụ thể trong đề cương học phần hoặc hỏi giảng viên phụ trách ngay buổi học đầu tiên.', 'diem-hoc-phan-duoc-tinh-nhu-the-nao-chuyen-can-giua-ky-cuoi-ky-3', 'da_xuat_ban', 194, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(8, 7, 1, NULL, 'Khi nào mở đăng ký học phần và đăng ký ở đâu?', 'Thời gian đăng ký học phần được Phòng Đào tạo thông báo trước mỗi học kỳ. Sinh viên đăng ký trực tuyến trên cổng thông tin sinh viên của Trường bằng tài khoản cá nhân. Nên theo dõi thông báo và đăng ký đúng đợt để không bị hết chỗ hoặc phải đăng ký bổ sung.', 'khi-nao-mo-dang-ky-hoc-phan-va-dang-ky-o-dau-4', 'da_xuat_ban', 283, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(9, 7, 1, NULL, 'Mỗi học kỳ được đăng ký tối thiểu và tối đa bao nhiêu tín chỉ?', 'Số tín chỉ tối thiểu và tối đa mỗi học kỳ được quy định trong quy chế đào tạo của Trường và có thể khác nhau tùy học lực, khóa học, học kỳ chính hay học kỳ hè. Sinh viên xem mức cụ thể trong thông báo đăng ký học phần của từng học kỳ hoặc hỏi cố vấn học tập.', 'moi-hoc-ky-duoc-dang-ky-toi-thieu-va-toi-da-bao-nhieu-tin-chi-5', 'da_xuat_ban', 274, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(10, 7, 1, NULL, 'Muốn hủy hoặc đổi học phần sau khi đã đăng ký thì làm thế nào?', 'Trong thời gian đăng ký điều chỉnh do Phòng Đào tạo thông báo, sinh viên có thể hủy hoặc đổi học phần trực tiếp trên cổng thông tin sinh viên. Quá thời hạn này, sinh viên làm đơn nêu lý do, có ý kiến của cố vấn học tập và nộp tại Văn phòng Khoa để được xem xét.', 'muon-huy-hoac-doi-hoc-phan-sau-khi-da-dang-ky-thi-lam-the-nao-6', 'da_xuat_ban', 127, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(11, 1, 1, NULL, 'Khi nào sinh viên bị cảnh báo học vụ?', 'Sinh viên bị cảnh báo học vụ khi kết quả học tập không đạt mức tối thiểu theo quy chế, ví dụ điểm trung bình học kỳ hoặc điểm trung bình tích lũy thấp, hoặc số tín chỉ không đạt vượt mức cho phép. Mức cụ thể ghi trong quy chế đào tạo. Khi nhận cảnh báo, sinh viên nên gặp cố vấn học tập để lập kế hoạch học tập phù hợp.', 'khi-nao-sinh-vien-bi-canh-bao-hoc-vu-7', 'da_xuat_ban', 85, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(12, 1, 1, NULL, 'Điều kiện và thủ tục để bảo lưu kết quả học tập hoặc tạm nghỉ học là gì?', 'Sinh viên có lý do chính đáng (sức khỏe, hoàn cảnh gia đình, nghĩa vụ…) có thể xin tạm nghỉ học và bảo lưu kết quả đã tích lũy.\nThủ tục: làm đơn có xác nhận của cố vấn học tập/Khoa, kèm giấy tờ minh chứng, nộp tại Phòng Đào tạo trong thời hạn quy định. Thời gian tạm nghỉ tối đa và điều kiện trở lại học theo quy chế hiện hành.', 'dieu-kien-va-thu-tuc-de-bao-luu-ket-qua-hoc-tap-hoac-tam-nghi-hoc-la-g-8', 'da_xuat_ban', 119, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(13, 1, 1, NULL, 'Làm sao để học cải thiện điểm hoặc học lại học phần?', 'Sinh viên đăng ký học lại (học phần chưa đạt) hoặc học cải thiện (học phần đã đạt nhưng muốn nâng điểm) trong đợt đăng ký học phần của các học kỳ sau, kể cả học kỳ hè nếu có mở lớp. Điểm cao nhất hoặc điểm mới nhất sẽ được tính theo quy chế của Trường. Mức học phí học lại và học cải thiện theo thông báo của Trường.', 'lam-sao-de-hoc-cai-thien-diem-hoac-hoc-lai-hoc-phan-9', 'da_xuat_ban', 300, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(14, 8, 1, NULL, 'Điều kiện để được đăng ký thực tập là gì?', 'Sinh viên được đăng ký thực tập khi đã tích lũy đủ số tín chỉ hoặc các học phần tiên quyết theo chương trình đào tạo và không nợ các học phần điều kiện. Khoa sẽ thông báo danh sách sinh viên đủ điều kiện, thời gian đăng ký và kế hoạch thực tập mỗi đợt.', 'dieu-kien-de-duoc-dang-ky-thuc-tap-la-gi-10', 'da_xuat_ban', 93, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(15, 8, 1, NULL, 'Sinh viên tự tìm nơi thực tập hay Khoa giới thiệu?', 'Sinh viên có thể tự liên hệ đơn vị thực tập phù hợp với ngành học và nộp giấy tiếp nhận có xác nhận của đơn vị cho Khoa. Ngoài ra Khoa có danh sách doanh nghiệp, đơn vị đối tác để giới thiệu cho sinh viên chưa tìm được nơi thực tập. Vui lòng theo dõi thông báo của Khoa và liên hệ giảng viên phụ trách thực tập.', 'sinh-vien-tu-tim-noi-thuc-tap-hay-khoa-gioi-thieu-11', 'da_xuat_ban', 164, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(16, 9, 1, NULL, 'Khi nào và bằng cách nào đăng ký đề tài đồ án?', 'Khoa thông báo thời gian đăng ký đề tài đồ án vào đầu học kỳ có học phần đồ án. Sinh viên chọn đề tài theo danh sách do giảng viên đề xuất hoặc tự đề xuất đề tài, sau đó nộp phiếu đăng ký đề tài theo mẫu của Khoa cho bộ môn để được duyệt.', 'khi-nao-va-bang-cach-nao-dang-ky-de-tai-do-an-12', 'da_xuat_ban', 123, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(17, 9, 1, NULL, 'Làm sao để chọn giảng viên hướng dẫn?', 'Sinh viên tham khảo lĩnh vực chuyên môn của giảng viên, liên hệ trao đổi trực tiếp và được giảng viên đồng ý hướng dẫn trong thời gian đăng ký. Trường hợp chưa liên hệ được, Bộ môn sẽ phân công giảng viên hướng dẫn dựa trên đề tài và số lượng sinh viên mỗi giảng viên.', 'lam-sao-de-chon-giang-vien-huong-dan-13', 'da_xuat_ban', 291, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(18, 9, 1, NULL, 'Quyển báo cáo đồ án cần trình bày theo mẫu nào, nộp khi nào?', 'Quyển báo cáo trình bày theo mẫu và quy định định dạng (khổ giấy, font chữ, cỡ chữ, giãn dòng, cách trình bày mục lục và tài liệu tham khảo) do Khoa ban hành. Mẫu được đăng trên website Khoa hoặc gửi qua giảng viên hướng dẫn. Thời hạn nộp được thông báo trong kế hoạch đồ án của từng học kỳ.', 'quyen-bao-cao-do-an-can-trinh-bay-theo-mau-nao-nop-khi-nao-14', 'da_xuat_ban', 60, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(19, 10, 1, NULL, 'Điều kiện để được làm khóa luận tốt nghiệp là gì?', 'Sinh viên được xét làm khóa luận tốt nghiệp khi đạt điểm trung bình chung tích lũy và số tín chỉ tối thiểu theo quy định của Khoa tại thời điểm đăng ký, không nợ học phần điều kiện. Danh sách sinh viên đủ điều kiện được Khoa thông báo hằng năm.', 'dieu-kien-de-duoc-lam-khoa-luan-tot-nghiep-la-gi-15', 'da_xuat_ban', 209, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(20, 10, 1, NULL, 'Khóa luận được bảo vệ trước hội đồng như thế nào?', 'Sau khi hoàn thành khóa luận và được giảng viên hướng dẫn đồng ý, sinh viên nộp quyển khóa luận đúng hạn. Hội đồng do Khoa thành lập gồm chủ tịch, thư ký, ủy viên và phản biện. Sinh viên trình bày kết quả, trả lời câu hỏi của hội đồng; điểm được tổng hợp từ điểm hướng dẫn, phản biện và hội đồng.', 'khoa-luan-duoc-bao-ve-truoc-hoi-dong-nhu-the-nao-16', 'da_xuat_ban', 291, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(21, 10, 1, NULL, 'Nếu không đạt thì được bảo vệ lại hay phải chuyển sang học phần thay thế?', 'Tùy quy định của Khoa, sinh viên chưa đạt có thể được xem xét chỉnh sửa và bảo vệ lại một lần hoặc chuyển sang học các học phần thay thế khóa luận để đủ tín chỉ tốt nghiệp. Sinh viên liên hệ Văn phòng Khoa để được hướng dẫn phương án phù hợp.', 'neu-khong-dat-thi-duoc-bao-ve-lai-hay-phai-chuyen-sang-hoc-phan-thay-t-17', 'da_xuat_ban', 259, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(22, 11, 1, NULL, 'Tham gia nghiên cứu khoa học có được cộng điểm hoặc khen thưởng không?', 'Sinh viên có đề tài nghiên cứu khoa học đạt kết quả có thể được khen thưởng, cộng điểm rèn luyện, được ưu tiên xét học bổng hoặc ghi nhận trong hồ sơ học tập theo quy định của Trường và Khoa. Mức khen thưởng cụ thể theo thông báo từng năm.', 'tham-gia-nghien-cuu-khoa-hoc-co-duoc-cong-diem-hoac-khen-thuong-khong-18', 'da_xuat_ban', 273, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(23, 11, 1, NULL, 'Sinh viên được tham gia nghiên cứu khoa học bằng cách nào?', 'Sinh viên có thể đăng ký đề tài theo thông báo hằng năm của Khoa, hoặc tham gia nhóm nghiên cứu cùng giảng viên. Cần có giảng viên hướng dẫn, nộp thuyết minh đề tài và được hội đồng xét duyệt. Sinh viên theo dõi thông báo tại Văn phòng Khoa hoặc website Khoa.', 'sinh-vien-duoc-tham-gia-nghien-cuu-khoa-hoc-bang-cach-nao-19', 'da_xuat_ban', 233, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(24, 5, 1, NULL, 'Có những loại học bổng nào dành cho sinh viên Khoa CNTT?', 'Sinh viên có thể tham gia xét các loại học bổng như: học bổng khuyến khích học tập của Trường, học bổng của doanh nghiệp và tổ chức tài trợ, học bổng dành cho sinh viên có hoàn cảnh khó khăn. Thông tin từng loại học bổng được Khoa và Phòng chức năng thông báo theo từng học kỳ.', 'co-nhung-loai-hoc-bong-nao-danh-cho-sinh-vien-khoa-cntt-20', 'da_xuat_ban', 101, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(25, 5, 1, NULL, 'Điều kiện xét học bổng khuyến khích học tập là gì?', 'Sinh viên cần đạt kết quả học tập và điểm rèn luyện từ mức quy định trở lên trong học kỳ xét, đăng ký đủ số tín chỉ tối thiểu và không bị kỷ luật. Học bổng được xét theo thứ tự từ cao xuống thấp trong chỉ tiêu của từng ngành. Mức điểm cụ thể theo thông báo của Trường.', 'dieu-kien-xet-hoc-bong-khuyen-khich-hoc-tap-la-gi-21', 'da_xuat_ban', 70, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(26, 5, 1, NULL, 'Hồ sơ xin học bổng gồm những gì và nộp ở đâu?', 'Hồ sơ thường gồm: đơn đề nghị xét học bổng, bảng điểm học kỳ, giấy xác nhận (nếu là học bổng cho sinh viên khó khăn) và các minh chứng thành tích khác (nếu có). Hồ sơ nộp tại Văn phòng Khoa hoặc đơn vị phụ trách đúng thời hạn thông báo.', 'ho-so-xin-hoc-bong-gom-nhung-gi-va-nop-o-dau-22', 'da_xuat_ban', 207, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(27, 12, 1, NULL, 'Thủ tục xin cấp bảng điểm là gì?', 'Sinh viên làm đơn xin cấp bảng điểm (hoặc đăng ký trực tuyến nếu có), nộp tại Phòng Đào tạo/bộ phận một cửa và nộp lệ phí theo quy định. Thời gian giải quyết theo thông báo của Trường; sinh viên nên nêu rõ cần bảng điểm tiếng Việt hay tiếng Anh và số bản cần cấp.', 'thu-tuc-xin-cap-bang-diem-la-gi-23', 'da_xuat_ban', 174, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(28, 12, 1, NULL, 'Làm sao để xin chuyển ngành hoặc chuyển lớp?', 'Sinh viên làm đơn nêu lý do, có ý kiến của cố vấn học tập và Khoa nơi đi và nơi đến, nộp tại Phòng Đào tạo trong thời gian Trường thông báo. Việc chuyển ngành, chuyển lớp phụ thuộc vào chỉ tiêu và điều kiện xét của từng ngành.', 'lam-sao-de-xin-chuyen-nganh-hoac-chuyen-lop-24', 'da_xuat_ban', 295, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(29, 12, 1, NULL, 'Cần làm gì khi mất thẻ sinh viên?', 'Sinh viên làm đơn xin cấp lại thẻ, kèm giấy tờ tùy thân và ảnh theo yêu cầu, nộp tại bộ phận phụ trách thẻ sinh viên của Trường và nộp lệ phí cấp lại. Trong thời gian chờ, có thể xin giấy xác nhận sinh viên tạm thời.', 'can-lam-gi-khi-mat-the-sinh-vien-25', 'da_xuat_ban', 104, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(30, 12, 1, NULL, 'Thủ tục xin giấy xác nhận vay vốn sinh viên hoặc giấy xác nhận hoãn nghĩa vụ quân sự là gì?', 'Sinh viên điền mẫu đơn xin xác nhận (mẫu đăng tại bộ phận một cửa hoặc website của Trường), nộp kèm bản sao giấy tờ cần thiết tại Phòng Công tác sinh viên hoặc bộ phận được phân công. Thời gian nhận kết quả theo hẹn trên phiếu; nên nộp trước thời hạn cần dùng ít nhất vài ngày làm việc.', 'thu-tuc-xin-giay-xac-nhan-vay-von-sinh-vien-hoac-giay-xac-nhan-hoan-ng-26', 'da_xuat_ban', 74, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(31, 12, 1, NULL, 'Cách làm đơn gia hạn chi phí học tập?', 'Sinh viên có khó khăn tài chính làm đơn gia hạn nộp học phí nêu rõ lý do và thời gian đề nghị gia hạn, có xác nhận của Khoa hoặc cố vấn học tập, kèm giấy tờ minh chứng nếu có, nộp tại Phòng Kế hoạch – Tài chính trước hạn nộp học phí.', 'cach-lam-don-gia-han-chi-phi-hoc-tap-27', 'da_xuat_ban', 165, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(32, 13, 1, NULL, 'Điểm rèn luyện được tính như thế nào?', 'Điểm rèn luyện được đánh giá theo thang 100 điểm gồm các tiêu chí: ý thức học tập, chấp hành nội quy và quy chế, tham gia hoạt động chính trị – xã hội – văn hóa – thể thao, quan hệ cộng đồng và công tác lớp. Sinh viên tự đánh giá, sau đó lớp, cố vấn học tập và Khoa xét duyệt vào cuối mỗi học kỳ.', 'diem-ren-luyen-duoc-tinh-nhu-the-nao-28', 'da_xuat_ban', 232, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(33, 13, 1, NULL, 'Có những cuộc thi học thuật, lập trình nào dành cho sinh viên?', 'Hằng năm Khoa và Trường tổ chức hoặc giới thiệu sinh viên tham gia các cuộc thi như: thi lập trình, Olympic tin học, thi ý tưởng khởi nghiệp, hackathon và các cuộc thi học thuật do doanh nghiệp, tổ chức tài trợ. Thông tin cụ thể được đăng trên website Khoa và các kênh thông báo của lớp.', 'co-nhung-cuoc-thi-hoc-thuat-lap-trinh-nao-danh-cho-sinh-vien-29', 'da_xuat_ban', 280, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(34, 13, 1, NULL, 'Tham gia hoạt động có được tính điểm rèn luyện hoặc ngày công tác xã hội không?', 'Các hoạt động do Trường, Khoa, Đoàn – Hội tổ chức có thể được cộng điểm rèn luyện và ghi nhận ngày công tác xã hội theo hướng dẫn của từng hoạt động. Sinh viên giữ lại giấy xác nhận hoặc đăng ký trong danh sách tham gia để được ghi nhận.', 'tham-gia-hoat-dong-co-duoc-tinh-diem-ren-luyen-hoac-ngay-cong-tac-xa-h-30', 'da_xuat_ban', 87, '2026-11-04 21:45:11', '2026-11-04 21:45:11'),
	(35, 11, 1, NULL, 'có bạn nào muốn tham gia NCKH cùng với thầy không?', '', 'co-ban-nao-muon-tham-gia-nckh-cung-voi-thay-khong', 'da_xuat_ban', 4, '2026-11-07 00:38:31', '2026-11-07 00:38:31'),
	(36, 13, 1, 6, 'trường sắp có hoạt động về nguồn cần khảo sát số lượng tham gia', '', 'truong-sap-co-hoat-dong-ve-nguon-can-khao-sat-so-luong-tham-gia', 'da_xuat_ban', 5, '2026-11-07 01:00:38', '2026-11-07 01:00:38'),
	(37, 1, 1, 7, 'haaaaaaaaaaaaaa?', '', 'haaaaaaaaaaaaaa', 'da_xuat_ban', 16, '2026-11-07 02:37:38', '2026-11-07 02:37:38'),
	(38, 3, 1, 8, 'phòng C71.206 có máy bị hư hỏng', '', 'phong-c71206-co-may-bi-hu-hong', 'da_xuat_ban', 26, '2026-11-07 23:37:20', '2026-11-07 23:37:20');

-- Dumping structure for table faq_it.faq_tra_loi
CREATE TABLE IF NOT EXISTS `faq_tra_loi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faq_id` bigint unsigned NOT NULL,
  `nguoi_tra_loi_id` bigint unsigned DEFAULT NULL,
  `noi_dung` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tra_loi` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_faq_id` (`faq_id`),
  KEY `idx_nguoi_tra_loi_id` (`nguoi_tra_loi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.faq_tra_loi: ~13 rows (approximately)
INSERT INTO `faq_tra_loi` (`id`, `faq_id`, `nguoi_tra_loi_id`, `noi_dung`, `ngay_tra_loi`) VALUES
	(1, 14, 3, 'không nợ môn', '2026-11-06 00:46:57'),
	(2, 14, 2, 'làm xong csn và cn', '2026-11-06 00:58:17'),
	(3, 5, 5, 'k nợ môn nha e', '2026-11-07 01:32:58'),
	(4, 9, 5, 'xem chương trình đào tạo nha e', '2026-11-07 01:33:33'),
	(5, 37, 1, 'không biết', '2026-11-07 23:08:49'),
	(6, 37, 1, 'không biết nha<div><br></div>', '2026-11-07 23:08:59'),
	(7, 38, 2, 'woow wowwww', '2026-11-07 23:42:00'),
	(8, 38, 2, 'hahahah', '2026-11-08 20:49:56'),
	(9, 36, 2, 'lumbulaba', '2026-11-08 20:52:28'),
	(10, 15, 2, 'xem tep nha', '2026-11-08 21:03:57'),
	(11, 9, 5, 'hmmmmmmmm', '2026-11-08 21:28:24'),
	(12, 15, 5, 'hay nha hay nha', '2026-11-08 21:29:37'),
	(13, 36, 5, 'ok em&nbsp;', '2026-11-09 04:34:41');

-- Dumping structure for table faq_it.faq_tra_loi_tep
CREATE TABLE IF NOT EXISTS `faq_tra_loi_tep` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_loi_id` bigint unsigned NOT NULL,
  `ten_goc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duong_dan` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kich_thuoc` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tra_loi_id` (`tra_loi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.faq_tra_loi_tep: ~2 rows (approximately)
INSERT INTO `faq_tra_loi_tep` (`id`, `tra_loi_id`, `ten_goc`, `duong_dan`, `kich_thuoc`, `created_at`) VALUES
	(1, 9, 'Screenshot 2026-11-08 154357.png', 'tra-loi/DbQl77MyzvwIC6xMsDxJGKBWBdMcbg3zzBPOltx6.png', 129112, '2026-11-08 20:52:28'),
	(2, 10, 'Screenshot 2026-11-08 154357.png', 'tra-loi/2FKN8VTAktQVCIz16UV4izmPvGaxSqubx50FloJG.png', 129112, '2026-11-08 21:03:57');

-- Dumping structure for table faq_it.mau_tra_loi
CREATE TABLE IF NOT EXISTS `mau_tra_loi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `tieu_de` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mau_tra_loi_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.mau_tra_loi: ~5 rows (approximately)
INSERT INTO `mau_tra_loi` (`id`, `user_id`, `tieu_de`, `noi_dung`, `created_at`) VALUES
	(1, NULL, 'Xác nhận đã nhận câu hỏi', '<p>Chào em,</p><p>Cảm ơn em đã gửi câu hỏi. Khoa đã tiếp nhận và sẽ phản hồi chi tiết trong thời gian sớm nhất.</p><p>Trân trọng.</p>', '2026-11-06 05:29:52'),
	(2, NULL, 'Liên hệ phòng Đào tạo', '<p>Chào em,</p><p>Vấn đề này thuộc thẩm quyền của Phòng Đào tạo. Em vui lòng liên hệ trực tiếp Phòng Đào tạo để được hướng dẫn cụ thể.</p><p>Trân trọng.</p>', '2026-11-06 05:29:52'),
	(3, NULL, 'Đăng ký học phần', '<p>Chào em,</p><p>Em thực hiện theo các bước sau:</p><ol><li>Đăng nhập cổng thông tin sinh viên.</li><li>Chọn mục <b>Đăng ký học phần</b>.</li><li>Chọn học phần và lớp phù hợp, sau đó xác nhận.</li></ol><p>Nếu gặp lỗi, em gửi lại ảnh chụp màn hình để thầy/cô hỗ trợ.</p>', '2026-11-06 05:29:52'),
	(4, NULL, 'Cần bổ sung thông tin', '<p>Chào em,</p><p>Để hỗ trợ chính xác, em vui lòng bổ sung các thông tin sau:</p><ul><li>Mã số sinh viên và lớp</li><li>Mô tả cụ thể vấn đề đang gặp</li><li>Ảnh chụp minh chứng (nếu có)</li></ul>', '2026-11-06 05:29:52'),
	(5, 3, 'mau 1', '<p>Chào em,</p><p>Vấn đề này thuộc thẩm quyền của Phòng Đào tạo. Em vui lòng liên hệ trực tiếp Phòng Đào tạo để được hướng dẫn cụ thể.</p><p>Trân trọng.</p>', '2026-11-05 23:38:22');

-- Dumping structure for table faq_it.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.migrations: ~9 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_10_28_092831_add_vai_tro_to_users_table', 1),
	(5, '2026_10_28_102920_create_faq_tables', 1),
	(6, '2026_11_05_085730_update_cau_hoi_add_giang_vien_columns', 2),
	(7, '2026_11_06_050446_add_anh_dai_dien_to_users_table', 3),
	(8, '2026_11_06_052922_create_reply_feature_tables', 4),
	(9, '2026_11_06_140000_tao_bang_phuc_vu_tra_loi_giang_vien', 5);

-- Dumping structure for table faq_it.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table faq_it.tap_dinh_kem
CREATE TABLE IF NOT EXISTS `tap_dinh_kem` (
  `tep_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faq_id` bigint unsigned NOT NULL,
  `nguoi_tao_id` bigint unsigned NOT NULL,
  `ten_tep` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duong_dan` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai_tep` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dung_luong` bigint unsigned NOT NULL DEFAULT '0',
  `ngay_tao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tep_id`),
  KEY `tap_dinh_kem_faq_id_foreign` (`faq_id`),
  KEY `tap_dinh_kem_nguoi_tao_id_foreign` (`nguoi_tao_id`),
  CONSTRAINT `tap_dinh_kem_faq_id_foreign` FOREIGN KEY (`faq_id`) REFERENCES `faq` (`faq_id`) ON DELETE CASCADE,
  CONSTRAINT `tap_dinh_kem_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.tap_dinh_kem: ~0 rows (approximately)

-- Dumping structure for table faq_it.thong_bao
CREATE TABLE IF NOT EXISTS `thong_bao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tieu_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noi_dung` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lien_ket` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `da_doc` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `thong_bao_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.thong_bao: ~6 rows (approximately)
INSERT INTO `thong_bao` (`id`, `user_id`, `tieu_de`, `noi_dung`, `lien_ket`, `da_doc`, `created_at`) VALUES
	(1, 2, 'Câu hỏi của bạn đã được trả lời', 'Giảng viên B đã trả lời câu hỏi: đăng ký môn học như thế nào ?', '/cau-hoi-cua-toi#ch-3', 1, '2026-11-05 23:39:07'),
	(2, 2, 'Câu hỏi của bạn đã được trả lời', 'TS. Thạch Kọng Saoane đã trả lời câu hỏi: đăng ký môn học như thế nào ?', '/cau-hoi-cua-toi#ch-4', 1, '2026-11-06 23:42:47'),
	(3, 5, 'Câu hỏi của bạn đã được duyệt', 'Câu hỏi "có bạn nào muốn tham gia NCKH cùng với thầy không?" đã được đăng lên danh sách FAQ.', '/faqs/35', 1, '2026-11-07 00:38:31'),
	(4, 5, 'Câu hỏi của bạn đã được duyệt', 'Câu hỏi "trường sắp có hoạt động về nguồn cần khảo sát số lượng tham gia" đã được duyệt và đăng lên danh sách FAQ. Nhấn để xem.', '/faqs', 1, '2026-11-07 01:00:38'),
	(5, 5, 'Câu hỏi của bạn đã được trả lời', 'TS. Nguyễn Nhứt Lam đã trả lời câu hỏi: haaaaaaaaaaaaaa?', '/cau-hoi-cua-toi#ch-7', 0, '2026-11-07 02:39:51'),
	(6, 5, 'Câu hỏi của bạn đã được trả lời', 'ThS. Phan Thị Phương Nam đã trả lời câu hỏi: phòng C71.206 có máy bị hư hỏng', '/faqs/38', 1, '2026-11-07 23:39:40');

-- Dumping structure for table faq_it.tu_khoa_tim_kiem
CREATE TABLE IF NOT EXISTS `tu_khoa_tim_kiem` (
  `tu_khoa_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint unsigned DEFAULT NULL,
  `tu_khoa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tim` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tu_khoa_id`),
  KEY `tu_khoa_tim_kiem_nguoi_dung_id_foreign` (`nguoi_dung_id`),
  CONSTRAINT `tu_khoa_tim_kiem_nguoi_dung_id_foreign` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.tu_khoa_tim_kiem: ~0 rows (approximately)

-- Dumping structure for table faq_it.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vai_tro` enum('sinh_vien','giang_vien','quan_tri') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sinh_vien',
  `trang_thai` tinyint NOT NULL DEFAULT '1',
  `anh_dai_dien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table faq_it.users: ~21 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `vai_tro`, `trang_thai`, `anh_dai_dien`) VALUES
	(1, 'Quản trị viên', 'admin@faq.test', NULL, '$2y$12$BjFMWgIty49rLdPRHnuIlOIEE.Hv8wqTC5y2Tc3aMsf9ihCh9amle', NULL, '2026-10-28 03:32:29', '2026-11-05 01:58:45', 'quan_tri', 1, ''),
	(2, 'Sinh viên A', 'sinhvien@faq.test', NULL, '$2y$12$HL3B.WLFnMvxSWsnKyTGrenyd9MTRPCBJQmAOhbMWafTXBQcOnlZW', NULL, '2026-10-28 03:32:30', '2026-11-05 01:58:57', 'sinh_vien', 1, ''),
	(3, 'Giảng viên B', 'giangvien@faq.test', NULL, '$2y$12$c4jz5AVPD5jY2pgL7LFXcujgNRMVBiauy925FgcbObg8z2FIjWyja', NULL, '2026-10-28 03:32:30', '2026-11-05 01:59:16', 'giang_vien', 1, ''),
	(4, 'TS. Nguyễn Nhứt Lam', 'lamnn@tvu.edu.vn', '2026-11-06 01:30:08', '$2y$12$2KMV6RRvjqFbbB3Ky6QtUuVFIuLNNS.KaqeQWW7lY/C.j11OEgYZ2', NULL, '2026-11-06 01:30:08', '2026-11-06 01:30:08', 'giang_vien', 1, 'giang-vien/Lam.png'),
	(5, 'TS. Thạch Kọng Saoane', 'oane@tvu.edu.vn', '2026-11-06 01:30:08', '$2y$12$UvmBlUzIBe7njTC113iuTOgdss7AAsHRr.THuB3lOcI0Ht66Lugcq', NULL, '2026-11-06 01:30:08', '2026-11-06 01:30:08', 'giang_vien', 1, 'giang-vien/Kong.jpg'),
	(6, 'TS. Nguyễn Trần Diễm Hạnh', 'diemhanh_tvu@tvu.edu.vn', '2026-11-06 01:30:08', '$2y$12$cxTsJUVsGfhwgUnOHeSQx.nZ6H/4svN1xrFJa.55Vni0rKS4Omc.C', NULL, '2026-11-06 01:30:08', '2026-11-06 01:30:08', 'giang_vien', 1, 'giang-vien/Hanh.png'),
	(7, 'ThS. Nguyễn Bá Nhiệm', 'nhiemnb@tvu.edu.vn', '2026-11-06 01:30:09', '$2y$12$cHQlAMWoQXYw0KUXlMk6QuWHmrQ9ETg1FYkra7NdM2ES3SFJoveEK', NULL, '2026-11-06 01:30:09', '2026-11-06 01:30:09', 'giang_vien', 1, 'giang-vien/Nhiem.png'),
	(8, 'ThS. Lê Phong Dũ', 'lpdu@tvu.edu.vn', '2026-11-06 01:30:09', '$2y$12$UaV9siGARsZ7a5Y31/bNqOsUmxxRctMryFtFlPP7EKsppWNy1PJJS', NULL, '2026-11-06 01:30:09', '2026-11-06 01:30:09', 'giang_vien', 1, 'giang-vien/Du.png'),
	(9, 'TS. Đoàn Phước Miền', 'phuocmien@tvu.edu.vn', '2026-11-06 01:30:09', '$2y$12$ovfwk86JwFmzB.GkpHFTrOJ8JvTZN2X3VgU8OHkp2ZvhQayLNrNmq', NULL, '2026-11-06 01:30:09', '2026-11-06 01:30:09', 'giang_vien', 1, 'giang-vien/Mien.png'),
	(10, 'ThS. Võ Thành C', 'vothanhc@tvu.edu.vn', '2026-11-06 01:30:10', '$2y$12$6OxqnuSb6hG7dX2WwqcTwOxmj4QDtjg0UlaBTd72Easpmj279xfvq', NULL, '2026-11-06 01:30:10', '2026-11-06 01:30:10', 'giang_vien', 1, 'giang-vien/C.png'),
	(11, 'ThS. Trịnh Quốc Việt', 'tqviettv@tvu.edu.vn', '2026-11-06 01:30:10', '$2y$12$3ADb9Sc2Ma40x8iuMoiHUepTn68Uk.xmMk41s2usWpQsvMZDo3YnK', NULL, '2026-11-06 01:30:10', '2026-11-06 01:30:10', 'giang_vien', 1, 'giang-vien/Viet.png'),
	(12, 'ThS. Trần Văn Nam', 'namtv@tvu.edu.vn', '2026-11-06 01:30:10', '$2y$12$VOFIYg75Vm4V.NvVwKooJeSbWOvGctenPXuY8watsKiFG3Qv13/EW', NULL, '2026-11-06 01:30:10', '2026-11-06 01:30:10', 'giang_vien', 1, 'giang-vien/Namtran.png'),
	(13, 'ThS. Trầm Hoàng Nam', 'tramhoangnam@tvu.edu.vn', '2026-11-06 01:30:11', '$2y$12$RmgLDd2Z8srVRKU7oIRfP.xd8kRT8tov.A9pw5Ibf7/1l2sMjGezi', NULL, '2026-11-06 01:30:11', '2026-11-06 01:30:11', 'giang_vien', 1, 'giang-vien/Namtram.png'),
	(14, 'ThS. Phan Thị Phương Nam', 'ptpnam@tvu.edu.vn', '2026-11-06 01:30:11', '$2y$12$zWH/N2W04yyOev.VByVkweEtff/2AT9uxpNMGJqkIjux/JKiAWy7W', NULL, '2026-11-06 01:30:11', '2026-11-07 23:49:23', 'giang_vien', 1, 'giang-vien/PNam.png'),
	(15, 'ThS. Phạm Thị Trúc Mai', 'pttmai@tvu.edu.vn', '2026-11-06 01:30:11', '$2y$12$LTCes5L036I79u4mlcq8xeTV3HGKIeZpEKUP3k2jCh1w2k6s/B89u', NULL, '2026-11-06 01:30:11', '2026-11-06 01:30:11', 'giang_vien', 1, 'giang-vien/Mai.png'),
	(16, 'ThS. Nguyễn Thừa Phát Tài', 'phattai@tvu.edu.vn', '2026-11-06 01:30:12', '$2y$12$7KtktXAaibvo59B9fxH9QeTZq2.vPLNczFUEVuWnYUAEY6WGwIZNO', NULL, '2026-11-06 01:30:12', '2026-11-06 01:30:12', 'giang_vien', 1, 'giang-vien/Tai.png'),
	(17, 'ThS. Nguyễn Ngọc Đan Thanh', 'ngocdanthanhdt@tvu.edu.vn', '2026-11-06 01:30:12', '$2y$12$jhTGqosiixiYwCiPfDqkAOPKv8qC6XLvuPJACaCQfaxpHes8oiZFm', NULL, '2026-11-06 01:30:12', '2026-11-06 01:30:12', 'giang_vien', 1, 'giang-vien/Thanh.png'),
	(18, 'ThS. Nguyễn Mộng Hiền', 'hientvu@tvu.edu.vn', '2026-11-06 01:30:12', '$2y$12$l9hnGt.iG3d3QpQnqFK1XeTUer7OpyrnfZy4xqlJC5qQYd.Z1hkK2', NULL, '2026-11-06 01:30:12', '2026-11-06 01:30:12', 'giang_vien', 1, 'giang-vien/Hien.png'),
	(19, 'ThS. Nguyễn Hoàng Duy Thiện', 'thiennhd@tvu.edu.vn', '2026-11-06 01:30:13', '$2y$12$h5ptBGgE.SHRT06zGhhUV.gu8mDEfi7ODwPE.Lkj1O/8Q3KQKTB46', NULL, '2026-11-06 01:30:13', '2026-11-06 01:30:13', 'giang_vien', 1, 'giang-vien/Thien.png'),
	(20, 'ThS. Lê Minh Tự', 'lmtu@tvu.edu.vn', '2026-11-06 01:30:13', '$2y$12$6aE3hjBCPoFOf1NTuktDCeYDDdsDmmCbHs0NJkqDmv64ZlLFICT0e', NULL, '2026-11-06 01:30:13', '2026-11-06 01:30:13', 'giang_vien', 1, 'giang-vien/Tu.png'),
	(21, 'ThS. Phạm Minh Đương', 'duongminh@tvu.edu.vn', '2026-11-06 01:30:50', '$2y$12$pvLhMRjXQfHiwIIEW3RKnOCn9gAPsQNsO6v3nI3z20l.LOF1gcAMG', NULL, '2026-11-06 01:30:50', '2026-11-06 01:30:50', 'giang_vien', 1, 'giang-vien/Duong.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
