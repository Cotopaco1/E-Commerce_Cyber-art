<?php

namespace Controller;

use Model\Productos;
use Model\Usuarios;
use MVC\Router;

class ControllerPaginas{

    public static function index(Router $router){
        $script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        $router->render('paginas/index', [
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
    public static function producto(Router $router){
        $id = is_numeric($_GET['id']);
        if(!$id){
            header('Location: /');
            return;
        }
       
        $router->render('paginas/producto');
    }
    public static function productos(Router $router){
        $disponible = [
            1=>'Disponible',
            0=>'No disponible'
        ];
        $script = '<script src="./build/js/productos.js"></script>';
        $productos = Productos::all_no_deleted_and_disponibles();
        $router->render('paginas/productos',[
            'disponible'=>$disponible,
            'productos'=>$productos,
            'script'=> $script
        ]);
    }
}