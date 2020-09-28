<?php

class AutoController
{
    public static function Create()
    {
        if (isset($_POST['patente']) && isset($_POST['tipo'])) {

            $tipo = $_POST['tipo'];
            $patente = $_POST['patente'];

            if ($tipo != 'hora' && $tipo != 'estadia' && $tipo != 'mensual') {
                echo GenericResponse::obtain(false, 'El tipo es invalido, se acepta: hora, estadia o mensual.', null);
                die();
            }



            $auto = new Auto($patente, $tipo, time(), Token::getEmail());
            $resultado = Auto::save($auto);
            echo GenericResponse::obtain($resultado, $resultado ? 'Auto almacenado' : 'El auto ya se encuentra registrado.', $resultado ? $auto : null);
        }
    }

    public static function GetByPatente($patente)
    {
        $autos = Auto::getByPatente($patente);
        echo GenericResponse::obtain(true, '', $autos);
    }

    public static function GetEstacionados()
    {
        $autos = Auto::getAll();
        echo GenericResponse::obtain(true, '', $autos);
    }

    public static function GetTotalByTipo($tipo)
    {
        $sum = Auto::sumAllByType($tipo);
        echo GenericResponse::obtain(true, '', $sum);
    }

    public static function Remove($patente)
    {
        $auto = Auto::getByPatente($patente);

        $genericObject = new stdClass();
        $genericObject->fecha_egreso = time();
        $horas = ($genericObject->fecha_egreso - $auto->date) / 3600;
        $precio = Precio::getOne();


        switch ($auto->tipo) {
            case 'hora':
                $genericObject->importe = $horas * $precio->precio_hora;
                break;
            case 'estadia':
                $genericObject->importe = $horas * $precio->precio_estadia;
                break;
            case 'mensual':
                $genericObject->importe = $horas * $precio->precio_mensual;
                break;
        }

        $auto->importe = $genericObject->importe;
        $auto->fecha_egreso = $genericObject->fecha_egreso;
        Auto::update($auto);

        echo GenericResponse::obtain(true, 'Auto retirado satisfactoriamente.', $genericObject);
    }
}
