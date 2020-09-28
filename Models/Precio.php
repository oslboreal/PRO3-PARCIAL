<?php

require_once "./Components/JsonHandler.php";

class Precio
{
    public $id;
    public $precio_hora;
    public $precio_estadia;
    public $precio_mensual;

    function __construct($precio_hora, $precio_estadia, $precio_mensual)
    {
        $this->precio_hora = $precio_hora;
        $this->precio_estadia = $precio_estadia;
        $this->precio_mensual = $precio_mensual;
    }

    public static function save($object)
    {
        $object->id = 0;
        return JsonHandler::saveUnique($object, 'Precios.json');
    }

    public static function getOne()
    {
        $archivoArray = (array) JsonHandler::readJson('Precios.json');
        $listaPrecios = [];

        foreach ($archivoArray as $datos) {
            $nuevoPrecio = new Precio($datos->precio_hora, $datos->precio_estadia, $datos->precio_mensual);
            $nuevoPrecio->id = $datos->id;
            return $nuevoPrecio ? $nuevoPrecio : null;
        }
    }

    /* Verifica si el precio existe */
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
