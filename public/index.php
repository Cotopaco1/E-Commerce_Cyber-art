<?php

require '../includes/app.php';

use Controller\ControllerApi;
use Controller\ControllerLogin;
use Controller\ControllerPaginas;
use MVC\Router;

$router = new Router();

//Paginas publics
$router->get('/', [ControllerPaginas::class, 'index']);
$router->get('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);
$router->post('/carrito_de_compras', [ControllerPaginas::class, 'carrito_de_compras']);

//Login
$router->get('/login', [ControllerLogin::class, 'index']);
$router->post('/login', [ControllerLogin::class, 'index']);
    //Crear cuenta
$router->get('/crear_cuenta', [ControllerLogin::class, 'crear_cuenta']);
$router->post('/crear_cuenta', [ControllerLogin::class, 'crear_cuenta']);
    //Recuperar Cuenta
$router->get('/recuperar_password', [ControllerLogin::class, 'recuperar_password']);
$router->post('/recuperar_password', [ControllerLogin::class, 'recuperar_password']);
    //Confirmar cuenta
$router->get('/confirmar_cuenta', [ControllerLogin::class, 'confirmar_cuenta']);
/* $router->post('/recuperar_password', [ControllerLogin::class, 'recuperar_password']); */
     
    //logOut
$router->get('/logout', [ControllerLogin::class, 'log_out']);


//Api...
    //carrito_de_compras
$router->get('/api/cuadros/getcuadros', [ControllerApi::class, 'getCuadros']);
$router->post('/api/cuadros/guardar_carrito_compras', [ControllerApi::class, 'guardar_carrito_de_compras_en_session']);
$router->get('/api/cuadros/guardar_carrito_compras', [ControllerApi::class, 'guardar_carrito_de_compras_en_session']);
$router->post('/api/validar_formulario', [ControllerApi::class, 'validarFormulario']);
$router->post('/api/crear_orden', [ControllerApi::class, 'crear_orden']);

    //Login
$router->post('/api/login/crear_cuenta', [ControllerApi::class, 'crear_cuenta']);
$router->post('/api/login/login', [ControllerApi::class, 'logIn_usuario']);


$router->comprobarRutas();