<?php

if (! function_exists('file_url')) {
    /**
     * Trả về đường dẫn công khai của một tệp/ảnh.
     *
     * - URL đầy đủ (http/https/"//") : giữ nguyên, riêng http sẽ được nâng lên https
     *   khi web chạy HTTPS (tránh Mixed Content làm iPhone/Safari không tải được ảnh).
     * - Tệp có sẵn trong public/ (ảnh giảng viên, tệp cũ) : asset($path)
     * - Tệp tải lên mới (storage/app/public)               : asset('storage/' . $path)
     */
    function file_url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        // 1) Đã là URL đầy đủ
        if (preg_match('#^(https?:)?//#i', $path)) {
            if (str_starts_with($path, '//')) {
                $path = 'https:' . $path;
            }

            return _file_url_https($path);
        }

        // 2) Đường dẫn tương đối
        $path = ltrim(str_replace('\\', '/', $path), '/');

        // Mã hóa tên tệp có dấu/khoảng trắng (bỏ qua nếu đã được mã hóa sẵn)
        if (! preg_match('/%[0-9A-Fa-f]{2}/', $path)) {
            $path = implode('/', array_map('rawurlencode', explode('/', $path)));
        }

        // Tệp có sẵn trong public/ (ảnh giảng viên, tệp cũ)
        if (is_file(public_path(rawurldecode($path)))) {
            return _file_url_https(asset($path));
        }

        // Tệp tải lên mới (storage/app/public)
        return _file_url_https(asset('storage/' . $path));
    }
}

if (! function_exists('_file_url_https')) {
    /** Ép về https khi trang đang chạy HTTPS hoặc ở môi trường production. */
    function _file_url_https(string $url): string
    {
        $chayHttps = config('app.env') === 'production'
            || (function_exists('request') && request()->isSecure());

        return $chayHttps ? preg_replace('#^http://#i', 'https://', $url) : $url;
    }
}