<?php
if (!function_exists('generate_uuid_string')) {
    function generate_uuid_string(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('generate_secure_password')) {
    function generate_secure_password(int $length): string
    {
        return bin2hex(random_bytes($length));
    }
}
