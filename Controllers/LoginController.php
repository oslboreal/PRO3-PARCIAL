<?php

class LoginController
{
    public static function Login()
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $pass = PassManager::Hash($_POST['password']);

            // Look for credentials.
            if (Usuario::checkCredentials($email, $pass)) {
                $role =  Usuario::getRole($email);
                // Create token.
                $token = Token::getToken($email, $role);

                // Realiza autenticaciónn.
                echo GenericResponse::obtain(true, 'Bienvenido ' . $role, $token);
            } else {
                echo GenericResponse::obtain(false, 'Credenciales invalidas.');
            }
        } else {
            echo GenericResponse::obtain(false, 'Debe especificar el campo email y password.');
        }
    }

    public static function Validate()
    {
        $token = getallheaders()['token'] ?? '';

        if (!empty($token)) {
            $isDecoded = Token::validarToken($token);

            // Realiza autenticaciónn.
            echo GenericResponse::obtain($isDecoded, $isDecoded ? 'Token valido.' : 'Token ivalido', $token);
        } else {
            echo GenericResponse::obtain(false, 'Invalid credentials');
        }
    }

    public static function IsInRole($role)
    {
        $token = getallheaders()['token'] ?? '';

        if (!empty($role)) {
            if (!empty($token) && $token != '{{token}}') {
                $isInRole = Token::isInRole($token, $role);

                return $isInRole;
            } else {
                echo GenericResponse::obtain(false, 'Invalid token');
            }
        } else {
            echo GenericResponse::obtain(false, 'Role is mandatory.');
        }
    }
 // todo borrar
    public static function requireAuthorization($role = '')
    {
        $token = getallheaders()['token'] ?? '';

        if (!empty($token)) {
            $isDecoded = Token::validarToken($token);

            if ($isDecoded) {

                // Debe poseer rol
                if (!empty($role)) {
                    if (isset($_GET['role'])) {
                        $role = $_GET['role'];

                        $isInRole = Token::isInRole($token, $role);

                        if (!$isInRole) {
                            echo GenericResponse::obtain($isDecoded, 'El rol no se encuentra autorizado.', $token);
                            return false;
                        }

                        return true;
                    } else {
                        echo GenericResponse::obtain($isDecoded, 'Se esperaba un rol.', $token);
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                echo GenericResponse::obtain(false, 'Token invalido.');
                return false;
            }
        } else {
            echo GenericResponse::obtain(false, 'Se esperaba un token.');
            return false;
        }
    }
    
}
