<?php

namespace Controller;

use MVC\Router;

class ControllerAdmin{

    public static function index(Router $router){

        
        $nombreAdmin = $_SESSION['nombre'];
        $router->render('admin/index', [
            'nombre'=>$nombreAdmin
        ], 'admin/layout');
    }
}