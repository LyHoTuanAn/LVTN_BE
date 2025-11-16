<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Lấy message theo ngôn ngữ hiện tại
     *
     * @param string $key
     * @param string $type 'errors' hoặc 'success'
     * @param array $replace
     * @return string
     */
    public static function get(string $key, string $type = 'errors', array $replace = []): string
    {
        return __("{$type}.{$key}", $replace);
    }

    /**
     * Lấy error message
     *
     * @param string $key
     * @param array $replace
     * @return string
     */
    public static function error(string $key, array $replace = []): string
    {
        return self::get($key, 'errors', $replace);
    }

    /**
     * Lấy success message
     *
     * @param string $key
     * @param array $replace
     * @return string
     */
    public static function success(string $key, array $replace = []): string
    {
        return self::get($key, 'success', $replace);
    }
}


