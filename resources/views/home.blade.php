@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')

@php
    // Ảnh: đổi tên file nếu bạn có ảnh riêng (đặt trong public/images)
    $banners    = ['banner1.jpg', 'banner2.jpg'];
    $introImage = 'banner2.jpg';      // ảnh khu học xá bên mục Giới thiệu
    $topicBg    = 'banner1.jpg';      // ảnh nền mục Chủ đề phổ biến

    // Thẻ mặc định (slug khớp slug danh mục trong CSDL thì dùng đúng icon + mô tả này)
    $defaultCards = [
        ['name' => 'Đào tạo',              'slug' => 'dao-tao',              'icon' => 'fa-graduation-cap', 'desc' => 'Thông tin về chương trình đào tạo, tín chỉ, đăng ký học phần...'],
        ['name' => 'Học vụ',               'slug' => 'hoc-vu',               'icon' => 'fa-laptop',         'desc' => 'Thủ tục hành chính, đăng ký học phần, kết quả học tập...'],
        ['name' => 'Thực tập',             'slug' => 'thuc-tap',             'icon' => 'fa-briefcase',      'desc' => 'Quy định thực tập, đăng ký thực tập, giấy xác nhận...'],
        ['name' => 'Tốt nghiệp',           'slug' => 'tot-nghiep',           'icon' => 'fa-trophy',         'desc' => 'Điều kiện tốt nghiệp, thủ tục và thời gian xét tốt nghiệp...'],
        ['name' => 'Nghiên cứu khoa học',  'slug' => 'nghien-cuu-khoa-hoc',  'icon' => 'fa-flask',          'desc' => 'Hoạt động nghiên cứu khoa học, đề tài và hướng dẫn thực hiện...'],
        ['name' => 'Học bổng',             'slug' => 'hoc-bong',             'icon' => 'fa-award',          'desc' => 'Các loại học bổng, điều kiện xét và thời gian nộp hồ sơ...'],
        ['name' => 'Hoạt động sinh viên',  'slug' => 'hoat-dong-sinh-vien',  'icon' => 'fa-users',          'desc' => 'Câu lạc bộ, đoàn – hội, rèn luyện và các hoạt động phong trào...'],
        ['name' => 'Đồ án',                'slug' => 'do-an',                'icon' => 'fa-diagram-project','desc' => 'Đăng ký đề tài, quy trình thực hiện và báo cáo đồ án...'],
        ['name' => 'Khóa luận',            'slug' => 'khoa-luan',            'icon' => 'fa-book-open',      'desc' => 'Điều kiện làm khóa luận, hướng dẫn trình bày và bảo vệ...'],
    ];
    $byDefault = collect($defaultCards)->keyBy('slug');

    // Danh sách thẻ hiển thị: ưu tiên danh mục trong CSDL, sau đó bổ sung các thẻ mặc định còn thiếu
    $cards = [];
    foreach ($categories as $i => $c) {
        $d = $byDefault->get($c->slug);
        $cards[] = [
            'name' => $c->ten_danh_muc ?? $c->tendanhmuc ?? '',
            'slug' => $c->slug,
            'icon' => $d['icon'] ?? $defaultCards[$i % count($defaultCards)]['icon'],
            'desc' => ($c->mo_ta ?? $c->mota ?? null) ?: ($d['desc'] ?? $defaultCards[$i % count($defaultCards)]['desc']),
        ];
    }
    $existing = collect($cards)->pluck('slug')->all();
    foreach ($defaultCards as $d) {
        if (!in_array($d['slug'], $existing, true)) $cards[] = $d;
    }
@endphp

{{-- ===== 1. BANNER + TÌM KIẾM ===== --}}
<section class="hero-banner" id="heroBanner">
    @foreach ($banners as $i => $img)
        <img src="{{ asset('images/' . $img) }}" alt="Banner {{ $i + 1 }}"
             class="hero-banner-img {{ $i === 0 ? 'active' : '' }}">
    @endforeach
    <div class="hero-banner-overlay"></div>

    <div class="hero-banner-content">
        <h1>Chào mừng đến với<br>Website FAQ Khoa Công nghệ Thông tin</h1>
        <p>Tìm kiếm câu trả lời cho những thắc mắc của bạn</p>

        <form class="hero-search" action="{{ route('faqs.index') }}" method="GET">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" placeholder="Nhập từ khóa cần tìm...">
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>

    <div class="hero-dots">
        @foreach ($banners as $i => $img)
            <button type="button" class="dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" aria-label="Banner {{ $i + 1 }}"></button>
        @endforeach
    </div>
</section>

