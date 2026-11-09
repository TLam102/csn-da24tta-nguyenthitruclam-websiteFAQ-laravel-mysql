<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuanTriVien
{
    // Giá trị cột users.vai_tro của quản trị viên (sửa lại nếu CSDL của bạn dùng chữ khác)
    public const VAI_TRO = ['admin', 'quan_tri', 'quan_tri_vien', 'quantri'];

    public function handle(Request $request, Closure $next): Response
    {
        $vaiTro = $request->user()->vai_tro ?? '';

        abort_unless(
            in_array($vaiTro, self::VAI_TRO, true),
            403,
            'Bạn không có quyền truy cập khu vực quản trị.'
        );

        return $next($request);
    }
}