<?php

require_once __DIR__.'/../../../main/inc/global.inc.php';

class Crypt {

    /**
     * Generate token for the object
     *
     * @param array $object - object to signature
     *
     * @return string
     */
    public static function GetHash($object) {
        return \Firebase\JWT\JWT::encode($object, api_get_security_key());
    }

    /**
     * Create an object from the token
     *
     * @param string $token - token
     *
     * @return array
     */
    public static function ReadHash($token) {
        $result = null;
        $error = null;
        if ($token === null) {
            return [$result, "token is empty"];
        }
        try {
            $result = \Firebase\JWT\JWT::decode($token, api_get_security_key(), array("HS256"));
        } catch (\UnexpectedValueException $e) {
            $error = $e->getMessage();
        }
        return [$result, $error];
    }
}