{{-- ===== 2. GIỚI THIỆU ===== --}}
<section class="intro-section">
    <div class="container intro-grid">
        <div class="intro-media">
            <img src="{{ asset('images/' . $introImage) }}" alt="Khu học xá Khoa Công nghệ Thông tin">
           
        </div>

        <div class="intro-text">
            <span class="pill">Giới thiệu</span>
            <h2>Chào mừng bạn đến với trang hỏi – đáp (FAQ) của Khoa Công nghệ Thông tin</h2>
            <p>
                Website FAQ được xây dựng nhằm cung cấp thông tin nhanh chóng, chính xác về các vấn đề
                học tập, nghiên cứu và hoạt động tại Khoa Công nghệ Thông tin. Tại đây, bạn có thể dễ dàng
                tìm kiếm câu trả lời cho những thắc mắc liên quan đến chương trình đào tạo, học vụ,
                thực tập, đồ án, và nhiều nội dung khác.
            </p>
            <a href="{{ route('faqs.index') }}" class="btn-outline">Tìm hiểu thêm <span>→</span></a>
        </div>
    </div>
</section>

{{-- ===== 3. CHỦ ĐỀ PHỔ BIẾN (CAROUSEL TỰ LƯỚT) ===== --}}
<section class="topics-section" style="--topic-bg: url('{{ asset('images/' . $topicBg) }}');">
    <div class="topics-inner">
        <div class="topics-head">
            <h2>Khám phá các chủ đề phổ biến</h2>
            <p>Duyệt qua các câu hỏi thường gặp hoặc sử dụng thanh tìm kiếm để tìm nhanh hơn</p>
        </div>

        <div class="topic-carousel" id="topicCarousel">
            <button type="button" class="topic-arrow prev" aria-label="Trước"><i class="fa-solid fa-chevron-left"></i></button>

            <div class="topic-track">
                @foreach ($cards as $i => $c)
                    <a href="{{ route('faqs.index', ['danh_muc' => $c['slug']]) }}" class="topic-card c{{ $i % 9 }}">
                        <span class="topic-icon"><i class="fa-solid {{ $c['icon'] }}"></i></span>
                        <h3>{{ $c['name'] }}</h3>
                        <p>{{ $c['desc'] }}</p>
                        <span class="topic-link">Xem chi tiết <b>→</b></span>
                    </a>
                @endforeach
            </div>

            <button type="button" class="topic-arrow next" aria-label="Sau"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="topic-dots"></div>
        </div>
    </div>
</section>

{{-- ===== 4. CÂU HỎI THƯỜNG GẶP (BẤM LÀ XỔ RA CÂU TRẢ LỜI) ===== --}}
<section class="container section faq-home">
    <div class="faq-home-head">
        <h2 class="section-title">Câu hỏi thường gặp</h2>
        <a href="{{ route('faqs.index') }}" class="faq-home-all">Xem tất cả câu hỏi →</a>
    </div>

    <div class="faq-acc" id="faqAcc">
        @forelse ($popularFaqs as $i => $faq)
            @php
                // Nếu cột câu trả lời trong CSDL tên khác, sửa lại tên ở dòng dưới
                $answer = $faq->tra_loi ?? $faq->cau_tra_loi ?? null;
            @endphp
            <details class="faq-acc-item" {{ $i === 0 ? 'open' : '' }}>
                <summary>
                    <span class="q-badge"><i class="fa-solid fa-question"></i></span>
                    <span class="q-text">{{ $faq->cau_hoi }}</span>
                    <i class="fa-solid fa-chevron-down q-arrow"></i>
                </summary>
                <div class="faq-acc-body">
                    @if (!empty($answer))
                        {!! nl2br(e($answer)) !!}
                    @else
                        Câu trả lời đang được cập nhật.
                    @endif
                </div>
            </details>
        @empty
            <details class="faq-acc-item" open>
                <summary>
                    <span class="q-badge"><i class="fa-solid fa-question"></i></span>
                    <span class="q-text">Làm thế nào để đăng ký học phần?</span>
                    <i class="fa-solid fa-chevron-down q-arrow"></i>
                </summary>
                <div class="faq-acc-body">
                    Bạn có thể đăng ký học phần qua cổng thông tin sinh viên của Trường, theo hướng dẫn chi tiết tại mục Học vụ trên website.
                </div>
            </details>
            <details class="faq-acc-item">
                <summary>
                    <span class="q-badge"><i class="fa-solid fa-question"></i></span>
                    <span class="q-text">Quy định về thực tập tốt nghiệp?</span>
                    <i class="fa-solid fa-chevron-down q-arrow"></i>
                </summary>
                <div class="faq-acc-body">
                    Sinh viên đăng ký thực tập theo kế hoạch của Khoa, nộp phiếu đăng ký và giấy xác nhận của đơn vị thực tập đúng thời hạn được thông báo.
                </div>
            </details>
            <details class="faq-acc-item">
                <summary>
                    <span class="q-badge"><i class="fa-solid fa-question"></i></span>
                    <span class="q-text">Học phí và các khoản phí khác như thế nào?</span>
                    <i class="fa-solid fa-chevron-down q-arrow"></i>
                </summary>
                <div class="faq-acc-body">
                    Học phí được tính theo số tín chỉ đăng ký trong từng học kỳ. Vui lòng xem thông báo của Phòng Kế hoạch – Tài chính để biết mức thu cụ thể.
                </div>
            </details>
        @endforelse
    </div>
