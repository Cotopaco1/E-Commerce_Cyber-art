<?php

namespace Controller;

use Model\Productos;

use MVC\Router;

class ControllerPaginas{

    public static function index(Router $router){
        $cuadros = Productos::all();
        
        $router->render('paginas/index', [
            'cuadros'=> $cuadros
        ]);
    }
}