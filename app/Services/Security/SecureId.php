<?php

namespace App\Services\Security;

class SecureId
{
    private const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const MIN_LENGTH = 12;

    public static function encode(int $id, string $context = ''): string
    {
        if ($id <= 0) {
            return '';
        }

        $salt = self::getSalt($context);
        $checksum = substr(hash_hmac('sha256', (string) $id, $salt), 0, 4);

        $shuffled = self::shuffleAlphabet(self::ALPHABET, $salt);
        $base = strlen($shuffled);

        $num = $id;
        $encodedNum = '';
        while ($num > 0) {
            $rem = $num % $base;
            $encodedNum = $shuffled[$rem] . $encodedNum;
            $num = intdiv($num, $base);
        }

        $combined = $checksum . $encodedNum;

        if (strlen($combined) < self::MIN_LENGTH) {
            $padLength = self::MIN_LENGTH - strlen($combined);
            $padHash = substr(hash('sha256', $salt . $combined), 0, $padLength);
            $combined .= $padHash;
        }

        $token = self::permuteString($combined, $salt);

        return $token;
    }

    public static function decode(string $token, string $context = ''): ?int
    {
        if (empty($token) || strlen($token) < self::MIN_LENGTH) {
            return null;
        }

        $salt = self::getSalt($context);
        $unpermuted = self::unpermuteString($token, $salt);

        if (strlen($unpermuted) < 5) {
            return null;
        }

        $checksum = substr($unpermuted, 0, 4);
        $body = substr($unpermuted, 4);

        $shuffled = self::shuffleAlphabet(self::ALPHABET, $salt);
        $base = strlen($shuffled);

        $numStr = '';
        for ($i = 0; $i < strlen($body); $i++) {
            $char = $body[$i];
            if (strpos($shuffled, $char) === false) {
                break;
            }
            $numStr .= $char;
        }

        $id = 0;
        for ($i = 0; $i < strlen($numStr); $i++) {
            $pos = strpos($shuffled, $numStr[$i]);
            if ($pos === false) {
                return null;
            }
            $id = ($id * $base) + $pos;

            $expectedChecksum = substr(hash_hmac('sha256', (string) $id, $salt), 0, 4);
            if (hash_equals($checksum, $expectedChecksum)) {
                return $id;
            }
        }

        return null;
    }

    private static function getSalt(string $context): string
    {
        $appKey = config('app.key', 'BelanjaIn-Secure-Salt-Key-2026');
        $contextBasename = class_basename($context);
        return hash('sha256', $appKey . ':' . $contextBasename);
    }

    private static function shuffleAlphabet(string $alphabet, string $salt): string
    {
        $chars = str_split($alphabet);
        $len = count($chars);
        $saltHash = hash('sha256', $salt);
        $saltLen = strlen($saltHash);

        for ($i = $len - 1, $v = 0, $p = 0; $i > 0; $i--, $v++) {
            $v %= $saltLen;
            $p += ord($saltHash[$v]);
            $j = ($p + $v + ord($saltHash[$v])) % ($i + 1);

            $temp = $chars[$i];
            $chars[$i] = $chars[$j];
            $chars[$j] = $temp;
        }

        return implode('', $chars);
    }

    private static function permuteString(string $str, string $salt): string
    {
        $chars = str_split($str);
        $len = count($chars);
        $indices = range(0, $len - 1);
        $saltHash = hash('sha256', $salt . ':permute');
        $saltLen = strlen($saltHash);

        for ($i = $len - 1, $v = 0; $i > 0; $i--, $v++) {
            $v %= $saltLen;
            $j = ord($saltHash[$v]) % ($i + 1);

            $temp = $indices[$i];
            $indices[$i] = $indices[$j];
            $indices[$j] = $temp;
        }

        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= $chars[$indices[$i]];
        }

        return $result;
    }

    private static function unpermuteString(string $str, string $salt): string
    {
        $chars = str_split($str);
        $len = count($chars);
        $indices = range(0, $len - 1);
        $saltHash = hash('sha256', $salt . ':permute');
        $saltLen = strlen($saltHash);

        for ($i = $len - 1, $v = 0; $i > 0; $i--, $v++) {
            $v %= $saltLen;
            $j = ord($saltHash[$v]) % ($i + 1);

            $temp = $indices[$i];
            $indices[$i] = $indices[$j];
            $indices[$j] = $temp;
        }

        $resultArr = array_fill(0, $len, '');
        for ($i = 0; $i < $len; $i++) {
            $resultArr[$indices[$i]] = $chars[$i];
        }

        return implode('', $resultArr);
    }
}
