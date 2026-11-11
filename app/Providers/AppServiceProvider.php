<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ép dùng HTTPS khi chạy production (sửa lỗi Mixed Content)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.admin');

        View::composer('layouts.app', function ($view) {
            $gvUnread = 0;
            $gvShowNotice = false;

            if (Auth::check() && Auth::user()->vai_tro === 'giang_vien') {
                $gvUnread = DB::table('cau_hoi')
                    ->where('giang_vien_id', Auth::id())
                    ->where('giang_vien_da_xem', false)
                    ->where('trang_thai', '!=', 'bi_tu_choi')
                    ->count();

                if ($gvUnread > 0 && !session()->has('gv_notice_shown')) {
                    $gvShowNotice = true;
                    session()->put('gv_notice_shown', true);
                }
            }

            $view->with(compact('gvUnread', 'gvShowNotice'));
        });
    }
}