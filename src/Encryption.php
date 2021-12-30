<?php

namespace encryption;


class Encryption
{

    const DECOLLATOR = 'a';

    /**
     * 加密
     * @param string $original
     * @param int $offset
     * @return string
     */
    public static function encrypt(string $original = '', int $offset = 0)
    {
        $offset = $offset == 0 ? rand(1, 15) : $offset;
        $offset = abs($offset);
        if (empty($original)) {
            return $original;
        }
        $len = strlen($original);
        $encrypt = '';
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($original[$i]) + $offset;
            $encrypt .= chr($ascii);
        }
        $eq = $offset * $offset;
        $length = strlen($eq);
        $base = base64_encode($encrypt);
        $base = str_replace(array('+', '/'), array('-', '_'), $base);
        if (substr($base, -1, 1) != '=') {
            return $length . self::DECOLLATOR . $base . $eq;
        } else {
            return $length . self::DECOLLATOR . $eq . $base;
        }
    }

    /**
     * 解密
     * @param string $hash
     * @return string
     */
    public static function decrypt(string $hash = '')
    {
        if (empty($hash)) {
            return $hash;
        }
        $andIndex = strpos($hash, self::DECOLLATOR);
        $numberLength = substr($hash, 0, $andIndex);
        if (substr($hash, -1, 1) === '=') {
            $number = substr($hash, $andIndex + 1, $numberLength);
            $original = substr($hash, $andIndex + 1 + $numberLength);
        } else {
            $number = substr($hash, strlen($hash) - $numberLength, $numberLength);
            $original = substr($hash, $andIndex + 1, strlen($hash) - $numberLength - $andIndex);
        }
        $offset = sqrt($number);
        $original = str_replace(array('-', '_'), array('+', '/'), $original);
        $mod4 = strlen($original) % 4;
        if ($mod4) {
            $original .= substr('====', $mod4);
        }
        $original = base64_decode($original);

        $len = strlen($original);
        $string = '';
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($original[$i]) - $offset;
            $string .= chr($ascii);
        }

        return $string;
    }
}
