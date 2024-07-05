<?php

namespace Controller;

use MVC\Router;

class ControllerLogin{
    public static function index(Router $router){

        $router->render('auth/index');
    }
    public static function crear_cuenta(Router $router){
        $script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        $router->render('auth/crear_cuenta', [
            'script'=> $script
        ]);
    }
    public static function recuperar_password(Router $router){

        $router->render('auth/recuperar_password');
    }

}