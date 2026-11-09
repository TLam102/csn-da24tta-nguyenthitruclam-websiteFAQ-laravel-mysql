@php $imgExt = ['jpg', 'jpeg', 'png', 'webp']; @endphp
<div class="tep-list">
    @foreach ($files as $f)
        @php
            $ext = strtolower(pathinfo($f->ten_goc, PATHINFO_EXTENSION));
            $url = asset('storage/' . $f->duong_dan);
        @endphp

        @if (in_array($ext, $imgExt))
            <a href="{{ $url }}" target="_blank" class="tep-img" title="{{ $f->ten_goc }}">
                <img src="{{ $url }}" alt="{{ $f->ten_goc }}">
                <span>{{ \Illuminate\Support\Str::limit($f->ten_goc, 22) }}</span>
            </a>
        @else
            <a href="{{ $url }}" target="_blank" download class="tep-file" title="{{ $f->ten_goc }}">
                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf' : 'fa-file-lines' }}"></i>
                <span>{{ \Illuminate\Support\Str::limit($f->ten_goc, 28) }}</span>
                <small>{{ number_format(($f->kich_thuoc ?? 0) / 1024, 0) }} KB</small>
            </a>
        @endif
    @endforeach
</div>