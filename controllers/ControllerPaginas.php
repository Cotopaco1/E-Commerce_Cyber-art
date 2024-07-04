<?php

namespace Controller;

use Model\Productos;
use Model\Usuarios;
use MVC\Router;

class ControllerPaginas{

    public static function index(Router $router){
        iniciar_sesion_sino_esta_iniciada();
        $cuadros = Productos::all();
        $script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        

        $router->render('paginas/index', [
            'cuadros'=> $cuadros,
            'script' => $script
        ]);
    }
    public static function carrito_de_compras(Router $router){
        iniciar_sesion_sino_esta_iniciada();
        $script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        

        $router->render('paginas/carrito_de_compras', [
            'script'=>$script
        ]);

    }
}