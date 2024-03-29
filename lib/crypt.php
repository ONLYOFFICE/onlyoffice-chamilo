<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2023
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

require_once __DIR__ . "/../../../main/inc/global.inc.php";

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Crypt {

    /**
     * Generate token for the object
     *
     * @param array $object - object to signature
     *
     * @return string
     */
    public static function GetHash($object)
    {
        return JWT::encode($object, api_get_security_key(), "HS256");
    }

    /**
     * Create an object from the token
     *
     * @param string $token - token
     *
     * @return array
     */
    public static function ReadHash($token)
    {
        $result = null;
        $error = null;
        if ($token === null) {
            return [$result, "token is empty"];
        }
        try {
            $result = JWT::decode($token, new Key(api_get_security_key(), "HS256"));
        } catch (\UnexpectedValueException $e) {
            $error = $e->getMessage();
        }
        return [$result, $error];
    }
}
