<?php

require_once "./Components/JsonHandler.php";

class Usuario
{
    public $id;
    public $email;
    public $pass;
    public $tipo;

    function __construct($email = '', $pass = '', $tipo	= 'Usuario')
    {
        $this->email = $email;
        $this->pass = $pass;
        $this->tipo = $tipo;
    }

    /* Guarda un nuevo usuario */
    public static function guardarUsuario($object)
    {
        $object->id = Usuario::getNextUserId();
        return JsonHandler::saveJson($object, 'Users.json');
    }

    /* Trae los usuarios existentes */
    public static function getAll()
    {
        $archivoArray = (array) JsonHandler::readJson('Users.json');
        $listaUsuarios = [];

        foreach ($archivoArray as $datos) {
            $nuevoUsuario = new Usuario($datos->email, $datos->pass, $datos->tipo);
            $nuevoUsuario->id = $datos->id;
            array_push($listaUsuarios, $nuevoUsuario);
        }

        return $listaUsuarios;
    }

    public static function getRole($email)
    {
        $archivoArray = (array) JsonHandler::readJson('Users.json');
        $listaUsuarios = [];

        foreach ($archivoArray as $datos) {
            $nuevoUsuario = new Usuario($datos->email, $datos->pass, $datos->tipo);
            $nuevoUsuario->id = $datos->id;
            
            if($nuevoUsuario->email == $email){
                return $nuevoUsuario->tipo;
            }
        }

        // Default value.
        return 'usuario';
    }

    /* Verifica si el usuario existe */
    public static function exists($id)
    {
        $archivoArray = (array) JsonHandler::readJson('Users.json');
        $listaUsuarios = [];

        foreach ($archivoArray as $datos) {
            $nuevoUsuario = new Usuario($datos->email, $datos->pass, $datos->tipo);
            $nuevoUsuario->id = $datos->id;
            if ($nuevoUsuario->id == $id) {
                return true;
            }
        }

        return false;
    }

    public static function existsByEmail($email)
    {
        $archivoArray = (array) JsonHandler::readJson('Users.json');
        $listaUsuarios = [];

        foreach ($archivoArray as $datos) {
            $nuevoUsuario = new Usuario($datos->email, $datos->pass, $datos->tipo);
            $nuevoUsuario->id = $datos->id;
            if ($nuevoUsuario->email == $email) {
                return true;
            }
        }

        return false;
    }

    /* Verifica las credenciales de un usuario */
    public static function checkCredentials($email, $pass)
    {
        try {
            $usuarios = Usuario::getAll();

            foreach ($usuarios as $usuario) {
                if ($usuario->email == $email && $usuario->pass == $pass)
                    return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /* Trae el próximo identificador */
    public static function getNextUserId()
    {
        $users = Usuario::getAll();
        $lastId = -1;

        foreach ($users as $user) {

            if ($user->id != null && !empty($user->id)) {
                if ($user->id >= $lastId) {
                    $lastId = $user->id;
                }
            }
        }

        if ($lastId == -1)
            $lastId = 0;

        return $lastId + 1;
    }

    /* Métodos mágicos */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
