<?php

if (! function_exists('file_url')) {
    function file_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $path = ltrim($path, '/');

        // Tệp có sẵn trong public/ (ảnh giảng viên, tệp cũ)
        if (is_file(public_path($path))) {
            return asset($path);
        }

        // Tệp tải lên mới (storage/app/public)
        return asset('storage/' . $path);
    }
}