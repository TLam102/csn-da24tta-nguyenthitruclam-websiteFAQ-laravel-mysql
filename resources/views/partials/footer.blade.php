<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">

            {{-- Cột 1: Thông tin --}}
            <div class="footer-col">
                <h4>Thông tin</h4>
                <p><strong>🏫 TRƯỜNG KỸ THUẬT VÀ CÔNG NGHỆ</strong></p>
                <p><i>🌐 College of Engineering and Technology (CET)</i></p>
                <p class="dash">------------------------------------------------</p>
                <p><strong>💡 Triết lý đào tạo:</strong></p>
                <p>🎓 “Thực học – Thực hành – Thực nghiệp – Thích ứng – Tiên phong”</p>
            </div>

            {{-- Cột 2: Liên hệ --}}
            <div class="footer-col">
                <h4>Liên hệ</h4>
                <p><strong>📧 Email:</strong> <a href="mailto:ktcn@tvu.edu.vn">ktcn@tvu.edu.vn</a></p>
                <p><strong>☎️ Điện thoại:</strong> +084. 2943 855 246 (135)</p>
                <p><strong>📱 Liên hệ:</strong> +084. 2943 855 246 (203)</p>
                <p><strong>📍 Địa chỉ:</strong> số 126 Nguyễn Thiện Thành, phường Hòa Thuận, tỉnh Vĩnh Long</p>
            </div>

            {{-- Cột 3: Liên kết --}}
            <div class="footer-col">
                <h4>Liên kết</h4>
                <ul class="footer-links">
                    <li><a href="#">☆ Ban Đào tạo</a></li>
                    <li><a href="#">☆ Ban Tài chính</a></li>
                    <li><a href="#">☆ Cổng thông tin học viên</a></li>
                    <li><a href="#">☆ Cổng thông tin sinh viên</a></li>
                    <li><a href="#">☆ Ban Công tác sinh viên - học sinh</a></li>
                </ul>
            </div>

            {{-- Cột 4: Kết nối --}}
            <div class="footer-col">
                <h4>Kết nối với CET</h4>
                <p><strong>📘 Facebook:</strong><br>
                    <a href="https://www.facebook.com/tvuCET" target="_blank" rel="noopener">https://www.facebook.com/tvuCET</a></p>
                <p><strong>💬 Zalo:</strong><br>
                    <a href="https://zalo.me/2794274070052188270" target="_blank" rel="noopener">https://zalo.me/2794274070052188270</a></p>
                <p><strong>▶️ YouTube:</strong><br>
                    <a href="https://youtube.com/@TruongKythuatCongnghe_TVU_CET" target="_blank" rel="noopener">https://youtube.com/@TruongKythuatCongnghe_TVU_CET</a></p>
            </div>

        </div>

        <div class="footer-bottom">
            Trường Kỹ thuật và Công nghệ - Đại học Trà Vinh © {{ date('Y') }} cet.tvu.edu.vn
        </div>
    </div>

    {{-- Nút lên đầu trang --}}
    <button type="button" id="btnScrollTop" class="scroll-top" aria-label="Lên đầu trang">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
</footer>

<script>
    (function () {
        var btn = document.getElementById('btnScrollTop');
        window.addEventListener('scroll', function () {
            btn.classList.toggle('show', window.scrollY > 200);
        });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
</script>