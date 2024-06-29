<?php 

namespace Controller;

use Model\Productos;

class ControllerCuadros{

    public static function getCuadros(){

        $cuadros = Productos::all();

        echo(json_encode($cuadros));
    }
}