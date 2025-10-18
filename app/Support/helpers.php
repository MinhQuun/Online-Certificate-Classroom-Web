<?php

if (!function_exists('student_asset_version')) {
    function student_asset_version(string $relativePath): int
    {
        $fullPath = public_path($relativePath);
        return file_exists($fullPath) ? filemtime($fullPath) : time();
    }
}

