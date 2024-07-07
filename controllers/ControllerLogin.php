<?php

namespace Controller;

use Model\Usuarios;
use MVC\Router;

class ControllerLogin{
    public static function index(Router $router){
        $auth = $_SESSION['login'] ?? false;
        if($auth){
            header('location: /');
        }
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
    public static function confirmar_cuenta(Router $router){

        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $token = $_GET['token'] ?? NULL;
            $alertas = Usuarios::confirmarUsuario_con_sentencia_preparada($token);
            
        }
        

        $router->render('auth/confirmar_cuenta',[
            'alertas'=>$alertas
        ]);
        
    }
    public static function log_out(){
        iniciar_sesion_sino_esta_iniciada();
        $_SESSION = array();
        session_destroy();

        header('location: /login');
        exit;
    }

}