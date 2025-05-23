<?php

if (!function_exists('number_to_uuid')) {
    function number_to_uuid(int|string $number): string
    {
        $hex = str_pad(dechex($number), 32, '0', STR_PAD_LEFT);
        return substr($hex, 0, 8) . '-' .
            substr($hex, 8, 4) . '-' .
            substr($hex, 12, 4) . '-' .
            substr($hex, 16, 4) . '-' .
            substr($hex, 20);
    }
}

if (!function_exists('uuid_to_number')) {
    function uuid_to_number(string $uuid): float|int
    {
        $hex = str_replace('-', '', $uuid);
        return hexdec(ltrim($hex, '0'));
    }
}