</section>

<script>
    /* ---------- Slideshow banner ---------- */
    (function () {
        var INTERVAL = 5000;
        var root   = document.getElementById('heroBanner');
        var slides = root.querySelectorAll('.hero-banner-img');
        var dots   = root.querySelectorAll('.dot');
        var current = 0, timer;

        function show(n) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (n + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        }
        function start() { timer = setInterval(function () { show(current + 1); }, INTERVAL); }
        function reset() { clearInterval(timer); start(); }

        dots.forEach(function (d) {
            d.addEventListener('click', function () {
                show(parseInt(d.dataset.index, 10));
                reset();
            });
        });
        if (slides.length > 1) start();
    })();

    /* ---------- Carousel chủ đề: tự lướt ngang, dừng khi rê chuột ---------- */
    (function () {
        var AUTO_MS = 3200;
        var root    = document.getElementById('topicCarousel');
        var track   = root.querySelector('.topic-track');
        var cards   = track.querySelectorAll('.topic-card');
        var dotsBox = root.querySelector('.topic-dots');
        var prev    = root.querySelector('.prev');
        var next    = root.querySelector('.next');
        var timer = null, paused = false;

        function step()   { return cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : 0; }
        function perView(){ return Math.max(1, Math.round(track.clientWidth / (step() || track.clientWidth))); }
        function maxIdx() { return Math.max(0, cards.length - perView()); }
        function curIdx() { return step() ? Math.round(track.scrollLeft / step()) : 0; }
        function goTo(i)  { track.scrollTo({ left: i * step(), behavior: 'smooth' }); }

        function goNext() { var i = curIdx(); goTo(i >= maxIdx() ? 0 : i + 1); }
        function goPrev() { var i = curIdx(); goTo(i <= 0 ? maxIdx() : i - 1); }

        function buildDots() {
            dotsBox.innerHTML = '';
            var n = maxIdx() + 1;
            var scrollable = n > 1;
            prev.style.display = next.style.display = dotsBox.style.display = scrollable ? '' : 'none';
            for (var i = 0; i < n && scrollable; i++) {
                var b = document.createElement('button');
                b.type = 'button';
                b.setAttribute('aria-label', 'Vị trí ' + (i + 1));
                (function (k) { b.addEventListener('click', function () { goTo(k); restart(); }); })(i);
                dotsBox.appendChild(b);
            }
            sync();
        }
        function sync() {
            var idx = curIdx();
            dotsBox.querySelectorAll('button').forEach(function (b, i) {
                b.classList.toggle('active', i === idx);
            });
        }

        function stop()    { clearInterval(timer); timer = null; }
        function begin()   { stop(); if (maxIdx() > 0) timer = setInterval(function () { if (!paused) goNext(); }, AUTO_MS); }
        function restart() { begin(); }

        prev.addEventListener('click', function () { goPrev(); restart(); });
        next.addEventListener('click', function () { goNext(); restart(); });
        root.addEventListener('mouseenter', function () { paused = true; });
        root.addEventListener('mouseleave', function () { paused = false; });
        root.addEventListener('touchstart', function () { paused = true; }, { passive: true });
        root.addEventListener('touchend',   function () { setTimeout(function () { paused = false; }, 2500); }, { passive: true });
        track.addEventListener('scroll', sync);
        window.addEventListener('resize', function () { buildDots(); begin(); });
        document.addEventListener('visibilitychange', function () { document.hidden ? stop() : begin(); });

        buildDots();
        begin();
    })();

    /* ---------- Accordion: mở một câu thì đóng các câu khác ---------- */
    (function () {
        var items = document.querySelectorAll('#faqAcc .faq-acc-item');
        items.forEach(function (d) {
            d.addEventListener('toggle', function () {
                if (!d.open) return;
                items.forEach(function (o) { if (o !== d) o.open = false; });
            });
        });
    })();
</script>

@endsection