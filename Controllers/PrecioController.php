<?php

class PrecioController
{
    public static function Create()
    {
        if (isset($_POST['precio_hora']) && isset($_POST['precio_estadia']) && isset($_POST['precio_mensual'])) {

            $precio_hora = $_POST['precio_hora'];
            $precio_estadia = $_POST['precio_estadia'];
            $precio_mensual = $_POST['precio_mensual'];

            $precio = new Precio($precio_hora, $precio_estadia, $precio_mensual);
            $resultado = Precio::save($precio);
            echo GenericResponse::obtain($resultado, $resultado ? 'Precio actualizado' : 'Precio no actualizado', $resultado ? $precio : null);
        }else{
            echo GenericResponse::obtain(false, 'Los parametros precio_hora, precio_estadia y precio_mensul son obligatorios.');
        }
    }

    public static function GetAll()
    {
        $precios = Precio::getAll();
        echo GenericResponse::obtain(true, '', $precios);
    }
}
