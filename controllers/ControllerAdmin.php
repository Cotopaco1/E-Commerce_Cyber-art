<?php

namespace Controller;

use MVC\Router;

class ControllerAdmin{

    public static function index(Router $router){
        if(!isset($_SESSION['admin']) || !$_SESSION['admin']){
            header('location: /');
            return;
        }
        $nombreAdmin = $_SESSION['nombre'];
        $router->render('admin/index', [
            'nombre'=>$nombreAdmin
        ], 'admin/layout');
    }
}