<?php

namespace App\Services;

/**
 * https://github.com/recurly/druuid
 */
class UniqId
{
    const OPEN_GOV_EPOCH_UNIX_OFFSET_SECONDS = 1325376000;

    public static function gen()
    {
        $ourMilliseconds = round((microtime(true) - self::OPEN_GOV_EPOCH_UNIX_OFFSET_SECONDS) * 1000);
        $randomBytes = random_int(0, pow(2, 64 - 41));

        $id = $ourMilliseconds << (64 - 41);
        $id = $id | $randomBytes;

        return $id;
    }

    public static function genB64()
    {
        return static::b64(static::gen());
    }

    /**
     * Derived from:
     * https://stackoverflow.com/a/31876526
     */
    public static function b64($num)
    {
        $hex = base_convert($num, 10, 16);
        $base64 = base64_encode(pack('H*', $hex));
        $base64 = str_replace(['/', '+'], ['_', '-'], $base64);
        $base64 = rtrim($base64, '='); // Remove the padding '='
        return $base64;
    }
}
