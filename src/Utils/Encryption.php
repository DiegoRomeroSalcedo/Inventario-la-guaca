<?php

namespace Proyecto\Utils;

class Encryption {
    private static $secretKey = 'q£!K;55\8^tZ>ZH8#l*e4H(4T8H<f)#';
    private static $iv = '<}-5?-hl98@uw,wyM.[4;4;7?H(8Ea';

    public static function encrypt($datos) {
        $iv = substr(hash('sha256', self::$iv), 0, 16);
        $jsonData = json_encode($datos);
        $encryptedData = openssl_encrypt($jsonData, 'aes-256-cbc', self::$secretKey, 0, $iv);
        return base64_encode($encryptedData);
    }

    public static function decrypt($datos) {
        $iv = substr(hash('sha256', self::$iv), 0, 16);
        $decodedData = base64_decode($datos);
        $jsonData = openssl_decrypt($decodedData, 'aes-256-cbc', self::$secretKey, 0, $iv);
        return json_decode($jsonData, true);
    }
}