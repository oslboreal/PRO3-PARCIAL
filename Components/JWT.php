<?php

use \Firebase\JWT\JWT;

require './vendor/autoload.php';

class Token
{
    private static $key = 'primerparcial';

    public static function getToken($id, $role = null)
    {

        $payload = array(
            'data' => [
                'id' => $id,
                'role' => $role
            ]
        );

        return JWT::encode($payload, Token::$key);
    }

    public static function getEmail()
    {
        try {
            $token = getallheaders()['token'] ?? '';
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return $decoded->data->id;
        } catch (Exception $e) {
            return '';
        }
    }

    public static function validarToken($token)
    {
        try {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function isInRole($token, $role)
    {
        try {
            $decoded = JWT::decode($token, Token::$key, array('HS256'));

            if ($decoded->data != null) {

                $currentRole = $decoded->data->role ?? '';

                if ($currentRole && !empty($currentRole)) {
                    return $currentRole == $role;;
                }
            }

            echo GenericResponse::obtain(false, 'Unauthorized.');
        } catch (Exception $e) {
            echo GenericResponse::obtain(false, 'Unauthorized.');
            die();
        }
    }

    public static function getHeader($KEY)
    {
        $headers = getallheaders();

        if ($headers != false) {
            if (isset($headers[$KEY]) && !empty($headers[$KEY])) {
                return $headers[$KEY];
            }
        }
        return null;
    }
}
