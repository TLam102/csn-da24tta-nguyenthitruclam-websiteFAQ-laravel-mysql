<a href="{{ route('giangvien.cauhoi.show', $c->cau_hoi_pk) }}"
   class="gv-item {{ $c->giang_vien_da_xem ? '' : 'is-new' }}">
    <div class="gv-item-top">
        <h3>
            @unless ($c->giang_vien_da_xem)
                <span class="gv-tag gv-tag-new">Mới</span>
            @endunless
            {{ $c->tieu_de }}
        </h3>
        <span class="gv-time">{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') }}</span>
    </div>

    <p class="gv-excerpt">{{ \Illuminate\Support\Str::limit($c->noi_dung, 150) }}</p>

    <div class="gv-meta">
        <span><i class="fa-solid fa-user"></i> {{ $c->nguoi_gui ?? 'Ẩn danh' }}</span>
        <span><i class="fa-solid fa-folder"></i> {{ $c->tendanhmuc ?? 'Chưa phân loại' }}</span>
        @if ($c->so_tra_loi > 0)
            <span class="gv-tag gv-tag-done"><i class="fa-solid fa-check"></i> Đã trả lời</span>
        @else
            <span class="gv-tag gv-tag-wait"><i class="fa-regular fa-clock"></i> Chưa trả lời</span>
        @endif
    </div>
</a>