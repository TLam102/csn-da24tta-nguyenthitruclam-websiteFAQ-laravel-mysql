@extends('layouts.app')

@section('content')
<div class="container list-page">
    <div class="list-layout">

        {{-- ===== Thanh bên quản trị (dùng lại class .list-sidebar của style.css: xanh lá – vàng) ===== --}}
        <aside class="list-sidebar">
            <div class="sidebar-head">
                <h3><i class="fa-solid fa-user-shield" style="color: var(--primary); margin-right: 6px;"></i>Quản trị</h3>
            </div>
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i> Tổng quan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.faq.index') }}" class="{{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-question"></i> Quản lý FAQ
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.danhmuc.index') }}" class="{{ request()->routeIs('admin.danhmuc.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-open"></i> Quản lý danh mục
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.tapdinhkem.index') }}" class="{{ request()->routeIs('admin.tapdinhkem.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-paperclip"></i> Quản lý tệp đính kèm
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.cauhoi.index') }}" class="{{ request()->routeIs('admin.cauhoi.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-envelope-open-text"></i> Câu hỏi sinh viên
                    </a>
                </li>
            </ul>
        </aside>

        {{-- ===== Nội dung từng trang quản trị ===== --}}
        <div class="admin-content" style="min-width: 0;">
            @yield('admin')
        </div>

    </div>
</div>
@endsection